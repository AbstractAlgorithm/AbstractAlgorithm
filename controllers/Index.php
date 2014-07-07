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

class Blogpost
{
    public $tekst;
    public $naslov;
    public $komentari;

    public static $posts = array();
    public static $ime = 'Blogpost';
    public static $config = array( 'table' => 'post', 'rel'=>'boom');

    public function __construct($na, $te)
    {
        $this->naslov = $na;
        $this->tekst = $te;
        $this->komentari = array();
        self::$posts[] = $this;
    }

    public function addComm($co)
    {
        $this->komentari[] = $co;
        return $this;
    }

    public static function recent($num)
    {
        $res = array();
        $n = count(self::$posts);
        for($i=0; $i<$num && $i<$n; $i++)
            $res[] = self::$posts[$n-1-$i]->naslov;
        return $res;
    }

    public static function all()
    {
        return self::$posts;
    }
}

class IndexController extends Controller {

    public function run()
    {
        $post1 = new Blogpost("Prvi", "Ovo je prvi post.");
        $post1->addComm("Kul prvi post.");
        $post1->addComm("Stvarno do jaja.");

        $post2 = new Blogpost("2nd post", "Ovo je drugi post.");
        $post2->addComm("Losiji tekst");
        $post2->addComm("slabo");
        $post2->addComm("bedno");

        $post3 = new Blogpost("Treca sreca", "Lorem ipusm dolor sit amet.");
        $blogpostovi = array();
        $blogpostovi[] = $post1;
        $blogpostovi[] = $post2;
        $blogpostovi[] = $post3;

        $proba = array();
        $proba[] = 'prvi';
        $proba[] = 'drugi';

        Template::load('basic')

        ->title("Blog")

        ->content
        (
            Template::load('test2')

            ->tekstic('superman')

            ->postovi($blogpostovi)

            ->uslov(true)

            ->televizor($proba)

            ->get()
        )

        ->render();

    }

    public static function kapitalizuj($str)
    {
        return ucfirst($str).'*^&%*$';
    }
}