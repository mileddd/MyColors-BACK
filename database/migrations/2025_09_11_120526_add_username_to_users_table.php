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
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique();
        });

        DB::table('users')->insert([
            [
                'name'     => 'Alaa',
                'username' => 'alaa',
                'password' => hash('sha256', '123456'),
                'email'    => 'alaa@gmail.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'     => 'miled',
                'username' => 'miled',
                'password' => hash('sha256', '123456'),
                'email'    => 'miled@gmail.com',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
