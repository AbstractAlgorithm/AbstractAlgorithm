<?php

class BlogController extends Controller {

    private static $perPage = 5;

    public function run()
    {
        Model::Load('Post');

        // ----------------------------------------------------------------------
        // ----------------------------------------------------------------------
        // ----------------------------------------------------------------------

        $page   = isset(Request::GET()['page'])
                ? (int)Request::GET('page')-1
                : 0;
        $posts  = Post::Page($page);
        $num    = $posts['total'];
        $numPgs = ceil($num / Post::$perPage);
        unset($posts['total']);

        // ----------------------------------------------------------------------
        // ----------------------------------------------------------------------
        // ----------------------------------------------------------------------


        Template::load('basic')
            ->title("Blog")
            ->postPage(false)
            ->headerMenu
            ( 
                false,
                true,
                false,
                false,
                false
            )
            ->content
            (
                Template::load('list_posts')
                    ->posts($posts)
                    ->get()
                .
                Template::Load('paginator')
                    ->onePage($numPgs<2)
                    ->currentPage($page+1)
                    ->hasPrev( $page+1>1 )
                    ->prev(Core::$config['WEBSITE'].'/blog/page/'.$page)
                    ->hasNext( $page+1<$numPgs)
                    ->next(Core::$config['WEBSITE'].'/blog/page/'.($page+2))
                    ->get()
            )
            ->render();
    }

    public static function kapitalizuj($str)
    {
        return ucfirst($str).'*^&%*$';
    }
}