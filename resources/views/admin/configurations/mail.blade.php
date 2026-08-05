{{-- resources/views/admin/configurations/mail.blade.php --}}
@extends('layouts.app')

@section('title', 'Configuration email - SYSCOL')
@section('page-title', 'Configuration email')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm p-8">
        <!-- En-tête -->
        <div class="flex items-center space-x-4 mb-8 pb-6 border-b border-gray-100">
            <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-envelope text-white text-2xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Configuration email</h2>
                <p class="text-gray-500">Paramètres SMTP pour les notifications</p>
            </div>
        </div>

        <form action="{{ route('admin.configurations.mail') }}" method="POST" class="space-y-8">
            @csrf

            <!-- Test de connexion -->
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-info-circle text-blue-600 mr-3 text-xl"></i>
                        <p class="text-sm text-blue-800">Testez votre configuration avant de sauvegarder</p>
                    </div>
                    <button type="button" onclick="testMail()" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Tester
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Mailer -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">
                        Mailer
                    </label>
                    <select name="mail_mailer" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="smtp" {{ $configs['mail_mailer'] == 'smtp' ? 'selected' : '' }}>SMTP</option>
                        <option value="sendmail" {{ $configs['mail_mailer'] == 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                        <option value="mailgun" {{ $configs['mail_mailer'] == 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                        <option value="ses" {{ $configs['mail_mailer'] == 'ses' ? 'selected' : '' }}>Amazon SES</option>
                        <option value="postmark" {{ $configs['mail_mailer'] == 'postmark' ? 'selected' : '' }}>Postmark</option>
                        <option value="log" {{ $configs['mail_mailer'] == 'log' ? 'selected' : '' }}>Log (test)</option>
                    </select>
                </div>

                <!-- Hôte -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">
                        Hôte SMTP
                    </label>
                    <input type="text" 
                           name="mail_host" 
                           value="{{ $configs['mail_host'] }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Port -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">
                        Port
                    </label>
                    <input type="number" 
                           name="mail_port" 
                           value="{{ $configs['mail_port'] }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Chiffrement -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">
                        Chiffrement
                    </label>
                    <select name="mail_encryption" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="tls" {{ $configs['mail_encryption'] == 'tls' ? 'selected' : '' }}>TLS</option>
                        <option value="ssl" {{ $configs['mail_encryption'] == 'ssl' ? 'selected' : '' }}>SSL</option>
                        <option value="null" {{ $configs['mail_encryption'] == 'null' ? 'selected' : '' }}>Aucun</option>
                    </select>
                </div>

                <!-- Utilisateur -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">
                        Utilisateur
                    </label>
                    <input type="text" 
                           name="mail_username" 
                           value="{{ $configs['mail_username'] }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Mot de passe -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">
                        Mot de passe
                    </label>
                    <input type="password" 
                           name="mail_password" 
                           value="{{ $configs['mail_password'] }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Email expéditeur -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">
                        Email expéditeur
                    </label>
                    <input type="email" 
                           name="mail_from_address" 
                           value="{{ $configs['mail_from_address'] }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Nom expéditeur -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">
                        Nom expéditeur
                    </label>
                    <input type="text" 
                           name="mail_from_name" 
                           value="{{ $configs['mail_from_name'] }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.configurations.index') }}" 
                   class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all">
                    Annuler
                </a>
                <button type="submit" 
                        class="px-6 py-3 bg-gradient-to-r from-blue-600 to-cyan-600 text-white rounded-xl hover:from-blue-700 hover:to-cyan-700 transition-all shadow-lg">
                    <i class="fas fa-save mr-2"></i>
                    Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function testMail() {
    fetch('/admin/configurations/mail/test', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            to: '{{ Auth::user()->email }}'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ Email de test envoyé avec succès !');
        } else {
            alert('❌ Erreur : ' + data.message);
        }
    });
}
</script>
@endsection