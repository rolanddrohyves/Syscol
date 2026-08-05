<?php
// app/Http/Controllers/Admin/LogController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class LogController extends Controller
{
    /**
     * Affiche les journaux d'activité
     */
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')->orderBy('created_at', 'desc');
        
        // Filtres
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', Carbon::parse($request->date_from));
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', Carbon::parse($request->date_to));
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }
        
        $logs = $query->paginate(50);
        
        // Données pour les filtres
        $users = User::orderBy('name')->get(['id', 'name', 'email']);
        $actions = ActivityLog::select('action')->distinct()->pluck('action');
        
        // Statistiques
        $stats = [
            'total' => ActivityLog::count(),
            'today' => ActivityLog::whereDate('created_at', today())->count(),
            'week' => ActivityLog::where('created_at', '>=', now()->subDays(7))->count(),
            'month' => ActivityLog::where('created_at', '>=', now()->subMonth())->count(),
        ];
        
        return view('admin.logs.index', compact('logs', 'users', 'actions', 'stats'));
    }

    /**
     * Affiche les détails d'un log
     */
    public function show($id)
    {
        $log = ActivityLog::with('user')->findOrFail($id);
        
        return view('admin.logs.show', compact('log'));
    }

    /**
     * Supprime un log
     */
    public function destroy($id)
    {
        $log = ActivityLog::findOrFail($id);
        $log->delete();
        
        return redirect()->route('admin.logs')
            ->with('success', 'Journal supprimé avec succès.');
    }

    /**
     * Vide tous les logs
     */
    public function clear()
    {
        ActivityLog::truncate();
        
        // Logger cette action
        $this->log('clear', 'Tous les journaux ont été vidés');
        
        return redirect()->route('admin.logs')
            ->with('success', 'Tous les journaux ont été vidés.');
    }

    /**
     * Export des logs
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');
        
        $query = ActivityLog::with('user')->orderBy('created_at', 'desc');
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $logs = $query->get();
        
        switch ($format) {
            case 'csv':
                return $this->exportCsv($logs);
            case 'json':
                return $this->exportJson($logs);
            default:
                return redirect()->back()->with('error', 'Format non supporté');
        }
    }

    /**
     * Export CSV
     */
    private function exportCsv($logs)
    {
        $filename = 'logs-' . now()->format('Y-m-d') . '.csv';
        $handle = fopen('php://output', 'w');
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        // En-têtes
        fputcsv($handle, ['Date', 'Utilisateur', 'Action', 'Description', 'IP', 'Modèle']);
        
        // Données
        foreach ($logs as $log) {
            fputcsv($handle, [
                $log->created_at->format('d/m/Y H:i:s'),
                $log->user->name ?? 'Système',
                $log->action,
                $log->description,
                $log->ip_address,
                $log->model_type ? class_basename($log->model_type) . ' #' . $log->model_id : '-',
            ]);
        }
        
        fclose($handle);
        exit;
    }

    /**
     * Export JSON
     */
    private function exportJson($logs)
    {
        $data = $logs->map(function($log) {
            return [
                'id' => $log->id,
                'date' => $log->created_at->toIso8601String(),
                'user' => $log->user ? $log->user->name : 'Système',
                'user_email' => $log->user ? $log->user->email : null,
                'action' => $log->action,
                'description' => $log->description,
                'ip' => $log->ip_address,
                'model' => $log->model_type ? class_basename($log->model_type) : null,
                'model_id' => $log->model_id,
            ];
        });
        
        return response()->json($data)
            ->header('Content-Disposition', 'attachment; filename="logs-' . now()->format('Y-m-d') . '.json"');
    }

    /**
     * Graphiques des activités
     */
    public function chartData(Request $request)
    {
        $days = $request->get('days', 7);
        
        $data = [];
        $labels = [];
        
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('d/m');
            
            $count = ActivityLog::whereDate('created_at', $date)->count();
            $data[] = $count;
        }
        
        // Répartition par action
        $actions = ActivityLog::select('action', DB::raw('count(*) as total'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('action')
            ->get();
        
        return response()->json([
            'timeline' => [
                'labels' => $labels,
                'data' => $data,
            ],
            'actions' => $actions,
        ]);
    }

    /**
     * Journalisation (méthode utilitaire)
     */
    public static function log($action, $description, $model = null, $oldValues = null, $newValues = null)
    {
        try {
            $log = new ActivityLog();
            $log->user_id = auth()->id();
            $log->action = $action;
            $log->description = $description;
            $log->ip_address = request()->ip();
            $log->user_agent = request()->userAgent();
            
            if ($model) {
                $log->model_type = get_class($model);
                $log->model_id = $model->id;
            }
            
            if ($oldValues) {
                $log->old_values = $oldValues;
            }
            
            if ($newValues) {
                $log->new_values = $newValues;
            }
            
            $log->save();
            
            return $log;
        } catch (\Exception $e) {
            // Silently fail - ne pas bloquer l'application si le logging échoue
            return null;
        }
    }

    /**
     * Nettoyage automatique des vieux logs
     */
    public function cleanOldLogs($days = 90)
    {
        $deleted = ActivityLog::where('created_at', '<', now()->subDays($days))->delete();
        
        return redirect()->route('admin.logs')
            ->with('success', "{$deleted} anciens logs supprimés.");
    }
}