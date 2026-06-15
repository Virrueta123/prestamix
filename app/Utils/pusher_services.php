<?php

namespace App\Utils;

use Pusher\Pusher;

class pusher_services
{
    public function __construct()
    {
    }

    public function trigger($channel, $event, $message)
    {

        $pusher = new Pusher(env('PUSHER_APP_KEY'), env('PUSHER_APP_SECRET'), env('PUSHER_APP_ID'), [
            'cluster' => 'mt1',
            'encrypted' => true
        ]);
        $pusher->trigger($channel, $event, $message);
    }
}
