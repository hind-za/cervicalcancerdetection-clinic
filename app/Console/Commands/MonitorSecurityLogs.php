<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MonitorSecurityLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'security:monitor {hours=1 : Nombre d\'heures à analyser} {--report : Générer un rapport}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Surveiller les logs de sécurité pour détecter les problèmes de chiffrement et d\'accès';

    private $alertThresholds = [
        'decryption_errors' => 5,
        'unauthorized_access' => 3,
        'sensitive_data_access' => 50,
        'image_access_errors' => 10
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hours = $this->argument('hours');
        $generateReport = $this->option('report');

        $this->info("🔍 Surveillance des logs de sécurité (dernières {$hours}h)");
        $this->line(str_repeat("=", 60));

        $since = now()->subHours($hours);
        $logs = $this->getRecentLogs($since);

        if (empty($logs)) {
            $this->warn('Aucun log trouvé pour la période spécifiée');
            return;
        }

        $this->info("📊 {" . count($logs) . "} entrées de log analysées");
        $this->newLine();

        // Analyses
        $this->analyzeDecryptionErrors($logs);
        $this->analyzeUnauthorizedAccess($logs);
        $this->analyzeSensitiveDataAccess($logs);
        $this->analyzeImageAccessErrors($logs);

        if ($generateReport) {
            $this->generateReport($logs, $hours);
        }

        $this->info('✅ Surveillance terminée');
    }

    private function getRecentLogs($since)
    {
        $logPath = storage_path('logs/laravel.log');
        
        if (!file_exists($logPath)) {
            $this->error("Fichier de log non trouvé: {$logPath}");
            return [];
        }

        $logs = [];
        $handle = fopen($logPath, 'r');
        
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                if (preg_match('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $line, $matches)) {
                    $logTime = Carbon::parse($matches[1]);
                    if ($logTime->gte($since)) {
                        $logs[] = [
                            'timestamp' => $logTime,
                            'content' => $line
                        ];
                    }
                }
            }
            fclose($handle);
        }

        return $logs;
    }

    private function analyzeDecryptionErrors($logs)
    {
        $this->info('🔐 Erreurs de déchiffrement:');
        
        $errors = array_filter($logs, function($log) {
            return strpos($log['content'], 'DecryptException') !== false ||
                   strpos($log['content'], 'The payload is invalid') !== false ||
                   strpos($log['content'], 'decryption failed') !== false;
        });

        $count = count($errors);
        
        if ($count > $this->alertThresholds['decryption_errors']) {
            $this->error("   🚨 ALERTE: {$count} erreurs de déchiffrement!");
        } else {
            $this->line("   ✅ {$count} erreurs de déchiffrement");
        }

        foreach (array_slice($errors, -3) as $error) {
            $this->line("   📝 " . $error['timestamp']->format('H:i:s') . " - " . 
                       substr(trim($error['content']), 0, 80) . "...");
        }
        $this->newLine();
    }

    private function analyzeUnauthorizedAccess($logs)
    {
        $this->info('🚫 Tentatives d\'accès non autorisé:');
        
        $attempts = array_filter($logs, function($log) {
            return strpos($log['content'], 'Unauthorized') !== false ||
                   strpos($log['content'], 'Accès non autorisé') !== false ||
                   strpos($log['content'], 'Forbidden') !== false;
        });

        $count = count($attempts);
        
        if ($count > $this->alertThresholds['unauthorized_access']) {
            $this->error("   🚨 ALERTE: {$count} tentatives non autorisées!");
        } else {
            $this->line("   ✅ {$count} tentatives non autorisées");
        }

        // Analyser les IPs suspectes
        $ips = [];
        foreach ($attempts as $attempt) {
            if (preg_match('/ip.*?(\d+\.\d+\.\d+\.\d+)/', $attempt['content'], $matches)) {
                $ip = $matches[1];
                $ips[$ip] = ($ips[$ip] ?? 0) + 1;
            }
        }

        foreach ($ips as $ip => $count) {
            if ($count > 2) {
                $this->warn("   ⚠️  IP suspecte: {$ip} ({$count} tentatives)");
            }
        }
        $this->newLine();
    }

    private function analyzeSensitiveDataAccess($logs)
    {
        $this->info('📋 Accès aux données sensibles:');
        
        $accesses = array_filter($logs, function($log) {
            return strpos($log['content'], 'Sensitive data access') !== false ||
                   strpos($log['content'], 'Image access') !== false;
        });

        $count = count($accesses);
        
        if ($count > $this->alertThresholds['sensitive_data_access']) {
            $this->warn("   ⚠️  Volume élevé: {$count} accès");
        } else {
            $this->line("   ✅ {$count} accès aux données sensibles");
        }

        // Top utilisateurs
        $users = [];
        foreach ($accesses as $access) {
            if (preg_match('/user_id.*?(\d+)/', $access['content'], $matches)) {
                $userId = $matches[1];
                $users[$userId] = ($users[$userId] ?? 0) + 1;
            }
        }

        arsort($users);
        foreach (array_slice($users, 0, 3, true) as $userId => $count) {
            $this->line("   👤 Utilisateur {$userId}: {$count} accès");
        }
        $this->newLine();
    }

    private function analyzeImageAccessErrors($logs)
    {
        $this->info('🖼️  Erreurs d\'accès aux images:');
        
        $errors = array_filter($logs, function($log) {
            return (strpos($log['content'], 'Image') !== false || 
                    strpos($log['content'], 'image') !== false) &&
                   (strpos($log['content'], 'ERROR') !== false || 
                    strpos($log['content'], 'failed') !== false);
        });

        $count = count($errors);
        
        if ($count > $this->alertThresholds['image_access_errors']) {
            $this->error("   🚨 ALERTE: {$count} erreurs d'accès aux images!");
        } else {
            $this->line("   ✅ {$count} erreurs d'accès aux images");
        }
        $this->newLine();
    }

    private function generateReport($logs, $hours)
    {
        $reportPath = storage_path('logs/security_report_' . now()->format('Y-m-d_H-i-s') . '.txt');
        
        $report = "RAPPORT DE SÉCURITÉ - " . now()->format('d/m/Y H:i:s') . "\n";
        $report .= str_repeat("=", 60) . "\n\n";
        $report .= "Période analysée: {$hours} heures\n";
        $report .= "Nombre total de logs: " . count($logs) . "\n\n";
        
        // Ajouter les statistiques détaillées ici...
        
        file_put_contents($reportPath, $report);
        
        $this->info("📄 Rapport sauvegardé: {$reportPath}");
    }
}
