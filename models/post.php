<?php

class Post extends ORM {

	// database related
	protected static $dbName = 'posts';

	protected static $dbMap = array(
									'id'=>'id',
									'title'=>'title',
									'linktitle' => 'link_title',
									'galleryid' => 'galid',
									'timestamp' => 'time',
									'text' => 'text',
									'summary' => 'summary'
								  );

	// properties
	public $id;
	public $title;
	public $link_title;
	public $galid;
	public $time;
	public $text;
	public $summary;

	public function formatTime() {
		return date('j M, Y', strtotime($this->time));
	}

	public function getCommentsNum() {
		return 0;
	}

	public function getPID() {
		return sha1($this->id);
	}

	public function getText() {
		return htmlspecialchars_decode($this->text);
	}

	public function getSummary() {
		return htmlspecialchars_decode($this->summary);	
	}

	public function __toString() {
		return 	'ID: '.$this->id.'<br>'.
				'Title: '.$this->title.'<br>'.
				'Link: '.$this->link_title.'<br>'.
				'GalID: '.$this->galid.'<br>'.
				'Time: '.$this->time.'<br>'.
				'Text: '.substr($this->text, 0, 20).'...'.'<br>'.
				'Summary: '.substr($this->summary, 0, 5).'...'.'<br>';
	}

}