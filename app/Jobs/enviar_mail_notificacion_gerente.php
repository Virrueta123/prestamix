<?php

namespace App\Jobs;

use App\Helpers\Generales;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class enviar_mail_notificacion_gerente implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( 
        private String $ruta, 
        private String $mensaje,
    )
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $gerentes = User::whereHas('roles', function ($query) {
            $query->where('name', 'gerente');
        })->get();

        // enviar correo electronico a los gerentes para que puedan aprobar dicha  solicitud
        foreach ($gerentes as $gerente) {
            $destinatario = $gerente->email; 
  
            Mail::send('correo.notificacion_gerente', [
                "ruta" => $this->ruta,
                "mensaje" => $this->mensaje,
                "saludo" => Generales::saludo(),
                "nombre" => $gerente->name . ' | ' . $gerente->lastname,
            ], function ($message) use ($destinatario) {
                $message->to($destinatario)->subject('Notificacion de Horizon Finance');
            });
        } 
    }
}
