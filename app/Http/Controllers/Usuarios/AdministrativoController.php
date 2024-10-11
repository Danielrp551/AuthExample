<?php

namespace App\Http\Controllers\Usuarios;

use App\Http\Controllers\Controller;
use App\Models\Administrativo;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdministrativoController extends Controller
{
    public function index()
    {
        $per_page = request('per_page', 10);
        $search = request('search', '');

        $administrativos = Administrativo::with('usuario')
            ->whereHas('usuario', function ($query) use ($search) {
                $query->where('nombre', 'like', '%' . $search . '%')
                    ->orWhere('apellido_paterno', 'like', '%' . $search . '%')
                    ->orWhere('apellido_materno', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            })
            ->orWhere('codigoAdministrativo', 'like', '%' . $search . '%')
            ->paginate($per_page);

        return response()->json($administrativos, 200);
    }

    public function show($codigo)
    {
        $administrativo = Administrativo::with('usuario')->where('codigoAdministrativo', $codigo)->first();
        if (!$administrativo) {
            return response()->json(['message' => 'Administrativo no encontrado'], 404);
        }
        return response()->json($administrativo, 200);
    }

    public function update(Request $request, $codigo)
    {
        $administrativo = Administrativo::with('usuario')->where('codigoAdministrativo', $codigo)->first();
        if (!$administrativo) {
            return response()->json(['message' => 'Administrativo no encontrado'], 404);
        }

        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido_paterno' => 'nullable|string|max:255',
            'apellido_materno' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:usuarios,email,' . $administrativo->usuario_id,
            'password' => 'nullable|string|min:8',
            'lugarTrabajo' => 'required|string|max:255',
            'cargo' => 'required|string|max:255',
            'codigoAdministrativo' => 'required|string|max:50|unique:administrativos,codigoAdministrativo,' . $administrativo->id,
        ]);

        DB::transaction(function () use ($validatedData, $administrativo) {
            $usuarioData = [
                'nombre' => $validatedData['nombre'],
                'apellido_paterno' => $validatedData['apellido_paterno'],
                'apellido_materno' => $validatedData['apellido_materno'],
                'email' => $validatedData['email'],
            ];
            if (!empty($validatedData['password'])) {
                $usuarioData['password'] = Hash::make($validatedData['password']);
            }
            $administrativo->usuario->update($usuarioData);
            $administrativo->update([
                'lugarTrabajo' => $validatedData['lugarTrabajo'],
                'cargo' => $validatedData['cargo'],
                'codigoAdministrativo' => $validatedData['codigoAdministrativo'],
            ]);
        });

        return response()->json([
            'message' => 'Administrativo actualizado exitosamente',
        ], 200);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido_paterno' => 'nullable|string|max:255',
            'apellido_materno' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:usuarios,email',
            'codigoAdministrativo' => 'required|string|max:50|unique:administrativos,codigoAdministrativo',
            'lugarTrabajo' => 'required|string|max:255',
            'cargo' => 'required|string|max:255',
        ]);
        $usuario = Usuario::firstOrCreate(
            ['email' => $validatedData['email']],
            [
                'nombre' => $validatedData['nombre'],
                'apellido_paterno' => $validatedData['apellido_paterno'],
                'apellido_materno' => $validatedData['apellido_materno'],
                'password' => Hash::make($validatedData['codigoAdministrativo']),
            ]
        );
        $administrativo = new Administrativo();
        $administrativo->usuario_id = $usuario->id;
        $administrativo->codigoAdministrativo = $validatedData['codigoAdministrativo'];
        $administrativo->lugarTrabajo = $validatedData['lugarTrabajo'];
        $administrativo->cargo = $validatedData['cargo'];
        $usuario->administrativo()->save($administrativo);
        return response()->json(['message' => 'Administrativo creado exitosamente', 'administrativo' => $administrativo], 201);
    }

    public function destroy($codigo)
    {
        $administrativo = Administrativo::where('codigoAdministrativo', $codigo)->first();
        if (!$administrativo) {
            return response()->json(['message' => 'Administrativo no encontrado'], 404);
        }
        $administrativo->delete();
        return response()->json(['message' => 'Administrativo eliminado exitosamente'], 200);
    }


    public function massStore(Request $request)
    {
        $request->validate([
            'administrativos' => 'required|array',
            'administrativos.*.nombre' => 'required|string|max:255',
            'administrativos.*.apellido_paterno' => 'nullable|string|max:255',
            'administrativos.*.apellido_materno' => 'nullable|string|max:255',
            'administrativos.*.email' => 'required|email',
            'administrativos.*.lugarTrabajo' => 'required|string|max:255',
            'administrativos.*.cargo' => 'required|string|max:255',
            'administrativos.*.codigoAdministrativo' => 'required|string|max:50|unique:administrativos,codigoAdministrativo',
        ]);
        DB::beginTransaction();
        try {
            foreach ($request->administrativos as $administrativoData) {
                $usuario = Usuario::firstOrCreate(
                    ['email' => $administrativoData['email']],
                    [
                        'nombre' => $administrativoData['nombre'],
                        'apellido_paterno' => $administrativoData['apellido_paterno'],
                        'apellido_materno' => $administrativoData['apellido_materno'],
                        'password' => Hash::make($administrativoData['codigoAdministrativo']),
                    ]
                );

                Administrativo::create([
                    'usuario_id' => $usuario->id,
                    'codigoAdministrativo' => $administrativoData['codigoAdministrativo'],
                    'lugarTrabajo' => $administrativoData['lugarTrabajo'],
                    'cargo' => $administrativoData['cargo'],
                ]);
            }
            DB::commit();
            return response()->json(['message' => 'Administrativos creados exitosamente'], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al crear administrativos', 'error' => $e->getMessage()], 500);
        }
    }
}
