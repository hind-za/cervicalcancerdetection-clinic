<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DoctorTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer un utilisateur docteur de test
        User::firstOrCreate(
            ['email' => 'doctor@test.com'],
            [
                'name' => 'Dr. Martin Dubois',
                'password' => Hash::make('doctor123'),
                'role' => 'doctor',
                'speciality' => 'Gynécologie'
            ]
        );

        echo "Utilisateur docteur créé avec succès!\n";
        echo "- doctor@test.com / doctor123\n";
    }
}