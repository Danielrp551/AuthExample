<?php

namespace Database\Seeders;

use App\Models\Authorization\RoleScope;
use App\Models\Authorization\Scope;
use App\Models\Authorization\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $scopes = [
            ['name' => 'Departamento', 'entity_type' => 'App\Models\Departamento'],
            ['name' => 'Facultad', 'entity_type' => 'App\Models\Facultad'],
            ['name' => 'Especialidad', 'entity_type' => 'App\Models\Especialidad'],
            ['name' => 'Seccion', 'entity_type' => 'App\Models\Seccion'],
            ['name' => 'Curso', 'entity_type' => 'App\Models\Curso'],
            ['name' => 'Area', 'entity_type' => 'App\Models\Area'],
        ];
        
        $roles = [
            'Administrador',
            'Asistente',
            'Secretario Académico',
            'Director de Carrera',
            'Coordinador',
            'Docente',
            'Jefe de Práctica',
            'Estudiante',
        ];

        foreach ($scopes as $scope) {
            Scope::firstOrCreate($scope);
        }

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        $role_director = Role::firstOrCreate(['name' => 'Director']);
        $role_director->syncPermissions([
            'ver especialidades',
            'crear especialidades',
            'editar especialidades',
            'eliminar especialidades',
        ]);

        $role_director->scopes()->attach(Scope::where('name', 'Facultad')->first());
    }
}
