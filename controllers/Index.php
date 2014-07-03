<?php

class IndexController extends Controller {

    public function run()
    {
        Template::load('test')
        ->render();
    }
}