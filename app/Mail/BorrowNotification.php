<?php

namespace App\Mail;

use App\Models\Borrow;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BorrowNotification extends Mailable
{
    use Queueable, SerializesModels;
    public $borrow;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Borrow $borrow)
    {
        $this->borrow = $borrow;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Borrow Notification')
                    ->view('email.borrow')
                    ->with(['borrow'=>$this->borrow]);
    }
}
