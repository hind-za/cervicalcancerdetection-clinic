<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('analyse_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->string('nom_image');
            $table->string('chemin_image');
            $table->enum('resultat', ['Normal', 'Anomalie Détectée', 'À Surveiller']);
            $table->decimal('confiance', 5, 2); // Pourcentage de confiance
            $table->json('details')->nullable(); // Détails de l'analyse
            $table->decimal('temps_analyse', 8, 3)->nullable(); // Temps en secondes
            $table->enum('statut', ['En attente', 'Validé', 'À revoir'])->default('En attente');
            $table->foreignId('valide_par')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('date_validation')->nullable();
            $table->text('commentaires')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analyse_images');
    }
};
