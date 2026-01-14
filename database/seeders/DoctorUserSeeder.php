<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DoctorUserSeeder extends Seeder
{
    public function run()
    {
        // Créer un utilisateur docteur s'il n'existe pas
        $doctor = User::where('role', 'doctor')->first();
        
        if (!$doctor) {
            User::create([
                'name' => 'Dr. Sarah Martin',
                'email' => 'doctor@test.com',
                'password' => bcrypt('password'),
                'role' => 'doctor'
            ]);
            
            $this->command->info('✅ Utilisateur docteur créé: doctor@test.com / password');
        } else {
            $this->command->info('ℹ️  Utilisateur docteur existe déjà: ' . $doctor->email);
        }
    }
}