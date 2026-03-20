<?php

namespace Tests\Feature\Admin;

use App\Models\NewsletterSubscriber;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class NewsletterSubscriberManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup permissions and roles
        $permissions = ['view newsletter subscribers', 'delete newsletter subscribers'];
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions($permissions);
    }

    public function test_unauthenticated_user_cannot_access_page()
    {
        $this->get(route('admin.newsletter-subscribers'))
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_user_without_permission_cannot_access_page()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.newsletter-subscribers'))
            ->assertForbidden();
    }

    public function test_admin_can_access_page()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->get(route('admin.newsletter-subscribers'))
            ->assertSuccessful()
            ->assertSeeLivewire('admin::newsletter-subscribers');
    }

    public function test_subscribers_are_listed()
    {
        NewsletterSubscriber::create(['email' => 'test1@example.com']);
        NewsletterSubscriber::create(['email' => 'test2@example.com']);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        Livewire::actingAs($admin)
            ->test('admin::newsletter-subscribers')
            ->assertSee('test1@example.com')
            ->assertSee('test2@example.com');
    }

    public function test_subscribers_can_be_searched()
    {
        NewsletterSubscriber::create(['email' => 'apple@example.com']);
        NewsletterSubscriber::create(['email' => 'banana@example.com']);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        Livewire::actingAs($admin)
            ->test('admin::newsletter-subscribers')
            ->set('search', 'apple')
            ->assertSee('apple@example.com')
            ->assertDontSee('banana@example.com');
    }

    public function test_admin_can_delete_subscriber()
    {
        $subscriber = NewsletterSubscriber::create(['email' => 'delete_me@example.com']);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        Livewire::actingAs($admin)
            ->test('admin::newsletter-subscribers')
            ->call('confirmDelete', $subscriber->id)
            ->assertSet('deleteId', $subscriber->id)
            ->assertSet('showDeleteModal', true)
            ->call('delete')
            ->assertSet('showDeleteModal', false)
            ->assertSet('deleteId', 0);

        $this->assertDatabaseMissing('newsletter_subscribers', [
            'id' => $subscriber->id,
        ]);
    }
}
