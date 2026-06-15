<?php

namespace App\Http\Controllers;

use App\Models\send_email;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class send_email_controller extends Controller
{
    public static function send_email_ingreso_creado(
        $mensaje,
        $titulo
    ) {

        $send_email = new send_email();
        $send_email->mensaje = $mensaje;
        $send_email->tipo = 'I';
        $send_email->titulo = $titulo;
        // $send_email->url = $url;
        $send_email->user_id = 1; // ID del usuario 
        $send_email->created_user = Auth::user()->id; // ID del usuario que creó el mensaje
        $send_email->sucursal_id = Auth::user()->sucursal_id; // ID de la sucursal

        // Guardar el registro en la base de datos
        $send_email->save();
    }

    public static function send_email_solicitud(
        $mensaje,
        $titulo,
        $url
    ) {

        $send_email = new send_email();
        $send_email->mensaje = $mensaje;
        $send_email->tipo = 'I';
        $send_email->titulo = $titulo;
        $send_email->url = $url;
        $send_email->user_id = 1; // ID del usuario 
        $send_email->created_user = Auth::user()->id; // ID del usuario que creó el mensaje
        $send_email->sucursal_id = Auth::user()->sucursal_id; // ID de la sucursal

        // Guardar el registro en la base de datos
        $send_email->save();
    }
}
