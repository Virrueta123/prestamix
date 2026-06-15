<?php

namespace App\Http\Controllers;

use App\Events\pos_article_event;
use App\Models\producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Pusher\Pusher;
use App\Providers\PusherServiceProvider;
use App\Utils\pusher_services;

class pos_controller extends Controller
{

    public function __construct()
    {
        $this->middleware('auth')->except("enviar_codigo");
        $this->middleware('bloqueado');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function create()
    {

        Log::error("dasdsad");

        $message = "hola";

        // Broadcast the message to the private channel using Pusher

        // $codigo = "050001";

        // $pusher = new pusher_services();

        // // Llama al método trigger() en la instancia de Pusher
        // $pusher->trigger('channel-pos-article', 'pos-article', $codigo);


        // broadcast(new pos_article_event(Auth::user(),"mensaje prueba"));
        return view('modules.cautiva.pos.create');
    }

    public function enviar_codigo(Request $request)
    {
        $Param = $request->all();

        $codigo = $Param['codigo'];

        $producto = producto::where('code_bar', $codigo)->first();

        $pusher = new pusher_services();

        // Llama al método trigger() en la instancia de Pusher


        if ($producto == null) {
            $pusher->trigger('channel-pos-article', 'pos-article', [
                'message' => 'El código no se encuentra en la base de datos',
                'error' => "",
                'success' => false,
                'data' => '',
            ]);
            return response()->json([
                'message' => 'El código no se encuentra en la base de datos',
                'error' => "",
                'success' => false,
                'data' => '',
            ]);
        }
        $pusher->trigger('channel-pos-article', 'pos-article', [
            'message' => 'se cargo correctamente el producto',
            'error' => "",
            'success' => true,
            'data' => $producto,
        ]);
        return response()->json([
            'message' => 'se cargo correctamente el producto',
            'error' => "",
            'success' => true,
            'data' => $producto,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
