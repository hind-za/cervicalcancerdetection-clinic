<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SecurityDashboardController extends Controller
{
    // Le middleware sera géré dans les routes

    /**
     * Afficher le tableau de bord de sécurité
     */
    public function index()
    {
        // Vérifier l'authentification manuellement
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté pour accéder à cette page.');
        }

        // Vérifier le rôle admin
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Accès réservé aux administrateurs.');
        }

        try {
            $stats = $this->getSecurityStats();
            $recentLogs = $this->getRecentSecurityLogs();
            $alerts = $this->getSecurityAlerts();

            return view('admin.security-dashboard', compact('stats', 'recentLogs', 'alerts'));
        } catch (\Exception $e) {
            // En cas d'erreur, afficher une version simplifiée
            $stats = [
                'total_logs' => 0,
                'decryption_errors' => 0,
                'unauthorized_attempts' => 0,
                'sensitive_data_accesses' => 0,
                'image_access_errors' => 0,
                'unique_users' => 0,
                'unique_ips' => 0,
                'period_hours' => 24
            ];
            $recentLogs = [];
            $alerts = [];

            return view('admin.security-dashboard', compact('stats', 'recentLogs', 'alerts'))
                ->with('error', 'Erreur lors du chargement des logs: ' . $e->getMessage());
        }
    }

    /**
     * API pour les statistiques en temps réel
     */
    public function getStats(Request $request)
    {
        $hours = $request->get('hours', 24);
        $stats = $this->getSecurityStats($hours);

        return response()->json($stats);
    }

    /**
     * Obtenir les statistiques de sécurité
     */
    private function getSecurityStats($hours = 24)
    {
        $since = now()->subHours($hours);
        $logPath = storage_path('logs/laravel.log');

        if (!file_exists($logPath)) {
            return [
                'total_logs' => 0,
                'decryption_errors' => 0,
                'unauthorized_attempts' => 0,
                'sensitive_data_accesses' => 0,
                'image_access_errors' => 0,
                'unique_users' => 0,
                'unique_ips' => 0
            ];
        }

        $logs = $this->parseLogFile($logPath, $since);

        return [
            'total_logs' => count($logs),
            'decryption_errors' => $this->countLogType($logs, ['DecryptException', 'payload is invalid']),
            'unauthorized_attempts' => $this->countLogType($logs, ['Unauthorized', 'Accès non autorisé', '403']),
            'sensitive_data_accesses' => $this->countLogType($logs, ['Sensitive data access', 'Image access']),
            'image_access_errors' => $this->countLogType($logs, ['Image.*ERROR', 'image.*failed']),
            'unique_users' => $this->countUniqueUsers($logs),
            'unique_ips' => $this->countUniqueIPs($logs),
            'period_hours' => $hours
        ];
    }

    /**
     * Obtenir les logs de sécurité récents
     */
    private function getRecentSecurityLogs($limit = 20)
    {
        $logPath = storage_path('logs/laravel.log');
        
        if (!file_exists($logPath)) {
            return [];
        }

        $logs = $this->parseLogFile($logPath, now()->subHours(1));
        
        // Filtrer les logs de sécurité
        $securityLogs = array_filter($logs, function($log) {
            return strpos($log['content'], 'Sensitive data access') !== false ||
                   strpos($log['content'], 'Image access') !== false ||
                   strpos($log['content'], 'Unauthorized') !== false ||
                   strpos($log['content'], 'DecryptException') !== false;
        });

        // Trier par timestamp décroissant
        usort($securityLogs, function($a, $b) {
            return $b['timestamp']->timestamp - $a['timestamp']->timestamp;
        });

        return array_slice($securityLogs, 0, $limit);
    }

    /**
     * Obtenir les alertes de sécurité
     */
    private function getSecurityAlerts()
    {
        $alerts = [];
        $stats = $this->getSecurityStats(1); // Dernière heure

        // Seuils d'alerte
        if ($stats['decryption_errors'] > 5) {
            $alerts[] = [
                'type' => 'danger',
                'title' => 'Erreurs de déchiffrement élevées',
                'message' => "{$stats['decryption_errors']} erreurs de déchiffrement dans la dernière heure",
                'timestamp' => now()
            ];
        }

        if ($stats['unauthorized_attempts'] > 3) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'Tentatives d\'accès non autorisé',
                'message' => "{$stats['unauthorized_attempts']} tentatives non autorisées dans la dernière heure",
                'timestamp' => now()
            ];
        }

        if ($stats['sensitive_data_accesses'] > 50) {
            $alerts[] = [
                'type' => 'info',
                'title' => 'Volume d\'accès élevé',
                'message' => "{$stats['sensitive_data_accesses']} accès aux données sensibles dans la dernière heure",
                'timestamp' => now()
            ];
        }

        return $alerts;
    }

    /**
     * Parser le fichier de log
     */
    private function parseLogFile($logPath, $since)
    {
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

    /**
     * Compter les logs d'un type spécifique
     */
    private function countLogType($logs, $patterns)
    {
        $count = 0;
        foreach ($logs as $log) {
            foreach ($patterns as $pattern) {
                if (preg_match('/' . $pattern . '/i', $log['content'])) {
                    $count++;
                    break;
                }
            }
        }
        return $count;
    }

    /**
     * Compter les utilisateurs uniques
     */
    private function countUniqueUsers($logs)
    {
        $users = [];
        foreach ($logs as $log) {
            if (preg_match('/user_id.*?(\d+)/', $log['content'], $matches)) {
                $users[$matches[1]] = true;
            }
        }
        return count($users);
    }

    /**
     * Compter les IPs uniques
     */
    private function countUniqueIPs($logs)
    {
        $ips = [];
        foreach ($logs as $log) {
            if (preg_match('/ip.*?(\d+\.\d+\.\d+\.\d+)/', $log['content'], $matches)) {
                $ips[$matches[1]] = true;
            }
        }
        return count($ips);
    }
}