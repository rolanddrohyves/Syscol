{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SYSCOL') - Gestion Scolaire</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Alpine.js pour les interactions -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        
        .sidebar-transition {
            transition: all 0.3s ease;
        }
        
        .hover-scale:hover {
            transform: scale(1.02);
            transition: all 0.2s;
        }
        
        /* Scrollbar personnalisée */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        
        /* Animations */
        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .notification-enter {
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.7;
                transform: scale(1.05);
            }
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-50">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 bg-gradient-to-b from-indigo-900 to-purple-900 text-white shadow-xl sidebar-transition flex-shrink-0">
            <!-- Logo et titre -->
            <div class="p-6 border-b border-white/10">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center">
                        <i class="fas fa-graduation-cap text-2xl text-white"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold tracking-wider">SYSCOL</h1>
                        <p class="text-xs text-indigo-200 mt-0.5">{{ Auth::user()->role->display_name ?? 'Utilisateur' }}</p>
                    </div>
                </div>
            </div>
            
            <!-- Navigation -->
            <nav class="mt-6 overflow-y-auto max-h-[calc(100vh-140px)] px-2">
                @include('layouts.navigation')
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden bg-gray-50">
            <!-- Top Navigation -->
            <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-30">
                <div class="px-6 py-3 flex justify-between items-center">
                    <!-- Menu toggle pour mobile -->
                    <div class="flex items-center">
                        <button class="text-gray-500 focus:outline-none lg:hidden hover:text-indigo-600 transition-all hover:scale-110" id="sidebarToggle">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        <h2 class="text-xl font-semibold text-gray-800 ml-4 fade-in">
                            @yield('page-title', 'Tableau de bord')
                        </h2>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <!-- Raccourcis clavier -->
                        <div class="hidden md:flex items-center space-x-1 text-xs bg-gray-100 px-2 py-1 rounded-lg text-gray-600">
                            <i class="far fa-keyboard mr-1"></i>
                            <span>Ctrl+K</span>
                        </div>
                        
                        <!-- Menu Actions Rapides avec Alpine.js -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center space-x-2 bg-indigo-600 text-white px-4 py-2 rounded-xl hover:bg-indigo-700 transition-all shadow-md hover:shadow-lg hover:scale-105 transform">
                                <i class="fas fa-bolt"></i>
                                <span class="hidden md:inline">Actions rapides</span>
                                <i class="fas fa-chevron-down text-xs transition-transform" :class="{ 'rotate-180': open }"></i>
                            </button>
                            
                            <div x-show="open" 
                                 @click.away="open = false"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 class="absolute right-0 mt-2 w-72 bg-white rounded-xl shadow-2xl py-2 z-50 border border-gray-200">
                                
                                <div class="px-4 py-3 border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-t-xl">
                                    <h3 class="font-semibold text-gray-800">Que voulez-vous faire ?</h3>
                                    <p class="text-xs text-gray-500 mt-1">Actions rapides (Ctrl + raccourci)</p>
                                </div>
                                
                                <a href="{{ route('etablissement.notes.create') }}" class="flex items-center px-4 py-3 hover:bg-purple-50 transition-colors group">
                                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform">
                                        <i class="fas fa-star text-purple-600"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-800">Saisir une note</p>
                                        <p class="text-xs text-gray-500">Ctrl + N</p>
                                    </div>
                                    <i class="fas fa-arrow-right text-gray-400 group-hover:text-purple-600"></i>
                                </a>
                                
                                <a href="{{ route('etablissement.absences.create') }}" class="flex items-center px-4 py-3 hover:bg-red-50 transition-colors group">
                                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform">
                                        <i class="fas fa-calendar-times text-red-600"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-800">Signaler absence</p>
                                        <p class="text-xs text-gray-500">Ctrl + A</p>
                                    </div>
                                    <i class="fas fa-arrow-right text-gray-400 group-hover:text-red-600"></i>
                                </a>
                                
                                <a href="{{ route('etablissement.eleves.create') }}" class="flex items-center px-4 py-3 hover:bg-green-50 transition-colors group">
                                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform">
                                        <i class="fas fa-user-plus text-green-600"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-800">Nouvel élève</p>
                                        <p class="text-xs text-gray-500">Ctrl + E</p>
                                    </div>
                                    <i class="fas fa-arrow-right text-gray-400 group-hover:text-green-600"></i>
                                </a>
                                
                                <a href="{{ route('etablissement.classes.create') }}" class="flex items-center px-4 py-3 hover:bg-blue-50 transition-colors group">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform">
                                        <i class="fas fa-door-open text-blue-600"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-800">Nouvelle classe</p>
                                        <p class="text-xs text-gray-500">Ctrl + C</p>
                                    </div>
                                    <i class="fas fa-arrow-right text-gray-400 group-hover:text-blue-600"></i>
                                </a>
                                
                                <div class="border-t border-gray-100 my-2"></div>
                                
                                <a href="{{ route('etablissement.notes.index') }}" class="flex items-center px-4 py-3 hover:bg-gray-50 transition-colors">
                                    <i class="fas fa-list w-5 text-gray-500 mr-3"></i>
                                    <span class="text-sm text-gray-700">Voir toutes les notes</span>
                                </a>
                                <a href="{{ route('etablissement.absences.index') }}" class="flex items-center px-4 py-3 hover:bg-gray-50 transition-colors">
                                    <i class="fas fa-calendar-alt w-5 text-gray-500 mr-3"></i>
                                    <span class="text-sm text-gray-700">Voir les absences</span>
                                </a>
                            </div>
                        </div>
                        
                        <!-- Notifications avec compteur dynamique -->
                        <div class="relative" x-data="{ 
                            open: false, 
                            notifications: [],
                            unreadCount: 0,
                            loading: true,
                            
                            init() {
                                this.loadNotifications();
                                setInterval(() => this.loadNotifications(), 60000);
                            },
                            
                            loadNotifications() {
                                this.loading = true;
                                fetch('/api/notifications')
                                    .then(response => response.json())
                                    .then(data => {
                                        this.notifications = data;
                                        this.unreadCount = data.filter(n => !n.read).length;
                                    })
                                    .catch(error => {
                                        console.error('Erreur chargement notifications:', error);
                                    })
                                    .finally(() => {
                                        this.loading = false;
                                    });
                            },
                            
                            markAsRead(id) {
                                fetch(`/api/notifications/${id}/read`, { method: 'POST' })
                                    .then(() => this.loadNotifications());
                            }
                        }">
                            <button @click="open = !open" class="relative text-gray-600 hover:text-indigo-600 transition p-2 rounded-lg hover:bg-gray-100">
                                <i class="fas fa-bell text-xl"></i>
                                <span x-show="unreadCount > 0" 
                                      x-text="unreadCount"
                                      class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center pulse">
                                </span>
                            </button>
                            
                            <div x-show="open" 
                                 @click.away="open = false" 
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-2xl py-2 z-50 border border-gray-200">
                                
                                <div class="px-4 py-3 border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-t-xl">
                                    <div class="flex items-center justify-between">
                                        <h3 class="font-semibold text-gray-800">Notifications</h3>
                                        <span x-show="unreadCount > 0" 
                                              x-text="unreadCount + ' nouvelle(s)'"
                                              class="text-xs bg-indigo-600 text-white px-2 py-1 rounded-full">
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="max-h-96 overflow-y-auto">
                                    <template x-if="loading">
                                        <div class="p-4 text-center">
                                            <i class="fas fa-spinner fa-spin text-indigo-600 text-xl"></i>
                                            <p class="text-sm text-gray-500 mt-2">Chargement...</p>
                                        </div>
                                    </template>
                                    
                                    <template x-if="!loading && notifications.length === 0">
                                        <div class="p-8 text-center">
                                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                                <i class="fas fa-bell-slash text-2xl text-gray-400"></i>
                                            </div>
                                            <p class="text-gray-500 text-sm">Aucune notification</p>
                                            <p class="text-xs text-gray-400 mt-1">Vous serez notifié des événements importants</p>
                                        </div>
                                    </template>
                                    
                                    <template x-for="notification in notifications" :key="notification.id">
                                        <a href="#" 
                                           @click.prevent="markAsRead(notification.id)"
                                           class="block px-4 py-3 hover:bg-gray-50 transition-colors border-b border-gray-100 last:border-b-0"
                                           :class="{ 'bg-indigo-50': !notification.read }">
                                            <div class="flex items-start space-x-3">
                                                <div class="flex-shrink-0">
                                                    <div class="w-8 h-8 rounded-full"
                                                         :class="{
                                                             'bg-red-100': notification.type === 'absence',
                                                             'bg-yellow-100': notification.type === 'retard',
                                                             'bg-green-100': notification.type === 'presence',
                                                             'bg-purple-100': notification.type === 'sanction',
                                                             'bg-blue-100': notification.type === 'note'
                                                         }">
                                                        <i class="fas" 
                                                           :class="{
                                                               'fa-calendar-times text-red-600': notification.type === 'absence',
                                                               'fa-clock text-yellow-600': notification.type === 'retard',
                                                               'fa-check-circle text-green-600': notification.type === 'presence',
                                                               'fa-gavel text-purple-600': notification.type === 'sanction',
                                                               'fa-star text-blue-600': notification.type === 'note'
                                                           }"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-1">
                                                    <p class="text-sm text-gray-800" x-text="notification.message"></p>
                                                    <p class="text-xs text-gray-500 mt-1" x-text="notification.time"></p>
                                                </div>
                                                <span x-show="!notification.read" class="w-2 h-2 bg-indigo-600 rounded-full flex-shrink-0"></span>
                                            </div>
                                        </a>
                                    </template>
                                </div>
                                
                                <div class="border-t border-gray-100 px-4 py-2">
                                    <a href="#" class="text-xs text-indigo-600 hover:text-indigo-800 flex items-center justify-center">
                                        Voir toutes les notifications
                                        <i class="fas fa-arrow-right ml-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- User Menu -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center space-x-3 focus:outline-none group">
                                <div class="w-9 h-9 rounded-full bg-gradient-to-r from-indigo-500 to-purple-500 flex items-center justify-center text-white font-semibold shadow-md group-hover:scale-110 transition-transform">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                                <div class="hidden md:block text-left">
                                    <p class="text-sm font-medium text-gray-700">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-gray-500">{{ Auth::user()->role->display_name ?? '' }}</p>
                                </div>
                                <i class="fas fa-chevron-down text-xs text-gray-500 transition-transform" :class="{ 'rotate-180': open }"></i>
                            </button>
                            
                            <div x-show="open" 
                                 @click.away="open = false" 
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-2xl py-2 z-50 border border-gray-200">
                                
                                <div class="px-4 py-3 border-b border-gray-100">
                                    <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                                </div>
                                
                                <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 transition-colors">
                                    <i class="fas fa-user mr-3 w-4 text-gray-500"></i> Mon profil
                                </a>
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 transition-colors">
                                    <i class="fas fa-cog mr-3 w-4 text-gray-500"></i> Paramètres
                                </a>
                                <hr class="my-2 border-gray-100">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                        <i class="fas fa-sign-out-alt mr-3 w-4"></i> Déconnexion
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto bg-gray-50 p-6">
                <!-- Messages de succès -->
                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg shadow-md flex items-center justify-between notification-enter"
                         x-data="{ show: true }"
                         x-show="show"
                         x-init="setTimeout(() => show = false, 5000)">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle mr-2 text-green-600"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                        <button @click="show = false" class="text-green-600 hover:text-green-800">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif

                <!-- Messages d'erreur -->
                @if(session('error'))
                    <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg shadow-md flex items-center justify-between notification-enter"
                         x-data="{ show: true }"
                         x-show="show"
                         x-init="setTimeout(() => show = false, 5000)">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle mr-2 text-red-600"></i>
                            <span>{{ session('error') }}</span>
                        </div>
                        <button @click="show = false" class="text-red-600 hover:text-red-800">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif

                <!-- Messages de warning -->
                @if(session('warning'))
                    <div class="mb-6 p-4 bg-yellow-100 border border-yellow-400 text-yellow-700 rounded-lg shadow-md flex items-center justify-between notification-enter"
                         x-data="{ show: true }"
                         x-show="show"
                         x-init="setTimeout(() => show = false, 5000)">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle mr-2 text-yellow-600"></i>
                            <span>{{ session('warning') }}</span>
                        </div>
                        <button @click="show = false" class="text-yellow-600 hover:text-yellow-800">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif

                <!-- Messages d'information -->
                @if(session('info'))
                    <div class="mb-6 p-4 bg-blue-100 border border-blue-400 text-blue-700 rounded-lg shadow-md flex items-center justify-between notification-enter"
                         x-data="{ show: true }"
                         x-show="show"
                         x-init="setTimeout(() => show = false, 5000)">
                        <div class="flex items-center">
                            <i class="fas fa-info-circle mr-2 text-blue-600"></i>
                            <span>{{ session('info') }}</span>
                        </div>
                        <button @click="show = false" class="text-blue-600 hover:text-blue-800">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif

                <!-- Erreurs de validation -->
                @if($errors->any())
                    <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg shadow-md notification-enter">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-exclamation-circle mr-2 text-red-600"></i>
                            <span class="font-semibold">Erreurs de validation :</span>
                        </div>
                        <ul class="list-disc list-inside text-sm space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Raccourcis clavier -->
    <script>
        document.addEventListener('keydown', function(e) {
            // Ctrl+K pour focus recherche
            if (e.ctrlKey && e.key === 'k') {
                e.preventDefault();
                document.querySelector('input[type="search"]')?.focus();
            }
            
            // Ctrl+N pour nouvelle note
            if (e.ctrlKey && e.key === 'n') {
                e.preventDefault();
                window.location.href = "{{ route('etablissement.notes.create') }}";
            }
            
            // Ctrl+A pour absence
            if (e.ctrlKey && e.key === 'a') {
                e.preventDefault();
                window.location.href = "{{ route('etablissement.absences.create') }}";
            }
            
            // Ctrl+E pour élève
            if (e.ctrlKey && e.key === 'e') {
                e.preventDefault();
                window.location.href = "{{ route('etablissement.eleves.create') }}";
            }
            
            // Ctrl+C pour classe
            if (e.ctrlKey && e.key === 'c') {
                e.preventDefault();
                window.location.href = "{{ route('etablissement.classes.create') }}";
            }
            
            // ? pour afficher l'aide
            if (e.key === '?' || (e.shiftKey && e.key === '?')) {
                e.preventDefault();
                alert('🔍 Raccourcis disponibles:\n\n' +
                      'Ctrl+N: Nouvelle note\n' +
                      'Ctrl+A: Signaler absence\n' +
                      'Ctrl+E: Nouvel élève\n' +
                      'Ctrl+C: Nouvelle classe\n' +
                      'Ctrl+K: Recherche\n' +
                      '?: Afficher cette aide');
            }
        });

        // Toggle sidebar sur mobile
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.querySelector('aside').classList.toggle('-translate-x-full');
        });

        // Détection de la connexion internet
        window.addEventListener('online', function() {
            alert('Connexion rétablie');
        });

        window.addEventListener('offline', function() {
            alert('Connexion internet perdue');
        });
    </script>

    @stack('scripts')
</body>
</html>