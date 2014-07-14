<?php

class CategoryController extends Controller {

    public function run()
    {
        Model::Load('Post');

        // ----------------------------------------------------------------------
        // ----------------------------------------------------------------------
        // ----------------------------------------------------------------------

        $tagName    = Request::GET('tag');
        $page       = isset(Request::GET()['page'])
                    ? (int)Request::GET('page')-1
                    : 0;
        $posts      = Post::GetWithTag($tagName, $page);

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
                    ->posts($posts)
                    ->get()
            )
            ->render();
    }

    public static function kapitalizuj($str)
    {
        return ucfirst($str).'*^&%*$';
    }
}