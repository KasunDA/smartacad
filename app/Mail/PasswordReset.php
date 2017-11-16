<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PasswordReset extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The request instance.
     *
     * @var User
     */
    public $req;

    /**
     * The Message Contents.
     *
     * @var $content
     */
    public $content = "A password reset has been issued to this account.\n kindly find below details of the password reset\n\n";

    /**
     * Create a new message instance.
     * @param User $req
     * @param String $content
     */
    public function __construct($req, $content)
    {
        $this->req = $req;
        $this->content .= $content;
        $this->subject = env('APP_NAME') . ': Password Reset';
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.password-reset');
    }
}
