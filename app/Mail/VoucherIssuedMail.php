<?php

namespace App\Mail;

use App\Models\PromoCode;
use App\Models\VoucherRule;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VoucherIssuedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly PromoCode $voucher, public readonly VoucherRule $rule, public readonly string $customerName)
    {
    }

    public function build(): self
    {
        return $this->subject('A voucher just for you — '.$this->voucher->code)
            ->view('emails.voucher-issued');
    }
}
