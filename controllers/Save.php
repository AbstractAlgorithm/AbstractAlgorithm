<?php

require 'models/post.php';

class SaveController extends Controller {

	public function run() {

		$id 	 =  Request::GET('id');
		$title 	 =  Request::POST('edit_title');
		$link 	 =  Request::POST('edit_link');
		$text 	 =  htmlspecialchars(Request::POST('edit_text'));
		$summary =  htmlspecialchars(Request::POST('edit_summary'));

		$q = "UPDATE posts SET title='$title', linktitle='$link', text='$text', summary='$summary' WHERE id=".$id;
		Post::execute( $q, false );
		
		Request::gotoAddress('../list/');
	}
}