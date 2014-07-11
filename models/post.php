<?php

class Blogpost
{
    public $tekst;
    public $naslov;
    public $datum;
    public $komentari;
    public $glasovi;
    public $tagovi;

    public static $posts = array();
    public static $ime = 'Blogpost';
    public static $config = array( 'table' => 'post', 'rel'=>'boom');

    public function __construct($na, $te, $da, $ta)
    {
        $this->naslov = $na;
        $this->tekst = $te;
        $this->datum = $da;
        $this->glasovi = rand(0, 5);
        $this->komentari = array();
        $this->tagovi = $ta;
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
            $res[] = self::$posts[$n-1-$i];
        return $res;
    }

    public static function all()
    {
        return self::$posts;
    }

    public function excerpt()
    {
        // $args   = func_get_args();
        // $num    = count($args)>0
        //         ? $args[0]
        //         : 5;

        $hehe = preg_match('/([^\s]+\s+){100}/', $this->tekst);
        var_dump($hehe);
    }

    public function getTags()
    {
        return join(', ', $this->tagovi);
    }
}

class Post extends Model
{
    protected static $table = 'aa_post';
    protected static $map = array
                            (
                                'id'    => 'id',
                                'title' => 'title',
                                'link'  => 'link_title',
                                'text'  => 'text',
                                'date'  => 'timestamp',
                            );

    public $id;
    public $title;
    public $link;
    public $text;
    public $date;

    public $userVoted;
    public $votes;
    public $comments;

    public static function GetByTitle($title)
    {
        return null;
    }
}