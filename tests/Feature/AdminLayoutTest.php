<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLayoutTest extends TestCase
{
    use RefreshDatabase;

    /** Every page the sidebar must keep reaching, for any admin. */
    private const MENU_ROUTES = [
        'admin.dashboard', 'admin.orders.index', 'admin.delivery-schedule.index',
        'admin.products.index', 'admin.categories.index', 'admin.brands.index', 'admin.reviews.index',
        'admin.promo-codes.index', 'admin.discounts.index', 'admin.flash-sales.index', 'admin.voucher-rules.index', 'admin.promo-banners.index',
        'admin.users.index', 'admin.contact-messages.index', 'admin.suspended-customers.index', 'admin.newsletter-subscribers.index',
        'admin.hero-slides.index', 'admin.nav-links.index', 'admin.footer-links.index', 'admin.faqs.index', 'admin.content.index', 'admin.ui-texts.index', 'admin.page-meta.index',
        'admin.stock-movements.index', 'admin.reports.finance', 'admin.settings.index', 'admin.account.edit', 'admin.logout', 'home',
    ];

    private const SUPER_ADMIN_ROUTES = ['admin.admin-users.index', 'admin.audit-logs.index'];

    private function admin(string $role): User
    {
        $user = new User;
        $user->forceFill(['name' => 'Test Admin', 'email' => $role.'@example.com', 'password' => 'not-used', 'role' => $role])->save();

        return $user;
    }

    public function test_the_sidebar_still_reaches_every_page(): void
    {
        $response = $this->actingAs($this->admin('super_admin'))->get(route('admin.dashboard'))->assertOk();

        foreach ([...self::MENU_ROUTES, ...self::SUPER_ADMIN_ROUTES] as $name) {
            $response->assertSee('"'.route($name).'"', false);
        }
    }

    public function test_every_page_in_the_menu_opens(): void
    {
        $admin = $this->admin('super_admin');

        foreach ([...self::MENU_ROUTES, ...self::SUPER_ADMIN_ROUTES] as $name) {
            if (in_array($name, ['admin.logout', 'home'], true)) {
                continue;
            }

            $this->actingAs($admin)->get(route($name))->assertOk();
        }
    }

    public function test_a_normal_admin_does_not_see_super_admin_pages(): void
    {
        $response = $this->actingAs($this->admin('admin'))->get(route('admin.dashboard'))->assertOk();

        foreach (self::SUPER_ADMIN_ROUTES as $name) {
            $response->assertDontSee('"'.route($name).'"', false);
        }
    }

    public function test_the_dashboard_only_lists_work_that_is_waiting(): void
    {
        $admin = $this->admin('super_admin');

        $this->actingAs($admin)->get(route('admin.dashboard'))
            ->assertSee('Tidak ada yang perlu dikerjakan')
            ->assertDontSee('pembayaran perlu dicek');

        Order::create([
            'order_number' => 'SPR-WAIT01', 'name' => 'Test', 'email' => 'test@example.com', 'phone' => '0800', 'address' => 'Test', 'city' => 'Jakarta', 'postal_code' => '10110',
            'subtotal' => 100000, 'shipping' => 0, 'tax' => 0, 'total' => 100000, 'status' => 'pending', 'payment_method' => 'bank_transfer',
        ]);

        $this->actingAs($admin)->get(route('admin.dashboard'))
            ->assertSee('1 pembayaran perlu dicek')
            ->assertDontSee('Tidak ada yang perlu dikerjakan');
    }
}
