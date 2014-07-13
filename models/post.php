<?php

class Post extends Model
{
    protected static $table = 'aa_post';
    protected static $map = array
                            (
                                'id'    => 'id',
                                'title' => 'title',
                                'link'  => 'link_title',
                                'text'  => 'fulltext',
                                'date'  => 'timestamp',
                            );

    public $id;
    public $title;
    public $link;
    public $text;
    public $date;

    public $tags;
    public $userVoted;
    public $votes;
    public $comments;

    public function __construct()
    {
        $this->tags         = "none tag";
        $this->userVoted    = false;
        $this->votes        = 0;
        $this->comments     = 0;
    }

    public static function GetByTitle($title)
    {
        return self::Get();
    }

    public static function Recent($num)
    {
        return self::Query("SELECT * FROM ".self::$table . " ORDER BY timestamp DESC");
    }

    public static function transform($text)
    {
        $text = preg_replace('/(\n|^)([^\n]+)(\n\n|$)/g','/\n<p>$2</p>\n/',$text);
        $text = preg_replace('/######\s*([^\n]+)/g', '<h6>$1</h6>', $text);
        $text = preg_replace('/#####\s*([^\n]+)/g', '<h5>$1</h5>', $text);
        $text = preg_replace('/####\s*([^\n]+)/g', '<h4>$1</h4>', $text);
        $text = preg_replace('/###\s*([^\n]+)/g', '<h3>$1</h3>', $text);
        $text = preg_replace('/##\s*([^\n]+)/g', '<h2>$1</h2>', $text);
        $text = preg_replace('/#\s*([^\n]+)/g', '<h1>$1</h1>', $text);
        $text = preg_replace('/---/g', '<div class="hr"><span class="hrleft"></span><span class="hrright"></span></div>', $text);

        return $text;
    }

    public function GetDate()
    {
        $date = new DateTime($this->date);
        return $date->format('d M, Y');
    }

    public function __toString()
    {
        return '['.$this->id.'] '.$this->title.' was written on '.$this->date;
    }
}