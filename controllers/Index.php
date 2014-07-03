<?php

class IndexController extends Controller {

    public function run()
    {
        Template::load('test')

        ->pita('pie')

        ->dojaja(true)

        ->render();
    }
}