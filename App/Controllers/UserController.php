<?php

namespace App\Controllers;

use Framework\Core\BaseController;
use Framework\Http\Request;
use Framework\Http\Responses\Response;

class UserController extends BaseController
{

    public function index(Request $request): Response
    {
        return $this->html();
    }
}