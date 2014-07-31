<?php

class CategoryController extends Controller {

    public function run()
    {
        Model::Load('post');

        // ----------------------------------------------------------------------
        // ----------------------------------------------------------------------
        // ----------------------------------------------------------------------

        $tagName    = Request::GET('tag');
        $page       = isset(Request::GET()['page'])
                    ? (int)Request::GET('page')-1
                    : 0;
        $posts      = Post::GetWithTag($tagName, $page);
        $num        = $posts['total'];
        $numPgs     = ceil($num / Post::$perPage);
        unset($posts['total']);

        // ----------------------------------------------------------------------
        // ----------------------------------------------------------------------
        // ----------------------------------------------------------------------


        Template::load('basic')
            ->title($tagName=='project'?'Projects':($tagName=='random'?'Random and the purpose':'Tag: '.$tagName))
            ->headerMenu
            ( 
                false,
                false,
                $tagName=='project'?true:false,
                $tagName=='random'?true:false,
                false
            )
            ->postPage(false)
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
                    ->prev(Core::$config['WEBSITE'].'/category/tag/'.$tagName.'/page/'.$page)
                    ->hasNext( $page+1<$numPgs)
                    ->next(Core::$config['WEBSITE'].'/category/tag/'.$tagName.'/page/'.($page+2))
                    ->get()
            )
            ->render();
    }

    public static function kapitalizuj($str)
    {
        return ucfirst($str).'*^&%*$';
    }
}