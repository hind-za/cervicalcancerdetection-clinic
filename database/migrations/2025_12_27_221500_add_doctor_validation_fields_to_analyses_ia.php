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
        Schema::table('analyses_ia', function (Blueprint $table) {
            // Champs pour la validation par le docteur
            $table->string('classe_finale_medecin')->nullable()->after('commentaires_medecin');
            $table->enum('decision_medecin', ['valide', 'rejete', 'a_revoir'])->nullable()->after('classe_finale_medecin');
            $table->text('recommandations_finales')->nullable()->after('decision_medecin');
            
            // Modifier l'enum statut pour ajouter "Rejeté"
            $table->dropColumn('statut');
        });
        
        // Recréer la colonne statut avec les nouvelles valeurs
        Schema::table('analyses_ia', function (Blueprint $table) {
            $table->enum('statut', ['Brouillon', 'En attente', 'Validé', 'À revoir', 'Rejeté'])
                  ->default('En attente')
                  ->after('temps_analyse');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('analyses_ia', function (Blueprint $table) {
            $table->dropColumn([
                'classe_finale_medecin',
                'decision_medecin', 
                'recommandations_finales'
            ]);
            
            // Restaurer l'ancien enum
            $table->dropColumn('statut');
        });
        
        Schema::table('analyses_ia', function (Blueprint $table) {
            $table->enum('statut', ['Brouillon', 'En attente', 'Validé', 'À revoir'])
                  ->default('En attente')
                  ->after('temps_analyse');
        });
    }
};