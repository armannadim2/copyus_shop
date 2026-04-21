<?php

namespace Tests\Feature\Shop;

use App\Models\SavedAddress;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SavedAddressTest extends TestCase
{
    use RefreshDatabase;

    private function addressPayload(array $overrides = []): array
    {
        return array_merge([
            'label'       => 'Main Office',
            'address'     => 'Carrer Gran 10',
            'city'        => 'Barcelona',
            'postal_code' => '08001',
            'country'     => 'ES',
        ], $overrides);
    }

    private function makeAddress(User $user, array $overrides = []): SavedAddress
    {
        return SavedAddress::create(array_merge([
            'user_id'     => $user->id,
            'label'       => 'Office',
            'address'     => 'Carrer Test 1',
            'city'        => 'Barcelona',
            'postal_code' => '08001',
            'country'     => 'ES',
            'is_default'  => false,
        ], $overrides));
    }

    // -------------------------------------------------------
    // Access Control
    // -------------------------------------------------------

        public function test_guest_cannot_view_saved_addresses(): void
    {
        $this->get(route('addresses.index'))
             ->assertRedirect(route('login'));
    }

        public function test_pending_user_cannot_view_saved_addresses(): void
    {
        $user = User::factory()->pending()->create();

        $this->actingAs($user)
             ->get(route('addresses.index'))
             ->assertForbidden();
    }

        public function test_approved_user_can_view_saved_addresses(): void
    {
        $user = User::factory()->approved()->create();

        $this->actingAs($user)
             ->get(route('addresses.index'))
             ->assertOk();
    }

    // -------------------------------------------------------
    // Store
    // -------------------------------------------------------

        public function test_approved_user_can_save_a_new_address(): void
    {
        $user = User::factory()->approved()->create();

        $this->actingAs($user)
             ->post(route('addresses.store'), $this->addressPayload())
             ->assertRedirect();

        $this->assertDatabaseHas('saved_addresses', [
            'user_id' => $user->id,
            'label'   => 'Main Office',
            'city'    => 'Barcelona',
        ]);
    }

        public function test_address_store_requires_label_address_city_postal_country(): void
    {
        $user = User::factory()->approved()->create();

        $this->actingAs($user)
             ->post(route('addresses.store'), [])
             ->assertSessionHasErrors(['label', 'address', 'city', 'postal_code', 'country']);
    }

        public function test_setting_new_default_clears_old_default(): void
    {
        $user = User::factory()->approved()->create();
        $old  = $this->makeAddress($user, ['is_default' => true]);

        $this->actingAs($user)
             ->post(route('addresses.store'), $this->addressPayload(['is_default' => true]))
             ->assertRedirect();

        $this->assertDatabaseHas('saved_addresses', [
            'id'         => $old->id,
            'is_default' => false,
        ]);
    }

    // -------------------------------------------------------
    // Update
    // -------------------------------------------------------

        public function test_approved_user_can_update_their_own_address(): void
    {
        $user    = User::factory()->approved()->create();
        $address = $this->makeAddress($user);

        $this->actingAs($user)
             ->put(route('addresses.update', $address->id), $this->addressPayload(['city' => 'Girona']))
             ->assertRedirect();

        $this->assertDatabaseHas('saved_addresses', [
            'id'   => $address->id,
            'city' => 'Girona',
        ]);
    }

        public function test_user_cannot_update_another_users_address(): void
    {
        $user1   = User::factory()->approved()->create();
        $user2   = User::factory()->approved()->create();
        $address = $this->makeAddress($user2);

        $this->actingAs($user1)
             ->put(route('addresses.update', $address->id), $this->addressPayload())
             ->assertNotFound();
    }

    // -------------------------------------------------------
    // Set Default
    // -------------------------------------------------------

        public function test_user_can_set_an_address_as_default(): void
    {
        $user    = User::factory()->approved()->create();
        $address = $this->makeAddress($user, ['is_default' => false]);

        $this->actingAs($user)
             ->patch(route('addresses.default', $address->id))
             ->assertRedirect();

        $this->assertDatabaseHas('saved_addresses', [
            'id'         => $address->id,
            'is_default' => true,
        ]);
    }

        public function test_setting_default_clears_previous_default(): void
    {
        $user  = User::factory()->approved()->create();
        $old   = $this->makeAddress($user, ['is_default' => true]);
        $new   = $this->makeAddress($user, ['is_default' => false, 'label' => 'Warehouse']);

        $this->actingAs($user)
             ->patch(route('addresses.default', $new->id))
             ->assertRedirect();

        $this->assertDatabaseHas('saved_addresses', ['id' => $old->id, 'is_default' => false]);
        $this->assertDatabaseHas('saved_addresses', ['id' => $new->id, 'is_default' => true]);
    }

    // -------------------------------------------------------
    // Delete
    // -------------------------------------------------------

        public function test_user_can_delete_their_own_address(): void
    {
        $user    = User::factory()->approved()->create();
        $address = $this->makeAddress($user);

        $this->actingAs($user)
             ->delete(route('addresses.destroy', $address->id))
             ->assertRedirect();

        $this->assertDatabaseMissing('saved_addresses', ['id' => $address->id]);
    }

        public function test_user_cannot_delete_another_users_address(): void
    {
        $user1   = User::factory()->approved()->create();
        $user2   = User::factory()->approved()->create();
        $address = $this->makeAddress($user2);

        $this->actingAs($user1)
             ->delete(route('addresses.destroy', $address->id))
             ->assertNotFound();

        $this->assertDatabaseHas('saved_addresses', ['id' => $address->id]);
    }
}
