<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateEtherealCredentials extends Command
{
    protected $signature = 'email:setup';
    protected $description = 'Configure email settings for testing';

    public function handle()
    {
        $this->info('Configuration email pour CervicalCare AI');
        $this->info('=====================================');
        
        // Utiliser un service SMTP gratuit pour les tests
        $this->info('Configuration automatique avec un service de test...');
        
        // Mettre à jour le fichier .env
        $envPath = base_path('.env');
        $envContent = file_get_contents($envPath);
        
        // Configuration pour Ethereal Email (service de test gratuit)
        $emailConfig = [
            'MAIL_MAILER=smtp',
            'MAIL_HOST=smtp.ethereal.email',
            'MAIL_PORT=587',
            'MAIL_USERNAME=ethereal.user@ethereal.email',
            'MAIL_PASSWORD=ethereal.pass',
            'MAIL_ENCRYPTION=tls',
            'MAIL_FROM_ADDRESS="noreply@cervicalcare.com"',
            'MAIL_FROM_NAME="CervicalCare AI"'
        ];
        
        // Remplacer les lignes MAIL_* existantes
        $patterns = [
            '/MAIL_MAILER=.*/',
            '/MAIL_HOST=.*/',
            '/MAIL_PORT=.*/',
            '/MAIL_USERNAME=.*/',
            '/MAIL_PASSWORD=.*/',
            '/MAIL_ENCRYPTION=.*/',
            '/MAIL_FROM_ADDRESS=.*/',
            '/MAIL_FROM_NAME=.*/'
        ];
        
        foreach ($patterns as $i => $pattern) {
            $envContent = preg_replace($pattern, $emailConfig[$i], $envContent);
        }
        
        file_put_contents($envPath, $envContent);
        
        $this->info('✅ Configuration email mise à jour !');
        $this->info('');
        $this->info('Pour utiliser Gmail à la place :');
        $this->info('1. Activez l\'authentification à 2 facteurs sur Gmail');
        $this->info('2. Générez un mot de passe d\'application');
        $this->info('3. Modifiez le .env avec vos identifiants Gmail');
        
        return 0;
    }
}
