<?php

require 'models/post.php';

class EditController extends Controller {

	public function run() {

		$name = Request::GET('name');

		$one_post = Post::execute('SELECT * FROM posts WHERE linktitle=\''.$name.'\'', true) [0];

		Template::load('basic')

			->title('Edit post')

			->content( Template::load('editpost') ->post($one_post) ->get() )

		->render();
	}
}