<?php

namespace App\Mail;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VoucherCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $transaction;
    public $voucherCodes;

    public function __construct(Transaction $transaction, $voucherCodes)
    {
        $this->transaction = $transaction;
        $this->voucherCodes = $voucherCodes;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Voucher Codes - ' . $this->transaction->product->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.voucher-codes',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}