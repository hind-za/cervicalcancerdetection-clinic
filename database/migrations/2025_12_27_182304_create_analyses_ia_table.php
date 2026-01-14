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
        Schema::create('analyses_ia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Qui a fait l'analyse
            
            // Informations sur l'image
            $table->string('nom_image');
            $table->string('chemin_image');
            $table->string('taille_image')->nullable(); // Ex: "2.5 MB"
            $table->string('dimensions_image')->nullable(); // Ex: "1024x768"
            
            // Résultats de l'IA
            $table->string('classe_predite');
            $table->decimal('probabilite', 6, 4); // 0.9876
            $table->json('toutes_probabilites'); // Toutes les probabilités par classe
            $table->enum('niveau_risque', ['Faible', 'Modéré', 'Élevé']);
            $table->text('interpretation');
            $table->json('recommandations');
            $table->decimal('temps_analyse', 5, 3)->nullable(); // Temps en secondes
            
            // Statut et validation
            $table->enum('statut', ['Brouillon', 'En attente', 'Validé', 'À revoir'])->default('En attente');
            $table->text('commentaires_medecin')->nullable();
            $table->foreignId('valide_par')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('date_validation')->nullable();
            
            $table->timestamps();
            
            // Index pour les recherches
            $table->index(['patient_id', 'created_at']);
            $table->index(['statut', 'created_at']);
            $table->index('classe_predite');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analyses_ia');
    }
};
