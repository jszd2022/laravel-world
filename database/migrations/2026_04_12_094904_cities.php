<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create(config('laravel-world.migrations.cities.table_name'), function (Blueprint $table) {
            $table->id();

            $table->foreignId('country_id');
            $table->foreignId('state_id');

            $table->string('country_code');
            $table->string('name');
        });
    }

    public function down(): void {
        Schema::dropIfExists(config('laravel-world.migrations.cities.table_name'));
    }
};
