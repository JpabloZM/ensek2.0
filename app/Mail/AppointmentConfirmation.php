<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AppointmentConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $appointment;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $serviceRequest = $this->appointment->serviceRequest;
        $subject = 'Confirme su cita: ' . $serviceRequest->service->name;
        
        // Crear un token único para la confirmación
        $token = hash('sha256', $this->appointment->id . $this->appointment->created_at);
        
        return $this->subject($subject)
                    ->view('emails.appointments.confirmation')
                    ->with([
                        'confirmationLink' => route('appointments.confirm', [
                            'id' => $this->appointment->id,
                            'token' => $token
                        ])
                    ]);
    }
}