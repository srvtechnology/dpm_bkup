<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    const MESSAGE_SUCCESS = 1;
    const MESSAGE_ERROR = 2;

    protected function setMessage($message, $type = 1)
    {
        return [
            'alert' => [
                'type' => $type,
                'msg' => $message
            ]
        ];
    }
}
