<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Terminal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Muestra la lista de usuarios. (READ)
     */
    public function index()
    {
        // Obtenemos todos los usuarios, cargando sus relaciones de 'role' y 'terminal'
        // Esto es para evitar el problema de "N+1 queries" (es más eficiente)
        $users = User::with(['role', 'terminal'])->get();

        // Enviamos los usuarios a la vista
        return view('admin.users.index', compact('users'));
    }

    /**
     * Muestra el formulario para crear un nuevo usuario. (CREATE)
     */
    public function create()
    {
        // Obtenemos todos los roles y terminales para los menús desplegables
        $roles = Role::all();
        $terminals = Terminal::all();

        return view('admin.users.create', compact('roles', 'terminals'));
    }

    /**
     * Guarda el nuevo usuario en la base de datos. (CREATE)
     */
    public function store(Request $request)
    {
        // 1. Validar la información del formulario
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role_id' => ['required', 'exists:roles,id'], // 'exists' valida que el ID exista en la tabla 'roles'
            'terminal_id' => ['nullable', 'exists:terminals,id'], // 'nullable' si es admin, por ejemplo
        ]);

        // 2. Crear el usuario
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // ¡NUNCA guardar contraseñas sin encriptar!
            'role_id' => $request->role_id,
            'terminal_id' => $request->terminal_id,
        ]);

        // 3. Redirigir de vuelta a la lista con un mensaje de éxito
        return redirect()->route('users.index')->with('success', 'Usuario creado exitosamente.');
    }

    /**
     * Muestra el formulario para editar un usuario. (UPDATE)
     */
    public function edit(User $user) // Laravel automáticamente busca el usuario por su ID
    {
        $roles = Role::all();
        $terminals = Terminal::all();

        return view('admin.users.edit', compact('user', 'roles', 'terminals'));
    }

    /**
     * Actualiza el usuario en la base de datos. (UPDATE)
     */
    public function update(Request $request, User $user)
    {
        // 1. Validar la información
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class.',email,'.$user->id], // Ignora el email del propio usuario
            'role_id' => ['required', 'exists:roles,id'],
            'terminal_id' => ['nullable', 'exists:terminals,id'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()], // 'nullable' para no cambiarla si no se quiere
        ]);

        // 2. Actualizar los datos
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'terminal_id' => $request->terminal_id,
        ]);

        // 3. Si se proporcionó una nueva contraseña, actualizarla
        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        // 4. Redirigir
        return redirect()->route('users.index')->with('success', 'Usuario actualizado exitosamente.');
    }

    /**
     * Elimina un usuario de la base de datos. (DELETE)
     */
    public function destroy(User $user)
    {
        // No te dejes borrar a ti mismo (como admin)
       if ($user->id === Auth::id()) {
            return redirect()->route('users.index')->with('error', 'No puedes eliminarte a ti mismo.');
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'Usuario eliminado exitosamente.');
    }
}