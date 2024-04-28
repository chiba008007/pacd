<?php

namespace App\Mail\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Attendee;
use App\Models\FormInput;

class CreateAttendee extends Mailable
{
    use Queueable, SerializesModels;

    private $attendee;
    private $form;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Attendee $attendee, array $form)
    {
        $this->attendee = $attendee;
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
                    ->subject( "【" . config('app.name') . "】参加申し込み受付のお知らせ")
                    ->markdown('mails.admin.register_attendee')
                    ->with([
                        'attendee' => $this->attendee,
                        'user' => $this->attendee->user,
                        'form' => $this->form,
                        'custom_inputs' => FormInput::where(['form_type' => $this->form['key']])->get()
                    ]);
    }
}
