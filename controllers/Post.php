<?php

class PostController extends Controller {

	public function run() {

        Model::Load('Post');

		$title = Request::GET('title');
        $myPost = Post::GetByTitle($title);

		Template::load('basic')
            ->title("Blog")
            ->css('new_style.css')
            ->header
            (
                Template::Load('header')
                    ->postPage(true)
                    ->get()
            )
            ->content
            (
                Template::load('post')
                    ->post($myPost)
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
}