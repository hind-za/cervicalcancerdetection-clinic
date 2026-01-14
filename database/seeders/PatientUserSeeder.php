<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Patient;
use Illuminate\Support\Facades\Hash;

class PatientUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer un utilisateur patient de test
        $patientUser = User::firstOrCreate(
            ['email' => 'marie@test.com'],
            [
                'name' => 'Marie Dupont',
                'password' => Hash::make('password123'),
                'role' => 'patient',
            ]
        );

        // Créer le profil patient associé
        Patient::firstOrCreate(
            ['email' => 'marie@test.com'],
            [
                'nom' => 'Dupont',
                'prenom' => 'Marie',
                'numero_dossier' => 'PAT000001',
                'date_naissance' => '1985-03-15',
                'age' => 39,
                'sexe' => 'F',
                'telephone' => '0123456789',
                'adresse' => '123 Rue de la Santé, 75001 Paris',
                'antecedents_medicaux' => 'Aucun antécédent particulier. Suivi gynécologique régulier.',
            ]
        );

        // Créer un autre utilisateur patient
        $patientUser2 = User::firstOrCreate(
            ['email' => 'sophie@test.com'],
            [
                'name' => 'Sophie Martin',
                'password' => Hash::make('password123'),
                'role' => 'patient',
            ]
        );

        Patient::firstOrCreate(
            ['email' => 'sophie@test.com'],
            [
                'nom' => 'Martin',
                'prenom' => 'Sophie',
                'numero_dossier' => 'PAT000002',
                'date_naissance' => '1990-07-22',
                'age' => 34,
                'sexe' => 'F',
                'telephone' => '0987654321',
                'adresse' => '456 Avenue des Fleurs, 69001 Lyon',
                'antecedents_medicaux' => 'Antécédents familiaux de cancer du col de l\'utérus (mère).',
            ]
        );

        echo "Utilisateurs patients créés avec succès!\n";
        echo "- marie@test.com / password123\n";
        echo "- sophie@test.com / password123\n";
    }
}
