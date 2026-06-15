<?php

namespace App\Console;

use App\Models\send_email;
use App\Models\User;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        // $destinatario = "juan_AVC789@hotmail.com";
        // $ruta = 'https://laravel.com/docs/10.x/mail';
        // $mensaje = 'prueba ';

        // $mensaje = Mail::send('correo.send_correo', ['cliente' => $mensaje, 'nombre' => $mensaje, "ruta" => $ruta, "saludo" => "dasddas"], function ($message) use ($destinatario) {
        //     $message->to($destinatario)->subject('Asunto del mensaje');
        // });
        $send_correo = send_email::where('status', "C")->get();

        foreach ($send_correo as $s_c) {
            if( $s_c->tipo == "I" ){

                $gerentes = User::whereHas('roles', function ($query) {
                    $query->where('name', 'gerente');
                })->get();


                foreach ($gerentes as $gerente) {
                    $destinatario = $gerente->email;   
                  
                    Mail::send('correo.send_correo', ['mensaje' => $s_c->mensaje, 'titulo' => $s_c->titulo,"url"=>$s_c->url], function ($message) use ($s_c,$destinatario) {
                        $message->to($destinatario)->subject($s_c->mensaje);
                    });
                } 
                
                $s_c->status = "P";
                $s_c->save();

            }
            
        }
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
