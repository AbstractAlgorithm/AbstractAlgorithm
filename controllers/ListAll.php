<?php

require 'models/post.php';

class HomeController extends Controller {

	public function run() {

		$list_posts = Post::getLast(10, 'time');

		Template::load('basic')

			->title('Home')

			->content( Template::load('home') ->posts($list_posts) ->get() )

		->render();
	}
}