<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    public function run(): void
    {
        // Créer un utilisateur avec une vraie adresse email pour les tests
        $testEmail = 'test.cervicalcare@gmail.com'; // Remplacez par votre vraie adresse
        
        User::firstOrCreate([
            'email' => $testEmail
        ], [
            'name' => 'Test User',
            'password' => Hash::make('password123'),
            'role' => 'doctor'
        ]);

        User::firstOrCreate([
            'email' => 'admin.cervicalcare@gmail.com' // Remplacez par votre vraie adresse
        ], [
            'name' => 'Admin User',
            'password' => Hash::make('admin123'),
            'role' => 'admin'
        ]);

        echo "Utilisateurs de test créés:\n";
        echo "- {$testEmail} (doctor) - password: password123\n";
        echo "- admin.cervicalcare@gmail.com (admin) - password: admin123\n";
        echo "\n⚠️  IMPORTANT: Remplacez ces emails par vos vraies adresses dans le seeder !\n";
    }
}
