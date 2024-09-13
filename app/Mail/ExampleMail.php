<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ExampleMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data; // Public variable to pass data to the view

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data; // Assign data to the public variable
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('pasindutharushan119@gmail.com', 'Triangle POS') // Set the "From" address
            ->subject('Your Subject Here') // Set the email subject
            ->view('emails.example') // Specify the email view template
            ->with('data', $this->data); // Pass data to the view
    }
}
