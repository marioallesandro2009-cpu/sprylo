<?php

namespace App\Support;

use App\Mail\VoucherIssuedMail;
use App\Models\Order;
use App\Models\PromoCode;
use App\Models\VoucherRule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

/**
 * Hands customers personal, single-use vouchers automatically when a VoucherRule's trigger happens.
 * A voucher is an ordinary PromoCode (so cart and checkout need no special handling) limited to one use
 * and an expiry date; its code is random, so only the customer it was emailed to is likely to have it.
 */
class VoucherIssuer
{
    private const PREFIXES = ['first_order' => 'WELCOME', 'order_milestone' => 'LOYAL'];

    /**
     * Called after an order is placed.
     *
     * @return Collection<int, PromoCode>
     */
    public static function afterOrder(Order $order): Collection
    {
        $email = mb_strtolower(trim($order->email));
        $placed = Order::whereRaw('lower(email) = ?', [$email])->where('status', '!=', 'cancelled')->count();

        $issued = collect();

        foreach (VoucherRule::where('is_active', true)->whereIn('trigger', ['first_order', 'order_milestone'])->get() as $rule) {
            $due = $rule->trigger === 'first_order'
                ? $placed === 1
                : ($rule->milestone_every && $placed > 0 && $placed % $rule->milestone_every === 0);

            if ($due && $voucher = static::issue($rule, $email, $order->name, $rule->trigger === 'order_milestone' ? 'order-'.$placed : null)) {
                $issued->push($voucher);
            }
        }

        return $issued;
    }

    /**
     * Creates the voucher and emails it. $milestoneKey makes a milestone voucher once per milestone
     * (not again if the same order count is evaluated twice); other triggers are once per customer.
     */
    public static function issue(VoucherRule $rule, string $email, string $name, ?string $milestoneKey = null): ?PromoCode
    {
        $already = PromoCode::where('voucher_rule_id', $rule->id)->where('issued_to_email', $email);

        if ($milestoneKey === null && $already->exists()) {
            return null;
        }

        if ($milestoneKey !== null && PromoCode::where('voucher_rule_id', $rule->id)->where('issued_to_email', $email)->where('code', 'like', '%'.strtoupper($milestoneKey).'%')->exists()) {
            return null;
        }

        $prefix = self::PREFIXES[$rule->trigger] ?? 'VOUCHER';

        do {
            $code = $prefix.'-'.strtoupper(Str::random(6));
        } while (PromoCode::where('code', $code)->exists());

        $voucher = PromoCode::create([
            'code' => $code,
            'type' => $rule->type,
            'value' => $rule->value,
            'min_subtotal' => $rule->min_subtotal,
            'expires_at' => now()->addDays(max(1, $rule->valid_days))->endOfDay(),
            'is_active' => true,
            'voucher_rule_id' => $rule->id,
            'issued_to_email' => $email,
            'usage_limit' => 1,
            'used_count' => 0,
        ]);

        $rule->increment('issued_count');

        try {
            Mail::to($email)->send(new VoucherIssuedMail($voucher, $rule, $name));
        } catch (Throwable $e) {
            // The voucher still exists and is shown on screen; a mail failure shouldn't undo it.
            Log::error('Failed to send voucher email', ['voucher' => $voucher->code, 'error' => $e->getMessage()]);
        }

        return $voucher;
    }
}
