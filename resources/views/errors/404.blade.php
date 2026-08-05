<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page non trouvée</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
        body::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background-image: radial-gradient(rgba(255, 255, 255, 0.05) 1px, transparent 1px);
            background-size: 30px 30px;
            pointer-events: none;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            padding: 3rem;
            border-radius: 2rem;
            text-align: center;
            max-width: 500px;
            width: 90%;
        }
        .error-code {
            font-size: 8rem;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1;
            margin-bottom: 1rem;
        }
        .btn-home {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: all 0.3s ease;
            display: inline-block;
            padding: 0.75rem 2rem;
            border-radius: 0.75rem;
            color: white;
            text-decoration: none;
            font-weight: 500;
            margin-top: 2rem;
        }
        .btn-home:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(102, 126, 234, 0.5);
        }
    </style>
</head>
<body>
    <div class="glass-card">
        <div class="error-code">404</div>
        <h1 class="text-3xl font-light text-white mb-4">Page non trouvée</h1>
        <p class="text-white/60 mb-6">
            Désolé, la page que vous recherchez n'existe pas ou a été déplacée.
        </p>
        <a href="{{ url('/dashboard') }}" class="btn-home">
            Retour à l'accueil
        </a>
    </div>
</body>
</html>