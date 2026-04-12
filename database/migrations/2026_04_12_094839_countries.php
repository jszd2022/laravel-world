<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create(config('laravel-world.migrations.countries.table_name'), function (Blueprint $table) {
            $table->id();

            $table->string('iso2', 2);
            $table->string('name');
            $table->string('phone_code', 5);
            $table->tinyInteger('status')->default(1);
        });
    }

    public function down(): void {
        Schema::dropIfExists('');
    }
};
