<?php

namespace App\Mail\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\FormInput;

class CreateUser extends Mailable
{
    use Queueable, SerializesModels;

    private $user;
    private $form;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, array $form)
    {
        $this->user = $user;
        $this->form = $form;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(config('mail.from.address'))
                    ->subject( "【" . config('app.name') . "】会員登録のお知らせ")
                    ->markdown('mails.admin.register_user')
                    ->with([
                        'user' => $this->user,
                        'form' => $this->form,
                        'custom_inputs' => FormInput::where(['form_type' => $this->form['key']])->get()
                    ]);
    }
}
