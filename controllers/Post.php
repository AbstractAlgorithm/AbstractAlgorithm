<?php

require 'models/post.php';

class PostController extends Controller {

	public function run() {

		$title = Request::GET('title');
		
		$post = Post::execute('SELECT * FROM posts WHERE linktitle=\''.$title.'\'', true) [0];

		Template::load('basic')

			->title( $post->title )

			->content( Template::load('post') ->post($post) ->get() )

		->render();
	}
}