<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Record;
use App\Models\Personal;
use Illuminate\Bus\Queueable;
use App\Models\MonitoringPoint;
use App\Mail\IncidentNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class ProcessIncidentNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $puntoId;
    protected $recordId;
    protected $registradorId;
    protected $parametros;

    public function __construct($puntoId, $recordId, $registradorId, $parametros)
    {
        $this->puntoId = $puntoId;
        $this->recordId = $recordId;
        $this->registradorId = $registradorId;
        $this->parametros = $parametros;
    }

    public function handle()
    {
        $puntoMonitoreo = MonitoringPoint::with('proyecto:id,codigo,nombre')->where('id', $this->puntoId)->first();
        $proyecto = $puntoMonitoreo->proyecto;
        unset($puntoMonitoreo->proyecto);
        $registro = Record::find($this->recordId);
        $registrador = User::find($this->registradorId);
        $personal = Personal::all();

        Mail::to($personal)->send(new IncidentNotification($proyecto, $puntoMonitoreo, $registro, $registrador, $this->parametros));
    }
}
