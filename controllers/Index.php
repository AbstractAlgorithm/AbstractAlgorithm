<?php

class IndexController extends Controller {

    public function run()
    {
        Model::Load('Post');

        // ----------------------------------------------------------------------
        // ----------------------------------------------------------------------
        // ----------------------------------------------------------------------

        // Post::Recent(5);

        // ----------------------------------------------------------------------
        // ----------------------------------------------------------------------
        // ----------------------------------------------------------------------


        Template::load('basic')
            ->title("Blog")
            ->css('new_style.css')
            ->header
            (
                Template::Load('header')
                    ->postPage(false)
                    ->get()
            )
            ->content
            (
                Template::load('list_posts')
                    ->hehe("## SUCK\n
                            This is nasty shit.\n\n

                            ---\n\n

                            ### HIV\n
                            AIDS\n\n

                            ---\n\n

                            ## Consclusion\n
                            It's been a fun ride while it last. But now, we must say goodbye and hope for the best.<br>Ta-da!")
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