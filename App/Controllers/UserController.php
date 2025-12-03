<?php

namespace App\Controllers;

use Framework\Core\BaseController;
use Framework\Http\Request;
use Framework\Http\Responses\Response;

class UserController extends BaseController
{

    public function index(Request $request): Response
    {
        if(!($this->app->getAppUser()->isLoggedIn())) {  //lepsie takto ako $this->user->isLoggedIn()
            return $this->redirect($this->url("auth.login"));
        }
        //$this->app->getAppUser()->getIdentity() instanceof \App\Models\User)
        //tu uz je nacitany user z databazy
        return $this->html();
    }
}