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
            $table->timestamp('date_derniere_modification')->nullable()->after('date_validation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('analyses_ia', function (Blueprint $table) {
            $table->dropColumn('date_derniere_modification');
        });
    }
};
