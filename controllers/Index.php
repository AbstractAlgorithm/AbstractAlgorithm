<?php

class IndexController extends Controller {

    public function run()
    {

        $proba = array(1,2,4,8,16,32);

        Template::load('test')

        ->pita('pie')

        ->dojaja(false)

        ->king(true)

        ->brojevi($proba)

        ->render();
    }
}