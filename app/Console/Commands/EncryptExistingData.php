<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Patient;
use App\Models\AnalyseIA;
use App\Services\ImageEncryptionService;
use App\Traits\EncryptionTransition;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class EncryptExistingData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:encrypt-existing {--dry-run : Afficher ce qui sera fait sans l\'exécuter} {--force : Forcer l\'exécution sans confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Chiffrer les données sensibles existantes dans la base de données';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');
        
        if ($dryRun) {
            $this->info('🔍 MODE DRY-RUN - Aucune modification ne sera effectuée');
        } else {
            $this->warn('⚠️  ATTENTION: Cette commande va modifier les données existantes');
            if (!$force && !$this->confirm('Voulez-vous continuer?')) {
                $this->info('Opération annulée');
                return;
            }
        }

        $this->info('🔐 Début du chiffrement des données sensibles...');

        // Chiffrer les données des patients
        $this->encryptPatientData($dryRun);

        // Chiffrer les données des analyses IA
        $this->encryptAnalyseData($dryRun);

        // Chiffrer les images médicales
        $this->encryptImages($dryRun);

        $this->info('✅ Chiffrement terminé!');
    }

    private function encryptPatientData(bool $dryRun)
    {
        $this->info('📋 Chiffrement des données patients...');
        
        // Récupérer les patients avec des données non chiffrées
        $patients = Patient::all();
        $count = 0;
        $alreadyEncrypted = 0;

        foreach ($patients as $patient) {
            $needsEncryption = false;
            $encryptedFields = [];
            
            // Vérifier quels champs ont besoin d'être chiffrés
            $encryptedFields = ['nom', 'prenom', 'telephone', 'email', 'adresse', 'antecedents_medicaux', 'notes'];
            $fieldsToEncrypt = [];
            
            foreach ($encryptedFields as $field) {
                $value = $patient->getOriginal($field);
                if (!empty($value) && !EncryptionTransition::isValueEncrypted($value)) {
                    $needsEncryption = true;
                    $fieldsToEncrypt[] = $field;
                }
            }

            if ($dryRun) {
                if ($needsEncryption) {
                    $this->line("  - Patient ID {$patient->id}: {$patient->getOriginal('nom')} {$patient->getOriginal('prenom')} (Champs: " . implode(', ', $fieldsToEncrypt) . ")");
                } else {
                    $this->line("  - Patient ID {$patient->id}: Déjà chiffré");
                    $alreadyEncrypted++;
                }
            } else {
                if ($needsEncryption) {
                    try {
                        // Forcer la sauvegarde en modifiant chaque champ chiffré
                        foreach ($fieldsToEncrypt as $field) {
                            $currentValue = $patient->getOriginal($field);
                            if (!empty($currentValue)) {
                                $patient->$field = $currentValue; // Déclenche le mutateur
                            }
                        }
                        $patient->save();
                        $count++;
                        $this->line("  ✅ Patient ID {$patient->id} chiffré");
                    } catch (\Exception $e) {
                        $this->error("  ❌ Erreur patient ID {$patient->id}: " . $e->getMessage());
                        Log::error('Patient encryption failed', [
                            'patient_id' => $patient->id,
                            'error' => $e->getMessage()
                        ]);
                    }
                } else {
                    $alreadyEncrypted++;
                }
            }
        }

        if (!$dryRun) {
            $this->info("  ✅ {$count} patients chiffrés, {$alreadyEncrypted} déjà chiffrés");
        } else {
            $this->info("  📊 {$alreadyEncrypted} patients déjà chiffrés");
        }
    }

    private function encryptAnalyseData(bool $dryRun)
    {
        $this->info('🔬 Chiffrement des données d\'analyses...');
        
        $analyses = AnalyseIA::whereNotNull('commentaires_medecin')
                            ->orWhereNotNull('recommandations_finales')
                            ->orWhereNotNull('interpretation')
                            ->get();
        $count = 0;
        $alreadyEncrypted = 0;

        foreach ($analyses as $analyse) {
            $needsEncryption = false;
            $encryptedFields = [];
            
            // Vérifier quels champs ont besoin d'être chiffrés
            $encryptedFields = ['commentaires_medecin', 'recommandations_finales', 'interpretation'];
            $fieldsToEncrypt = [];
            
            foreach ($encryptedFields as $field) {
                $value = $analyse->getOriginal($field);
                if (!empty($value) && !EncryptionTransition::isValueEncrypted($value)) {
                    $needsEncryption = true;
                    $fieldsToEncrypt[] = $field;
                }
            }

            if ($dryRun) {
                if ($needsEncryption) {
                    $this->line("  - Analyse ID {$analyse->id} (Patient: {$analyse->patient_id}) (Champs: " . implode(', ', $fieldsToEncrypt) . ")");
                } else {
                    $this->line("  - Analyse ID {$analyse->id}: Déjà chiffrée");
                    $alreadyEncrypted++;
                }
            } else {
                if ($needsEncryption) {
                    try {
                        // Forcer la sauvegarde en modifiant chaque champ chiffré
                        foreach ($fieldsToEncrypt as $field) {
                            $currentValue = $analyse->getOriginal($field);
                            if (!empty($currentValue)) {
                                $analyse->$field = $currentValue; // Déclenche le mutateur
                            }
                        }
                        $analyse->save();
                        $count++;
                        $this->line("  ✅ Analyse ID {$analyse->id} chiffrée");
                    } catch (\Exception $e) {
                        $this->error("  ❌ Erreur analyse ID {$analyse->id}: " . $e->getMessage());
                        Log::error('Analyse encryption failed', [
                            'analyse_id' => $analyse->id,
                            'error' => $e->getMessage()
                        ]);
                    }
                } else {
                    $alreadyEncrypted++;
                }
            }
        }

        if (!$dryRun) {
            $this->info("  ✅ {$count} analyses chiffrées, {$alreadyEncrypted} déjà chiffrées");
        } else {
            $this->info("  📊 {$alreadyEncrypted} analyses déjà chiffrées");
        }
    }

    private function encryptImages(bool $dryRun)
    {
        $this->info('🖼️  Chiffrement des images médicales...');
        
        $analyses = AnalyseIA::whereNotNull('chemin_image')->get();
        $count = 0;

        foreach ($analyses as $analyse) {
            $imagePath = $analyse->chemin_image;
            
            if ($dryRun) {
                $this->line("  - Image: {$imagePath}");
            } else {
                // Vérifier si l'image existe dans public
                $publicPath = $imagePath; // Le chemin est déjà relatif à public
                $privatePath = $imagePath;
                
                if (Storage::disk('public')->exists($publicPath)) {
                    // Image existe dans public, la migrer vers private chiffrée
                    $imageContent = Storage::disk('public')->get($publicPath);
                    
                    if (ImageEncryptionService::encryptAndStore($imageContent, $privatePath)) {
                        // Supprimer l'original public après chiffrement réussi
                        Storage::disk('public')->delete($publicPath);
                        $count++;
                        $this->line("  ✅ Image migrée et chiffrée: {$imagePath}");
                    } else {
                        $this->error("  ❌ Échec migration: {$imagePath}");
                    }
                } elseif (ImageEncryptionService::isEncrypted($privatePath)) {
                    $this->line("  ⏭️  Déjà chiffrée: {$imagePath}");
                } else {
                    $this->error("  ❌ Image non trouvée: {$imagePath}");
                }
            }
        }

        if (!$dryRun) {
            $this->info("  ✅ {$count} images migrées et chiffrées");
        }
    }
}
