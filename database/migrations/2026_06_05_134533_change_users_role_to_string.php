<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Convertit l'enum en VARCHAR pour permettre l'ajout de profils
        // (surveillant, censeur, caissier...) sans nouvelle migration.
        DB::statement("ALTER TABLE users MODIFY role VARCHAR(30) NOT NULL DEFAULT 'secretaire'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY role ENUM('admin','directeur','comptable','secretaire') NOT NULL DEFAULT 'secretaire'");
    }
};
