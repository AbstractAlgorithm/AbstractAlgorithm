<?php

class ArchiveController extends Controller {

    private static $perPage = 5;

    public function run()
    {
        Model::Load('Post');

        // ----------------------------------------------------------------------
        // ----------------------------------------------------------------------
        // ----------------------------------------------------------------------

        $page = (int)Request::GET('page')-1;

        // ----------------------------------------------------------------------
        // ----------------------------------------------------------------------
        // ----------------------------------------------------------------------


        Template::load('basic')
            ->title("Archive")
            ->header
            (
                Template::Load('header')
                    ->postPage(false)
                    ->get()
            )
            ->content
            (
                Template::load('list_posts')
                    ->pagenum($page)
                    ->get()
            )
            ->script
            (
                '<script type="text/javascript" src="http://code.jquery.com/jquery-2.1.1.min.js"></script>'
                ."\n".
                '<script type="text/javascript" src="'.JS_DIR.'/myCode.js"></script>'
            )
            ->render();
    }

    public static function kapitalizuj($str)
    {
        return ucfirst($str).'*^&%*$';
    }
}