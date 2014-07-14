<?php

class PostController extends Controller {

	public function run() {

        Model::Load('Post');

		$title = Request::GET('title');
        $myPost = Post::GetByTitle($title);

		Template::load('basic')
            ->title($myPost->title)
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
            ->render();
	}
}