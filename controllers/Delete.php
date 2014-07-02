<?php

require 'models/post.php';

class DeleteController extends Controller {

	public function run() {

		$id = (int)Request::GET('id');

		Post::removeAt( 'id', $id );
		
		Request::gotoAddress('../list/');
	}
}