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
        Schema::create('estudiante_riesgo', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_estudiante');
            $table->unsignedBigInteger('codigo_curso');
            $table->string('codigo_docente')->nullable();
            $table->unsignedBigInteger('codigo_especialidad');
            $table->string('horario')->nullable();
            $table->string('riesgo')->nullable();
            $table->date('fecha')->nullable();
            $table->string('ciclo')->nullable();
            $table->string('estado')->default('Activo');
            $table->string('desempenho')->nullable();
            $table->string('nombre')->nullable();
            $table->string('observaciones')->nullable();
            $table->timestamps();

            // Definición de llaves foráneas
            $table->foreign('codigo_estudiante')->references('codigoEstudiante')->on('estudiantes')->onDelete('cascade');
            $table->foreign('codigo_curso')->references('id')->on('cursos')->onDelete('cascade');
            $table->foreign('codigo_docente')->references('codigoDocente')->on('docentes')->onDelete('cascade');
            $table->foreign('codigo_especialidad')->references('id')->on('especialidades')->onDelete('cascade');
            $table->unique(['codigo_estudiante', 'codigo_curso', 'ciclo'], 'unique_estudiante_curso_ciclo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estudiante_riesgo');
    }
};
