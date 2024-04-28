<?php

namespace App\Mail\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Presenter;
use App\Models\FormInput;

class CreatePresenter extends Mailable
{
    use Queueable, SerializesModels;

   // private Presenter $presenter;
   // private array $form;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Presenter $presenter, array $form)
    {
        $this->presenter = $presenter;
        $this->form = $form;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $attendee = $this->presenter->attendee;
        $user = $attendee->user;
        return $this->from(['address' => config('mail.from.address'), 'name' => config('mail.from.name')])
                    ->subject( "【" . config('app.name') . "】講演申し込み受付のお知らせ")
                    ->markdown('mails.admin.register_presenter')
                    ->with([
                        'presenter' => $this->presenter,
                        'attendee' => $attendee,
                        'user' => $user,
                        'form' => $this->form,
                        'custom_inputs' => FormInput::where(['form_type' => $this->form['key']])->get()
                    ]);
    }
}
