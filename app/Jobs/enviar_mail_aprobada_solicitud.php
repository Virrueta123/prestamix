<?php

namespace App\Jobs;

use App\Helpers\Generales;
use App\Models\Cliente;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class enviar_mail_aprobada_solicitud implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

     /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        private User $usuario,
        private String $ruta,
        private int $cliente,
        private $serie
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
            $usuario = $this->usuario; 
            
            $destinatario = $usuario->email;

            $cliente = Cliente::find($this->cliente);

            Mail::send('correo.solicitud_aprobada', [
                "ruta" => $this->ruta,
                "cliente" => $cliente,
                "serie" => $this->serie,
                "saludo" => Generales::saludo(),
                "nombre" => $usuario->name . ' | ' . $usuario->lastname,
            ], function ($message) use ($destinatario) {
                $message->to($destinatario)->subject('Solicitud aprobada');
            });
         
    }
}
