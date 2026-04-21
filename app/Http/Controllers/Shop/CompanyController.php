<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CompanyInvitation;
use App\Models\User;
use App\Notifications\CompanyInvitationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\AnonymousNotifiable;

class CompanyController extends Controller
{
    /**
     * Company overview — members, settings, invitations.
     */
    public function index()
    {
        $user    = Auth::user();
        $company = $user->company;

        if (!$company) {
            return view('shop.company.create');
        }

        $members     = $company->members()->orderBy('company_role')->get();
        $invitations = $company->invitations()
            ->whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->get();

        return view('shop.company.index', compact('company', 'members', 'invitations'));
    }

    /**
     * Create a new company and make the current user its owner.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->company_id) {
            return back()->with('error', 'Ja pertanys a una empresa.');
        }

        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'cif_vat'     => ['nullable', 'string', 'max:30', 'unique:companies,cif_vat'],
            'email'       => ['nullable', 'email', 'max:255'],
            'phone'       => ['nullable', 'string', 'max:30'],
            'address'     => ['nullable', 'string', 'max:255'],
            'city'        => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country'     => ['nullable', 'string', 'max:5'],
        ]);

        $company = Company::create($validated);

        $user->update([
            'company_id'   => $company->id,
            'company_role' => 'owner',
        ]);

        return redirect()->route('company.index')
            ->with('success', 'Empresa creada correctament. Ara pots convidar membres.');
    }

    /**
     * Update company profile (owner/manager only).
     */
    public function update(Request $request)
    {
        $company = Auth::user()->company;
        abort_unless($company && Auth::user()->canManageCompany(), 403);

        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'email'       => ['nullable', 'email', 'max:255'],
            'phone'       => ['nullable', 'string', 'max:30'],
            'address'     => ['nullable', 'string', 'max:255'],
            'city'        => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country'     => ['nullable', 'string', 'max:5'],
        ]);

        $company->update($validated);

        return back()->with('success', 'Dades de l\'empresa actualitzades.');
    }

    /**
     * Send an invitation email.
     */
    public function invite(Request $request)
    {
        $user    = Auth::user();
        $company = $user->company;
        abort_unless($company && $user->canManageCompany(), 403);

        $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'role'  => ['required', 'in:manager,buyer,viewer'],
        ]);

        $email = strtolower(trim($request->email));

        // Cannot invite existing members
        $alreadyMember = $company->members()->where('email', $email)->exists();
        if ($alreadyMember) {
            return back()->withErrors(['email' => 'Aquest usuari ja és membre de l\'empresa.']);
        }

        $invitation = CompanyInvitation::generate($company, $email, $request->role);

        // Notify via email (works for both existing and new users)
        \Notification::route('mail', $email)
            ->notify(new CompanyInvitationNotification($invitation));

        return back()->with('success', 'Invitació enviada a ' . $email . '.');
    }

    /**
     * Show the invitation acceptance page (public, no auth required).
     */
    public function showInvitation(string $token)
    {
        $invitation = CompanyInvitation::where('token', $token)->firstOrFail();

        if (!$invitation->isValid()) {
            return view('shop.company.invitation_expired');
        }

        $existingUser = User::where('email', $invitation->email)->first();

        return view('shop.company.invitation', compact('invitation', 'existingUser'));
    }

    /**
     * Accept the invitation (must be logged in as the invited email or just registered).
     */
    public function acceptInvitation(Request $request, string $token)
    {
        $invitation = CompanyInvitation::where('token', $token)->firstOrFail();

        if (!$invitation->isValid()) {
            return redirect()->route('home')->with('error', 'La invitació ha caducat.');
        }

        $user = Auth::user();

        if (!$user || strtolower($user->email) !== $invitation->email) {
            return redirect()->route('login')
                ->with('info', 'Inicia sessió amb el compte ' . $invitation->email . ' per acceptar la invitació.')
                ->with('invitation_token', $token);
        }

        if ($user->company_id) {
            return redirect()->route('company.index')
                ->with('error', 'Ja pertanys a una empresa. Abandona-la primer per acceptar aquesta invitació.');
        }

        $user->update([
            'company_id'   => $invitation->company_id,
            'company_role' => $invitation->role,
        ]);

        $invitation->update(['accepted_at' => now()]);

        return redirect()->route('company.index')
            ->with('success', 'T\'has unit a **' . $invitation->company->name . '** correctament!');
    }

    /**
     * Remove a member from the company (owner only, can't remove self).
     */
    public function removeMember(User $member)
    {
        $user    = Auth::user();
        $company = $user->company;

        abort_unless($company && $user->isCompanyOwner(), 403);
        abort_if($member->id === $user->id, 403);
        abort_unless($member->company_id === $company->id, 403);

        $member->update(['company_id' => null, 'company_role' => null]);

        return back()->with('success', $member->name . ' ha estat eliminat/da de l\'empresa.');
    }

    /**
     * Change a member's role (owner only).
     */
    public function updateMemberRole(Request $request, User $member)
    {
        $user    = Auth::user();
        $company = $user->company;

        abort_unless($company && $user->isCompanyOwner(), 403);
        abort_if($member->id === $user->id, 403);
        abort_unless($member->company_id === $company->id, 403);

        $request->validate([
            'role'           => ['required', 'in:manager,buyer,viewer'],
            'spending_limit' => ['nullable', 'numeric', 'min:0'],
        ]);

        $member->update([
            'company_role'  => $request->role,
            'spending_limit' => $request->spending_limit ?: null,
        ]);

        return back()->with('success', 'Rol de ' . $member->name . ' actualitzat.');
    }

    /**
     * Leave the company (non-owners only).
     */
    public function leave()
    {
        $user = Auth::user();
        abort_if($user->isCompanyOwner(), 403, 'El propietari no pot abandonar l\'empresa. Transfereix la propietat primer.');
        abort_unless($user->company_id, 403);

        $user->update(['company_id' => null, 'company_role' => null]);

        return redirect()->route('dashboard')
            ->with('success', 'Has abandonat l\'empresa correctament.');
    }
}
