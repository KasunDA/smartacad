<?php

namespace App\Mail;

use App\Models\Admin\Users\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserConfirm extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The user instance.
     *
     * @var User
     */
    public $user;

    /**
     * The Message Contents.
     *
     * @var $content
     */
    public $content;

    /**
     * Create a new message instance.
     * @param User $user
     * @param String $content
     */
    public function __construct(User $user, $content)
    {
        $this->user = $user;
        $this->content = $content;
        $this->subject = env('APP_NAME') . ' Account Confirmation';
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.confirm');
    }
}
