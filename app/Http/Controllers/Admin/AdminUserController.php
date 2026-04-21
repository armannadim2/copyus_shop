<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::where('role', '!=', 'admin')
            ->when(
                $request->search,
                fn($q, $s) =>
                $q->where('name', 'like', "%$s%")
                    ->orWhere('email', 'like', "%$s%")
                    ->orWhere('company_name', 'like', "%$s%")
                    ->orWhere('cif', 'like', "%$s%")
            )
            ->when($request->role, fn($q, $r) => $q->where('role', $r))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function pending()
    {
        $users = User::where('role', 'pending')
            ->latest()
            ->paginate(20);

        return view('admin.users.pending', compact('users'));
    }

    public function show(int $id)
    {
        $user = User::with(['orders', 'quotations'])
            ->findOrFail($id);

        return view('admin.users.show', compact('user'));
    }

    public function approve(int $id)
    {
        $user = User::findOrFail($id);
        $user->update([
            'role'        => 'approved',
            'approved_at' => now(),
        ]);

        return back()->with('success', "Usuari {$user->name} aprovat correctament. ✅");
    }

    public function reject(int $id)
    {
        $user = User::findOrFail($id);
        $user->update([
            'role'        => 'rejected',
            'approved_at' => null,
        ]);

        return back()->with('error', "Usuari {$user->name} rebutjat.");
    }

    public function destroy(int $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuari eliminat correctament.');
    }
}
