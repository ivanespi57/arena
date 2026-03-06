<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sectores', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('asientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sector_id')->constrained('sectores')->onDelete('cascade');
            $table->string('fila');
            $table->integer('numero');
            $table->unique(['sector_id', 'fila', 'numero']);
            $table->timestamps();
        });

        Schema::create('eventos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('descripcion_corta', 255);
            $table->text('descripcion_larga');
            $table->string('poster_url')->nullable();
            $table->date('fecha')->unique();
            $table->time('hora')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('precios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evento_id')->constrained('eventos')->onDelete('cascade');
            $table->foreignId('sector_id')->constrained('sectores')->onDelete('cascade');
            $table->decimal('precio', 10, 2);
            $table->boolean('disponible')->default(true);
            $table->unique(['evento_id', 'sector_id']);
            $table->timestamps();
        });

        Schema::create('estado_asientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evento_id')->constrained('eventos')->onDelete('cascade');
            $table->foreignId('asiento_id')->constrained('asientos')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('estado', ['bloqueado', 'vendido']);
            $table->timestamp('reservado_hasta')->nullable();
            $table->unique(['evento_id', 'asiento_id']);
            $table->timestamps();
        });

        Schema::create('entradas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('evento_id')->constrained('eventos')->onDelete('cascade');
            $table->foreignId('asiento_id')->constrained('asientos')->onDelete('cascade');
            $table->decimal('precio_pagado', 10, 2);
            $table->string('codigo_qr')->unique();
            $table->unique(['evento_id', 'asiento_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entradas');
        Schema::dropIfExists('estado_asientos');
        Schema::dropIfExists('precios');
        Schema::dropIfExists('eventos');
        Schema::dropIfExists('asientos');
        Schema::dropIfExists('sectores');
    }
};
