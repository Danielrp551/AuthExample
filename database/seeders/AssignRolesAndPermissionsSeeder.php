<?php

namespace Database\Seeders;

use App\Models\Usuario;
use App\Models\Docente;
use App\Models\Administrativo;
use App\Models\Estudiante;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class AssignRolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $docenteRole = Role::findByName('docente');
        $secretarioRole = Role::findByName('secretarioAcademico');

        Docente::all()->each(function ($docente) use ($docenteRole, $secretarioRole) {
            $usuario = $docente->usuario;
            if ($usuario) {
                $usuario->assignRole($docenteRole);
                if (rand(0, 100) < 30) {
                    $usuario->assignRole($secretarioRole);
                }
            }
        });

        $administrativoRole = Role::findByName('administrativo');
        Administrativo::all()->each(function ($administrativo) use ($administrativoRole) {
            $usuario = $administrativo->usuario;
            if ($usuario) {
                $usuario->assignRole($administrativoRole);
            }
        });

        $estudianteRole = Role::findByName('estudiante');
        $jefePracticaRole = Role::findByName('jefePractica');
        Estudiante::all()->each(function ($estudiante) use ($estudianteRole, $jefePracticaRole) {
            $usuario = $estudiante->usuario;
            if ($usuario) {
                $usuario->assignRole([$estudianteRole, $jefePracticaRole]);
            }
        });
    }
}