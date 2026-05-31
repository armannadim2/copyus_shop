<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\UserApprovedNotification;
use App\Notifications\UserRejectedNotification;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $allowed = ['name', 'email', 'company_name', 'role', 'created_at'];
        $sort    = in_array($request->input('sort'), $allowed) ? $request->input('sort') : 'created_at';
        $dir     = $request->input('direction', 'desc') === 'asc' ? 'asc' : 'desc';

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
            ->orderBy($sort, $dir)
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'sort', 'dir'));
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

        $user->notify((new UserApprovedNotification())->locale($user->locale ?? 'ca'));

        return back()->with('success', "Usuari {$user->name} aprovat correctament. ✅");
    }

    public function reject(int $id, Request $request)
    {
        $user = User::findOrFail($id);
        $user->update([
            'role'        => 'rejected',
            'approved_at' => null,
        ]);

        $user->notify(
            (new UserRejectedNotification($request->input('reason', '')))->locale($user->locale ?? 'ca')
        );

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
