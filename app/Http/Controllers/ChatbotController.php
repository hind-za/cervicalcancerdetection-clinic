<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Patient;
use App\Models\AnalyseIA;
use App\Models\User;

class ChatbotController extends Controller
{
    /**
     * Traiter une question du chatbot
     */
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:500'
        ]);

        $message = strtolower(trim($request->message));
        $user = Auth::user();
        
        // Analyser le message et générer une réponse
        $response = $this->generateResponse($message, $user);
        
        return response()->json([
            'success' => true,
            'response' => $response,
            'timestamp' => now()->format('H:i')
        ]);
    }

    /**
     * Générer une réponse basée sur le message et le rôle de l'utilisateur
     */
    private function generateResponse($message, $user)
    {
        $role = $user->role;
        
        // Mots-clés et réponses selon le rôle
        $responses = $this->getResponsesByRole($role, $user);
        
        // Rechercher une correspondance
        foreach ($responses as $keywords => $response) {
            $keywordList = explode('|', $keywords);
            foreach ($keywordList as $keyword) {
                if (strpos($message, trim($keyword)) !== false) {
                    return $this->personalizeResponse($response, $user);
                }
            }
        }
        
        // Réponse par défaut si aucune correspondance
        return $this->getDefaultResponse($role, $user);
    }

    /**
     * Obtenir les réponses selon le rôle
     */
    private function getResponsesByRole($role, $user)
    {
        $commonResponses = [
            'bonjour|salut|hello|bonsoir' => "Bonjour {name} ! Comment puis-je vous aider aujourd'hui ?",
            'merci|remercie' => "Je vous en prie ! N'hésitez pas si vous avez d'autres questions.",
            'au revoir|bye|à bientôt' => "Au revoir {name} ! Bonne journée et n'hésitez pas à revenir si vous avez des questions.",
            'aide|help|aidez-moi' => "Je suis là pour vous aider ! Posez-moi vos questions sur {domain}.",
        ];

        switch ($role) {
            case 'patient':
                return array_merge($commonResponses, [
                    'analyse|résultat|test' => "Pour consulter vos analyses, rendez-vous dans la section 'Mes Analyses'. Vous y trouverez l'historique complet de vos examens avec les résultats détaillés.",
                    'rendez-vous|rdv|consultation' => "Pour prendre rendez-vous, allez dans 'Mes RDV' où vous pouvez demander une consultation. Vous pouvez aussi appeler directement la clinique au 01 23 45 67 89.",
                    'profil|informations|données' => "Vous pouvez modifier vos informations personnelles dans 'Mon Profil'. Seules certaines données comme le téléphone et l'adresse peuvent être modifiées.",
                    'rapport|pdf|télécharger' => "Les rapports PDF sont disponibles pour les analyses validées. Cliquez sur l'icône de téléchargement dans le détail de votre analyse.",
                    'risque|danger|inquiet' => "Si vous avez des inquiétudes concernant vos résultats, n'hésitez pas à contacter votre médecin. Les analyses à risque élevé nécessitent un suivi médical immédiat.",
                    'cancer|maladie|santé' => "Pour toute question médicale, consultez votre médecin traitant. Ce système d'IA est un outil d'aide au diagnostic, pas un remplacement de l'avis médical.",
                    'mot de passe|connexion|compte' => "Pour changer votre mot de passe, contactez l'administration. Gardez vos identifiants confidentiels pour protéger vos données médicales.",
                    'notification|alerte' => "Les notifications apparaissent dans votre dashboard et dans 'Mes RDV'. Elles vous informent des analyses en attente ou des suivis nécessaires.",
                ]);

            case 'doctor':
                return array_merge($commonResponses, [
                    'patient|dossier|analyse' => "Vous pouvez consulter tous les patients et leurs analyses dans le dashboard médecin. Les analyses en attente de validation sont prioritaires.",
                    'validation|valider|approuver' => "Pour valider une analyse, cliquez sur l'analyse concernée et utilisez le bouton 'Valider'. Vous pouvez ajouter des commentaires médicaux.",
                    'rapport|pdf|document' => "Les rapports PDF sont générés automatiquement après validation. Vous pouvez les télécharger depuis le détail de l'analyse.",
                    'ia|intelligence artificielle|algorithme' => "L'IA analyse les images cytologiques avec une précision élevée. Votre validation médicale reste essentielle pour confirmer le diagnostic.",
                    'risque|urgent|priorité' => "Les analyses à risque élevé sont marquées en rouge et nécessitent une attention immédiate. Priorisez leur validation.",
                    'historique|suivi|évolution' => "L'historique complet des patients est disponible dans leur dossier. Vous pouvez suivre l'évolution des analyses dans le temps.",
                    'statistiques|données|performance' => "Les statistiques de performance sont disponibles dans le dashboard. Elles incluent le nombre d'analyses validées et les tendances.",
                    'formation|aide|utilisation' => "Pour toute question sur l'utilisation du système, consultez la documentation ou contactez l'équipe technique.",
                ]);

            case 'admin':
                return array_merge($commonResponses, [
                    'utilisateur|compte|gestion' => "Vous pouvez gérer tous les utilisateurs depuis le dashboard admin. Créez, modifiez ou désactivez les comptes selon les besoins.",
                    'sécurité|audit|surveillance' => "Le système de surveillance sécurisé est accessible via 'Surveillance Sécurité'. Vous y trouverez les logs d'audit et les alertes.",
                    'patient|dossier|données' => "La gestion des patients se fait via 'Gestion des Patients'. Toutes les données sont chiffrées pour la confidentialité.",
                    'analyse|ia|système' => "Vous pouvez effectuer des analyses IA depuis le dashboard admin. Le système utilise un modèle de deep learning pour la détection.",
                    'rapport|statistiques|performance' => "Les rapports globaux et statistiques sont disponibles dans votre dashboard. Exportez les données selon vos besoins.",
                    'sauvegarde|backup|export' => "Les fonctions d'export et de sauvegarde sont disponibles dans les outils d'administration. Planifiez des sauvegardes régulières.",
                    'configuration|paramètres|système' => "Les paramètres système sont configurables via les fichiers de configuration. Contactez l'équipe technique pour les modifications avancées.",
                    'maintenance|mise à jour|version' => "Pour la maintenance système, utilisez les outils d'administration. Les mises à jour doivent être planifiées en dehors des heures d'activité.",
                ]);

            default:
                return $commonResponses;
        }
    }

    /**
     * Personnaliser la réponse avec les données de l'utilisateur
     */
    private function personalizeResponse($response, $user)
    {
        $name = $user->name;
        $domain = match($user->role) {
            'patient' => 'vos analyses médicales et votre suivi de santé',
            'doctor' => 'la validation des analyses et le suivi des patients',
            'admin' => 'la gestion du système et l\'administration',
            default => 'le système médical'
        };

        return str_replace(['{name}', '{domain}'], [$name, $domain], $response);
    }

    /**
     * Réponse par défaut si aucune correspondance
     */
    private function getDefaultResponse($role, $user)
    {
        $suggestions = match($role) {
            'patient' => [
                "Comment consulter mes analyses ?",
                "Comment prendre rendez-vous ?",
                "Comment télécharger mon rapport ?",
                "Que signifient mes résultats ?"
            ],
            'doctor' => [
                "Comment valider une analyse ?",
                "Comment consulter l'historique d'un patient ?",
                "Comment interpréter les résultats IA ?",
                "Comment générer un rapport médical ?"
            ],
            'admin' => [
                "Comment gérer les utilisateurs ?",
                "Comment consulter les logs de sécurité ?",
                "Comment effectuer une analyse IA ?",
                "Comment exporter les données ?"
            ],
            default => [
                "Comment utiliser le système ?",
                "Où trouver de l'aide ?",
                "Comment contacter le support ?"
            ]
        };

        $suggestionsList = implode('", "', $suggestions);
        
        return "Je ne suis pas sûr de comprendre votre question. Voici quelques suggestions : \"" . $suggestionsList . "\". Ou posez-moi une question plus spécifique !";
    }

    /**
     * Obtenir des suggestions contextuelles
     */
    public function getSuggestions(Request $request)
    {
        $user = Auth::user();
        $role = $user->role;

        $suggestions = match($role) {
            'patient' => [
                "Comment consulter mes analyses ?",
                "Comment prendre rendez-vous ?",
                "Que signifient mes résultats ?",
                "Comment télécharger mon rapport PDF ?",
                "Quand aurai-je mes prochains résultats ?",
                "Comment modifier mes informations personnelles ?"
            ],
            'doctor' => [
                "Comment valider une analyse ?",
                "Quelles analyses sont en attente ?",
                "Comment consulter l'historique d'un patient ?",
                "Comment interpréter les résultats de l'IA ?",
                "Comment ajouter des commentaires médicaux ?",
                "Comment générer un rapport pour un patient ?"
            ],
            'admin' => [
                "Comment créer un nouvel utilisateur ?",
                "Comment consulter les logs de sécurité ?",
                "Comment effectuer une analyse IA ?",
                "Comment gérer les patients ?",
                "Comment exporter les données ?",
                "Comment surveiller les performances du système ?"
            ],
            default => [
                "Comment utiliser le système ?",
                "Où trouver de l'aide ?",
                "Comment contacter le support ?"
            ]
        };

        return response()->json([
            'suggestions' => $suggestions
        ]);
    }

    /**
     * Obtenir des statistiques contextuelles pour enrichir les réponses
     */
    private function getContextualStats($user)
    {
        switch ($user->role) {
            case 'patient':
                $patient = Patient::where('email', 'LIKE', '%' . $user->email . '%')->first();
                if ($patient) {
                    return [
                        'total_analyses' => $patient->analysesIA->count(),
                        'analyses_validees' => $patient->analysesIA->where('statut', 'Validé')->count(),
                        'derniere_analyse' => $patient->analysesIA->first()?->created_at?->format('d/m/Y')
                    ];
                }
                break;

            case 'doctor':
                return [
                    'analyses_en_attente' => AnalyseIA::where('statut', 'En attente')->count(),
                    'analyses_validees_aujourd_hui' => AnalyseIA::where('statut', 'Validé')
                        ->where('valide_par', $user->id)
                        ->whereDate('date_validation', today())
                        ->count()
                ];

            case 'admin':
                return [
                    'total_patients' => Patient::count(),
                    'total_analyses' => AnalyseIA::count(),
                    'analyses_aujourd_hui' => AnalyseIA::whereDate('created_at', today())->count()
                ];
        }

        return [];
    }
}
