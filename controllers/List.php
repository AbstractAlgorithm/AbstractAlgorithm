<?php

require 'models/post.php';

class ListController extends Controller {

	public function run() {

		$list_posts = Post::all();

		Template::load('basic')

			->title('All posts')

			->content( Template::load('list_posts') ->posts($list_posts) ->get() )

		->render();
	}
}