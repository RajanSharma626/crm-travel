<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::whereNull('deleted_at')->with('roles')->orderBy('created_at', 'desc')->get();
        $roles = Role::all();

        return view('users', compact('users', 'roles'));
    }

    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'user_id' => 'nullable|string|max:255|unique:users,user_id',
            'password' => 'required|string|min:6',
            'role' => 'required|string',
            'salutation' => 'nullable|in:Mr,Mrs,Miss,Ms,Dr,Prof',
            'first_name' => 'nullable|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'dob' => 'nullable|date',
            'phone' => 'nullable|string|max:20',
            'email' => 'required|email|unique:users',
            'address_line' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'pin_code' => 'nullable|string|max:20',
        ]);

        // Generate user_id if not provided
        if (empty($validated['user_id'])) {
            $validated['user_id'] = 'USER' . str_pad(User::max('id') + 1, 6, '0', STR_PAD_LEFT);
        }

        // Build full name from parts or use provided name
        $name = trim(($validated['first_name'] ?? '') . ' ' . ($validated['middle_name'] ?? '') . ' ' . ($validated['last_name'] ?? ''));
        if (empty($name)) {
            $name = $request->input('name', 'User');
        }

        // Create the user
        $user = User::create([
            'user_id' => $validated['user_id'],
            'name' => $name,
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'], // Keep for backward compatibility
            'status' => 'Active',
            'salutation' => $validated['salutation'] ?? null,
            'first_name' => $validated['first_name'] ?? null,
            'middle_name' => $validated['middle_name'] ?? null,
            'last_name' => $validated['last_name'] ?? null,
            'dob' => $validated['dob'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'address_line' => $validated['address_line'] ?? null,
            'city' => $validated['city'] ?? null,
            'state' => $validated['state'] ?? null,
            'country' => $validated['country'] ?? null,
            'pin_code' => $validated['pin_code'] ?? null,
        ]);

        // Assign role using Spatie
        $role = Role::where('name', $validated['role'])->first();
        if ($role) {
            $user->assignRole($role);
        }

        // Redirect back with a success message
        return redirect()->route('users')->with('success', 'User created successfully.');
    }

    public function edit($id)
    {
        $user = User::with('roles')->findOrFail($id);
        return response()->json([
            'id' => $user->id,
            'user_id' => $user->user_id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->roles->first()?->name ?? $user->role,
            'status' => $user->status,
            'salutation' => $user->salutation,
            'first_name' => $user->first_name,
            'middle_name' => $user->middle_name,
            'last_name' => $user->last_name,
            'dob' => $user->dob ? $user->dob->format('Y-m-d') : null,
            'phone' => $user->phone,
            'address_line' => $user->address_line,
            'city' => $user->city,
            'state' => $user->state,
            'country' => $user->country,
            'pin_code' => $user->pin_code,
        ]);
    }

    public function update(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'id' => 'required|exists:users,id',
            'user_id' => 'nullable|string|max:255|unique:users,user_id,' . $request->id,
            'password' => 'nullable|string|min:6',
            'role' => 'required|string',
            'salutation' => 'nullable|in:Mr,Mrs,Miss,Ms,Dr,Prof',
            'first_name' => 'nullable|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'dob' => 'nullable|date',
            'phone' => 'nullable|string|max:20',
            'email' => 'required|email|unique:users,email,' . $request->id,
            'address_line' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'pin_code' => 'nullable|string|max:20',
            'status' => 'required|in:Active,Deactive',
        ]);

        // Find the user
        $user = User::findOrFail($request->id);

        // Build full name from parts or use provided name
        $name = trim(($validated['first_name'] ?? '') . ' ' . ($validated['middle_name'] ?? '') . ' ' . ($validated['last_name'] ?? ''));
        if (empty($name)) {
            $name = $request->input('name', $user->name);
        }

        // Update the user's details
        $user->user_id = $validated['user_id'] ?? $user->user_id;
        $user->name = $name;
        $user->email = $validated['email'];
        $user->role = $validated['role']; // Keep for backward compatibility
        $user->status = $validated['status'];
        $user->salutation = $validated['salutation'] ?? null;
        $user->first_name = $validated['first_name'] ?? null;
        $user->middle_name = $validated['middle_name'] ?? null;
        $user->last_name = $validated['last_name'] ?? null;
        $user->dob = $validated['dob'] ?? null;
        $user->phone = $validated['phone'] ?? null;
        $user->address_line = $validated['address_line'] ?? null;
        $user->city = $validated['city'] ?? null;
        $user->state = $validated['state'] ?? null;
        $user->country = $validated['country'] ?? null;
        $user->pin_code = $validated['pin_code'] ?? null;

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        // Sync roles using Spatie
        $role = Role::where('name', $validated['role'])->first();
        if ($role) {
            $user->syncRoles([$role]);
        }

        // Redirect back with a success message
        return redirect()->route('users')->with('success', 'User updated successfully.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('users')->with('success', 'User deleted successfully.');
    }
}
