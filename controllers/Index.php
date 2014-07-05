<?php

class Tekst
{
    public $tekst;
    public function __construct($str)
    {
        $this->tekst = $str;
    }
    public function deo($broj, $dodatak, $extra)
    {
        return substr($this->tekst, 0, $broj).$dodatak.($extra?'eksta':'<==3');
    }
}

class IndexController extends Controller {

    public function run()
    {
        // $ttt = new Tekst("123456789");

        // $proba = array(1,2,4,8,16,32);

        // Template::load('test')

        // ->pita('pie')

        // ->dojaja(false)

        // ->king(true)

        // ->brojevi($proba)

        // ->nekitekst('mwahaHaHha')

        // ->malohtml('<p>jebe mater</p>')

        // ->mojtekst($ttt)

        // ->render();

        Template::load('basic')

        ->content
        (
            Template::load('test2')

            ->tekstic('superman')

            ->get()
        )

        ->render();

    }

    public static function kapitalizuj($str)
    {
        return ucfirst($str).'*^&%*$';
    }
}