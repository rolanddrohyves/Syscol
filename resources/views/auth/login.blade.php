<!-- resources/views/auth/login.blade.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Connexion - SYSCOL</title>
    <style>
        body {
            background: linear-gradient(165deg, #1a1c2c 0%, #2a3f54 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            position: relative;
            overflow: hidden;
        }
        
        /* Motif élégant en arrière-plan */
        body::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background-image: radial-gradient(rgba(255, 255, 255, 0.05) 1px, transparent 1px);
            background-size: 30px 30px;
            pointer-events: none;
        }
        
        /* Dégradé subtil animé */
        body::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 20% 50%, rgba(108, 92, 231, 0.1) 0%, transparent 50%);
            pointer-events: none;
        }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            position: relative;
            z-index: 10;
        }
        
        .input-field {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }
        
        .input-field:focus {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.3);
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.05);
        }
        
        .input-field::placeholder {
            color: rgba(255, 255, 255, 0.3);
            font-weight: 300;
        }
        
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 25px -5px rgba(102, 126, 234, 0.5);
        }
        
        .btn-login:active {
            transform: translateY(1px);
        }
        
        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }
        
        .btn-login:hover::before {
            left: 100%;
        }
        
        .title-underline {
            width: 40px;
            height: 2px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            margin: 0.5rem auto 0;
            border-radius: 2px;
        }
    </style>
</head>
<body>
    <div class="glass-card w-full max-w-sm p-8 rounded-2xl">
        <!-- En-tête élégant -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-light text-white tracking-wider">SYSCOL</h1>
            <div class="title-underline"></div>
            <p class="text-xs text-white/30 mt-3 font-light">Accès sécurisé</p>
        </div>

        <!-- Messages d'erreur -->
        @if ($errors->any())
            <div class="mb-6 p-3 bg-red-500/10 border border-red-500/20 rounded-lg">
                <p class="text-red-400 text-xs text-center">{{ $errors->first() }}</p>
            </div>
        @endif

        <!-- Formulaire -->
        <form action="{{ route('login.submit') }}" method="POST" class="space-y-5">
            @csrf
            
            <!-- Email -->
            <div>
                <input 
                    type="email" 
                    name="email" 
                    value="{{ old('email') }}" 
                    placeholder="Adresse email"
                    class="input-field w-full px-5 py-3 rounded-lg text-white/90 text-sm focus:outline-none @error('email') border-red-500/50 @enderror" 
                    required 
                    autofocus
                >
            </div>

            <!-- Mot de passe -->
            <div>
                <input 
                    type="password" 
                    name="password" 
                    placeholder="Mot de passe"
                    class="input-field w-full px-5 py-3 rounded-lg text-white/90 text-sm focus:outline-none @error('password') border-red-500/50 @enderror" 
                    required
                >
            </div>

            <!-- Bouton de connexion -->
            <button 
                type="submit" 
                class="btn-login w-full py-3 rounded-lg text-white font-medium text-sm tracking-wide mt-6"
            >
                Se connecter
            </button>
        </form>

        <!-- Lien optionnel discret -->
        <p class="text-center mt-6">
            <a href="#" class="text-white/20 text-xs hover:text-white/40 transition-colors">
                Mot de passe oublié ?
            </a>
        </p>
    </div>
</body>
</html>