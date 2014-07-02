<?php

require 'orm.php';

abstract class Controller {

	// TODO : prosledjivanje parametera
	public abstract function run();

	public static function getName() {
		return get_called_class();
	}

	public function exe($f) {
		return $f();
	}

}