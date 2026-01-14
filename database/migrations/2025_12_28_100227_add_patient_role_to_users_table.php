<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modifier l'enum pour inclure 'patient'
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'doctor', 'patient') DEFAULT 'doctor'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revenir à l'enum original
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'doctor') DEFAULT 'doctor'");
    }
};
