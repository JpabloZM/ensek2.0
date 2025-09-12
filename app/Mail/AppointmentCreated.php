<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AppointmentCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $appointment;
    public $isTechnician;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Appointment $appointment, $isTechnician = false)
    {
        $this->appointment = $appointment;
        $this->isTechnician = $isTechnician;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $serviceRequest = $this->appointment->serviceRequest;
        $subject = 'Cita Programada: ' . $serviceRequest->service->name;

        return $this->subject($subject)
                    ->view('emails.appointments.created');
    }
}