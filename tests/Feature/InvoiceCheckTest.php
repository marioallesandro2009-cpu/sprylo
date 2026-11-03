<?php

namespace Tests\Feature;

use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceCheckTest extends TestCase
{
    use RefreshDatabase;

    private function order(array $overrides = []): Order
    {
        $order = Order::create($overrides + [
            'order_number' => 'SPR-ABC123',
            'invoice_number' => 'INV-202611-0001',
            'invoiced_at' => now(),
            'name' => 'Test Customer',
            'email' => 'customer@example.com',
            'phone' => '08123456789',
            'address' => 'Secret Street 1',
            'city' => 'Jakarta',
            'postal_code' => '10110',
            'subtotal' => 100000,
            'shipping' => 10000,
            'tax' => 0,
            'total' => 110000,
            'status' => 'paid',
            'payment_method' => 'qris',
        ]);

        $order->items()->create(['product_name' => 'Test Keyboard', 'unit_price' => 100000, 'qty' => 1, 'subtotal' => 100000]);

        return $order;
    }

    private function check(string $invoiceNumber, string $email)
    {
        return $this->from(route('invoice-check.index'))->post(route('invoice-check.lookup'), [
            'invoice_number' => $invoiceNumber,
            'email' => $email,
        ]);
    }

    public function test_the_page_opens(): void
    {
        $this->get(route('invoice-check.index'))->assertOk()->assertSee('Check your invoice');
    }

    public function test_matching_number_and_email_shows_the_invoice_without_the_address(): void
    {
        $this->order();

        $this->check('INV-202611-0001', 'customer@example.com')
            ->assertOk()
            ->assertSee('SPR-ABC123')
            ->assertSee('Test Keyboard')
            ->assertSee('PAID')
            ->assertSee('invoice/SPR-ABC123?expires=', false)
            ->assertDontSee('Secret Street 1')
            ->assertDontSee('08123456789');
    }

    public function test_the_number_can_be_typed_in_lowercase(): void
    {
        $this->order();

        $this->check(' inv-202611-0001 ', 'customer@example.com')->assertOk()->assertSee('SPR-ABC123');
    }

    public function test_a_wrong_email_gets_the_same_answer_as_an_unknown_number(): void
    {
        $this->order();

        $wrongEmail = $this->check('INV-202611-0001', 'someone@example.com');
        $wrongEmail->assertRedirect(route('invoice-check.index'));
        $wrongEmail->assertSessionHasErrors('invoice_number');
        $wrongEmailMessage = session('errors')->first('invoice_number');

        $unknownNumber = $this->check('INV-202611-9999', 'customer@example.com');
        $unknownNumber->assertSessionHasErrors('invoice_number');

        $this->assertSame($wrongEmailMessage, session('errors')->first('invoice_number'));
    }

    public function test_a_cancelled_invoice_is_marked_invalid_and_has_no_pdf_link(): void
    {
        $this->order(['status' => 'cancelled']);

        $this->check('INV-202611-0001', 'customer@example.com')
            ->assertOk()
            ->assertSee('CANCELLED')
            ->assertSee('no longer valid')
            ->assertDontSee('invoice/SPR-ABC123?expires=', false);
    }

    public function test_looking_up_does_not_assign_a_number_to_an_order_without_one(): void
    {
        $order = $this->order(['invoice_number' => null, 'invoiced_at' => null]);

        $this->check('SPR-ABC123', 'customer@example.com')->assertSessionHasErrors('invoice_number');

        $this->assertNull($order->refresh()->invoice_number);
    }
}
