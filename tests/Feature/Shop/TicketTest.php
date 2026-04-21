<?php

namespace Tests\Feature\Shop;

use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class TicketTest extends TestCase
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

        public function test_guest_cannot_access_tickets(): void
    {
        $this->get(route('tickets.index'))
             ->assertRedirect(route('login'));
    }

        public function test_pending_user_cannot_access_tickets(): void
    {
        $user = User::factory()->pending()->create();

        $this->actingAs($user)
             ->get(route('tickets.index'))
             ->assertForbidden();
    }

        public function test_approved_user_can_view_tickets_list(): void
    {
        $user = User::factory()->approved()->create();

        $this->actingAs($user)
             ->get(route('tickets.index'))
             ->assertOk();
    }

    // -------------------------------------------------------
    // Create Ticket
    // -------------------------------------------------------

        public function test_approved_user_can_view_create_ticket_form(): void
    {
        $user = User::factory()->approved()->create();

        $this->actingAs($user)
             ->get(route('tickets.create'))
             ->assertOk();
    }

        public function test_approved_user_can_create_a_ticket(): void
    {
        $user = User::factory()->approved()->create();

        $this->actingAs($user)
             ->post(route('tickets.store'), [
                 'subject'  => 'Order issue',
                 'body'     => 'I have a problem with my order.',
                 'priority' => 'high',
             ])->assertRedirect();

        $this->assertDatabaseHas('tickets', [
            'user_id' => $user->id,
            'subject' => 'Order issue',
            'status'  => 'open',
        ]);
    }

        public function test_ticket_creation_requires_subject_and_body(): void
    {
        $user = User::factory()->approved()->create();

        $this->actingAs($user)
             ->post(route('tickets.store'), [])
             ->assertSessionHasErrors(['subject', 'body']);
    }

        public function test_ticket_number_is_generated_on_creation(): void
    {
        $user = User::factory()->approved()->create();

        $this->actingAs($user)
             ->post(route('tickets.store'), [
                 'subject'  => 'Question',
                 'body'     => 'How does this work?',
                 'priority' => 'low',
             ]);

        $ticket = Ticket::where('user_id', $user->id)->first();
        $this->assertNotNull($ticket->ticket_number);
    }

    // -------------------------------------------------------
    // View Ticket
    // -------------------------------------------------------

        public function test_user_can_view_their_own_ticket(): void
    {
        $user   = User::factory()->approved()->create();
        $ticket = $this->makeTicket($user);

        $this->actingAs($user)
             ->get(route('tickets.show', $ticket->id))
             ->assertOk();
    }

        public function test_user_cannot_view_another_users_ticket(): void
    {
        $user1  = User::factory()->approved()->create();
        $user2  = User::factory()->approved()->create();
        $ticket = $this->makeTicket($user2);

        $this->actingAs($user1)
             ->get(route('tickets.show', $ticket->id))
             ->assertNotFound();
    }

    // -------------------------------------------------------
    // Reply to Ticket
    // -------------------------------------------------------

        public function test_user_can_reply_to_their_own_ticket(): void
    {
        $user   = User::factory()->approved()->create();
        $ticket = $this->makeTicket($user);

        $this->actingAs($user)
             ->post(route('tickets.reply', $ticket->id), [
                 'body' => 'Here is more information.',
             ])->assertRedirect();

        $this->assertDatabaseHas('ticket_replies', [
            'ticket_id'      => $ticket->id,
            'user_id'        => $user->id,
            'is_admin_reply' => false,
        ]);
    }

        public function test_reply_requires_body(): void
    {
        $user   = User::factory()->approved()->create();
        $ticket = $this->makeTicket($user);

        $this->actingAs($user)
             ->post(route('tickets.reply', $ticket->id), [])
             ->assertSessionHasErrors('body');
    }

        public function test_user_cannot_reply_to_another_users_ticket(): void
    {
        $user1  = User::factory()->approved()->create();
        $user2  = User::factory()->approved()->create();
        $ticket = $this->makeTicket($user2);

        $this->actingAs($user1)
             ->post(route('tickets.reply', $ticket->id), ['body' => 'Hello'])
             ->assertNotFound();
    }

    // -------------------------------------------------------
    // Close Ticket
    // -------------------------------------------------------

        public function test_user_can_close_their_own_ticket(): void
    {
        $user   = User::factory()->approved()->create();
        $ticket = $this->makeTicket($user);

        $this->actingAs($user)
             ->patch(route('tickets.close', $ticket->id))
             ->assertRedirect();

        $this->assertDatabaseHas('tickets', [
            'id'     => $ticket->id,
            'status' => 'closed',
        ]);
    }
}
