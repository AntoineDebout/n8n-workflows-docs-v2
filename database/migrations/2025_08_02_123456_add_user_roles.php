<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        // Insertion des rôles par défaut
        DB::table('user_roles')->insert([
            [
                'id' => 1,
                'name' => 'Utilisateur',
                'slug' => 'user',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Administrateur',
                'slug' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('user_role_id')->after('email')->default(1)->constrained('user_roles');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_role_id');
        });
        
        Schema::dropIfExists('user_roles');
    }
};
