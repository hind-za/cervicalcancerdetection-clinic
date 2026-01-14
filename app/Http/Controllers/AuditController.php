<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class AuditController extends Controller
{
    /**
     * Afficher les logs d'audit (admin seulement)
     */
    public function index(Request $request)
    {
        // Vérifier que l'utilisateur est admin
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403, 'Accès non autorisé');
        }

        $query = AuditLog::with('user')->orderBy('created_at', 'desc');

        // Filtres
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('model_type')) {
            $query->where('model_type', $request->model_type);
        }

        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(50);

        // Statistiques
        $stats = [
            'total_logs' => AuditLog::count(),
            'today_logs' => AuditLog::whereDate('created_at', today())->count(),
            'critical_logs' => AuditLog::where('severity', 'critical')->count(),
            'unique_users' => AuditLog::distinct('user_id')->count('user_id')
        ];

        return view('admin.audit', compact('logs', 'stats'));
    }

    /**
     * Afficher les détails d'un log d'audit
     */
    public function show(AuditLog $auditLog)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403, 'Accès non autorisé');
        }

        return view('admin.audit-detail', compact('auditLog'));
    }

    /**
     * Exporter les logs d'audit
     */
    public function export(Request $request)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403, 'Accès non autorisé');
        }

        // Log de l'export
        AuditLog::logAction(
            'export',
            'AuditLog',
            null,
            null,
            ['filters' => $request->all()],
            'high',
            'Export des logs d\'audit'
        );

        $query = AuditLog::with('user')->orderBy('created_at', 'desc');

        // Appliquer les mêmes filtres que l'index
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('model_type')) {
            $query->where('model_type', $request->model_type);
        }

        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->limit(10000)->get(); // Limiter à 10k pour éviter les timeouts

        $filename = 'audit_logs_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            
            // En-têtes CSV
            fputcsv($file, [
                'ID', 'Date', 'Utilisateur', 'Action', 'Modèle', 'ID Modèle', 
                'Sévérité', 'IP', 'Description'
            ]);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->created_at->format('Y-m-d H:i:s'),
                    $log->user ? $log->user->name : 'N/A',
                    $log->action,
                    $log->model_type,
                    $log->model_id,
                    $log->severity,
                    $log->ip_address,
                    $log->description
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
