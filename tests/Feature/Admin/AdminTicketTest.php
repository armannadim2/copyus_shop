<?php

namespace Tests\Feature\Admin;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminTicketTest extends TestCase
{
    use RefreshDatabase;

    private function makeTicket(User $user, array $overrides = []): Ticket
    {
        return Ticket::create(array_merge([
            'ticket_number' => 'TKT-' . strtoupper(Str::random(6)),
            'user_id'       => $user->id,
            'subject'       => 'Test subject',
            'body'          => 'Test body',
            'status'        => 'open',
            'priority'      => 'medium',
        ], $overrides));
    }

    // -------------------------------------------------------
    // Access Control
    // -------------------------------------------------------

        public function test_guest_cannot_access_admin_tickets(): void
    {
        $this->get(route('admin.tickets.index'))
             ->assertRedirect(route('login'));
    }

        public function test_b2b_user_cannot_access_admin_tickets(): void
    {
        $user = User::factory()->approved()->create();

        $this->actingAs($user)
             ->get(route('admin.tickets.index'))
             ->assertForbidden();
    }

        public function test_admin_can_view_tickets_list(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
             ->get(route('admin.tickets.index'))
             ->assertOk();
    }

    // -------------------------------------------------------
    // View Ticket
    // -------------------------------------------------------

        public function test_admin_can_view_any_ticket(): void
    {
        $admin  = User::factory()->admin()->create();
        $user   = User::factory()->approved()->create();
        $ticket = $this->makeTicket($user);

        $this->actingAs($admin)
             ->get(route('admin.tickets.show', $ticket->id))
             ->assertOk();
    }

    // -------------------------------------------------------
    // Admin Reply
    // -------------------------------------------------------

        public function test_admin_can_reply_to_any_ticket(): void
    {
        $admin  = User::factory()->admin()->create();
        $user   = User::factory()->approved()->create();
        $ticket = $this->makeTicket($user);

        $this->actingAs($admin)
             ->post(route('admin.tickets.reply', $ticket->id), [
                 'body' => 'We are looking into this.',
             ])->assertRedirect();

        $this->assertDatabaseHas('ticket_replies', [
            'ticket_id'      => $ticket->id,
            'user_id'        => $admin->id,
            'is_admin_reply' => true,
        ]);
    }

        public function test_admin_reply_requires_body(): void
    {
        $admin  = User::factory()->admin()->create();
        $user   = User::factory()->approved()->create();
        $ticket = $this->makeTicket($user);

        $this->actingAs($admin)
             ->post(route('admin.tickets.reply', $ticket->id), [])
             ->assertSessionHasErrors('body');
    }

    // -------------------------------------------------------
    // Resolve Ticket
    // -------------------------------------------------------

        public function test_admin_can_resolve_a_ticket(): void
    {
        $admin  = User::factory()->admin()->create();
        $user   = User::factory()->approved()->create();
        $ticket = $this->makeTicket($user);

        $this->actingAs($admin)
             ->patch(route('admin.tickets.status', $ticket->id), ['status' => 'resolved'])
             ->assertRedirect();

        $this->assertDatabaseHas('tickets', [
            'id'     => $ticket->id,
            'status' => 'resolved',
        ]);
    }

    // -------------------------------------------------------
    // Filter Tickets
    // -------------------------------------------------------

        public function test_admin_can_filter_tickets_by_status(): void
    {
        $admin = User::factory()->admin()->create();
        $user  = User::factory()->approved()->create();

        $this->makeTicket($user, ['status' => 'open']);
        $this->makeTicket($user, ['status' => 'resolved']);

        $this->actingAs($admin)
             ->get(route('admin.tickets.index', ['status' => 'open']))
             ->assertOk()
             ->assertViewHas('tickets');
    }
}
