<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\UserApprovedNotification;
use App\Notifications\UserRejectedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::where('role', '!=', 'admin')
            ->when($request->status, fn($q, $s) => $q->where('role', $s))
            ->when(
                $request->search,
                fn($q, $s) =>
                $q->where('company_name', 'like', "%$s%")
                    ->orWhere('email', 'like', "%$s%")
                    ->orWhere('cif', 'like', "%$s%")
            )
            ->latest()
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load(['orders', 'quotations', 'invoices']);
        return view('admin.users.show', compact('user'));
    }

    public function approve(User $user)
    {
        $user->update([
            'role'        => 'approved',
            'approved_at' => now(),
            'approved_by' => Auth::id(),
            'rejection_reason' => null,
        ]);

        $user->notify(new UserApprovedNotification());

        return back()->with('success', __('app.user_approved_success'));
    }

    public function reject(Request $request, User $user)
    {
        $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $user->update([
            'role'             => 'rejected',
            'rejection_reason' => $request->reason,
        ]);

        $user->notify(new UserRejectedNotification($request->reason ?? ''));

        return back()->with('success', __('app.user_rejected_success'));
    }
}
