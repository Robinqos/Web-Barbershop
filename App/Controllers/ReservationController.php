<?php

namespace App\Controllers;

use Framework\Core\BaseController;
use Framework\Http\Request;
use Framework\Http\Responses\Response;

class ReservationController extends BaseController
{
    public function create(Request $request): Response
    {
        return $this->html();
    }

    public function index(Request $request): Response
    {
        return $this->html();
    }


}