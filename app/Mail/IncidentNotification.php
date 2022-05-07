<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class IncidentNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $proyecto;
    public $puntoMonitoreo;
    public $registro;
    public $registrador;
    public $parametros;

    public function __construct($proyecto, $puntoMonitoreo, $registro, $registrador, $parametros)
    {
        $this->proyecto = $proyecto;
        $this->puntoMonitoreo = $puntoMonitoreo;
        $this->registro = $registro;
        $this->registrador = $registrador;
        $this->parametros = $parametros;
    }

    public function build()
    {
        return $this->view('incidents.incident_notification');
    }
}
