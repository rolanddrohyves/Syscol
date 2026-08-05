<?php
// app/helpers.php

if (!function_exists('activity')) {
    /**
     * Crée un nouvel enregistrement d'activité
     *
     * @param string|null $description
     * @return \App\Models\ActivityLog
     */
    function activity($description = null)
    {
        try {
            $log = new \App\Models\ActivityLog();
            
            if (auth()->check()) {
                $log->user_id = auth()->id();
            }
            
            if ($description) {
                $log->description = $description;
            }
            
            // Ajouter les informations de la requête si disponible
            if (function_exists('request') && request()) {
                $log->ip_address = request()->ip();
                $log->user_agent = request()->userAgent();
            }
            
            return $log;
        } catch (\Exception $e) {
            // En cas d'erreur, retourner un objet stdClass pour éviter les crashes
            return new stdClass();
        }
    }
}

if (!function_exists('format_telephone')) {
    /**
     * Formate un numéro de téléphone au format ivoirien
     *
     * @param string $numero
     * @return string
     */
    function format_telephone($numero)
    {
        $numero = preg_replace('/[^0-9]/', '', $numero);
        
        if (strlen($numero) == 8) {
            return substr($numero, 0, 2) . ' ' . 
                   substr($numero, 2, 2) . ' ' . 
                   substr($numero, 4, 2) . ' ' . 
                   substr($numero, 6, 2);
        }
        
        if (strlen($numero) == 10) {
            return '+' . substr($numero, 0, 3) . ' ' . 
                   substr($numero, 3, 2) . ' ' . 
                   substr($numero, 5, 2) . ' ' . 
                   substr($numero, 7, 2) . ' ' . 
                   substr($numero, 9, 2);
        }
        
        return $numero;
    }
}

if (!function_exists('format_fcfa')) {
    /**
     * Formate un montant en Franc CFA
     *
     * @param int|float $montant
     * @return string
     */
    function format_fcfa($montant)
    {
        return number_format($montant, 0, ',', ' ') . ' FCFA';
    }
}

if (!function_exists('get_current_annee_scolaire')) {
    /**
     * Récupère l'année scolaire en cours
     *
     * @return \App\Models\AnneeScolaire|null
     */
    function get_current_annee_scolaire()
    {
        return \App\Models\AnneeScolaire::where('is_current', true)->first();
    }
}

if (!function_exists('get_etablissement_user')) {
    /**
     * Récupère l'établissement de l'utilisateur connecté
     *
     * @return \App\Models\Etablissement|null
     */
    function get_etablissement_user()
    {
        if (auth()->check() && auth()->user()->etablissement) {
            return auth()->user()->etablissement;
        }
        return null;
    }
}

if (!function_exists('calculate_age')) {
    /**
     * Calcule l'âge à partir d'une date de naissance
     *
     * @param string|Carbon $dateNaissance
     * @return int
     */
    function calculate_age($dateNaissance)
    {
        return \Carbon\Carbon::parse($dateNaissance)->age;
    }
}