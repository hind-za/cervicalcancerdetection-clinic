<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation de mot de passe</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .btn {
            display: inline-block;
            background: #007bff;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
        .btn:hover {
            background: #0056b3;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #666;
            font-size: 14px;
        }
        .warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🏥 CervicalCare AI</h1>
        <p>Réinitialisation de votre mot de passe</p>
    </div>
    
    <div class="content">
        <h2>Bonjour {{ $user->name }},</h2>
        
        <p>Vous avez demandé la réinitialisation de votre mot de passe pour votre compte CervicalCare AI.</p>
        
        <p>Cliquez sur le bouton ci-dessous pour créer un nouveau mot de passe :</p>
        
        <div style="text-align: center;">
            <a href="{{ $resetUrl }}" class="btn">Réinitialiser mon mot de passe</a>
        </div>
        
        <div class="warning">
            <strong>⚠️ Important :</strong>
            <ul>
                <li>Ce lien est valide pendant 60 minutes seulement</li>
                <li>Si vous n'avez pas demandé cette réinitialisation, ignorez cet email</li>
                <li>Pour votre sécurité, ne partagez jamais ce lien</li>
            </ul>
        </div>
        
        <p>Si le bouton ne fonctionne pas, copiez et collez ce lien dans votre navigateur :</p>
        <p style="word-break: break-all; background: #e9ecef; padding: 10px; border-radius: 5px;">
            {{ $resetUrl }}
        </p>
        
        <p>Cordialement,<br>
        L'équipe CervicalCare AI</p>
    </div>
    
    <div class="footer">
        <p>© {{ date('Y') }} CervicalCare AI - Plateforme de détection précoce du cancer cervical</p>
        <p>Cet email a été envoyé à {{ $email }}</p>
    </div>
</body>
</html>