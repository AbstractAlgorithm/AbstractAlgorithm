<?php

class Post extends Model
{
    private static $table = 'aa_post';
    private static $map =   array
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
}