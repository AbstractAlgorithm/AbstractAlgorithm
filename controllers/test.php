<?php

require 'models/post.php';

class Student {
	public $ime;
	public $godina;

	public function __construct($i, $g) {
		$this->ime = $i;
		$this->godina = $g;
	}

	public function __toString() {
		return $this->ime.'('.$this->godina.')';
	}

	public function p() {
		return new Student($this->ime.' Okanovic', $this->godina);
	}

	public function pozdrav() {
		return 'cao, '.$this;
	}

	public function jeOdrastao() {
		return ($this->godina>=18);
	}

	public function seZoveDragan() {
		return $this->ime=='Dragan';
	}
}

class HomeController extends Controller {

	public function run() {

		$ime = '<p>Dragan</p>';

		$lista = array();
		$lista[] = new Student('Dragan', 20);
		$lista[] = new Student('Milica', 21);
		$lista[] = new Student('Nikola', 20);
		$lista[] = new Student('Igor',   16);

		Template::load('basic')

			// description
			// ->description('Home page')

			// setting the keywordsfor the page
			// ->keywords('abstract, algorithm, blog, web, gpu, vfx, okanovic, dragan')
			// ->keywords('hehe')

			// set the titleof the main page
			->title('Home')

			// set the default stylesheet
			->stylesheet('media/style.css')

			// content
			->content(
				'<h1>Student strana</h1>'.

				Template::load('test')
				->student(new Student('Dragan', 20))
				->prom(true)
				->get().

				Template::load('lista')
				->studenti($lista)
				->test(true)
				->get()
			)

			// no scripts
			// ->script(new Student('Dragan', 20))

			// display everything
			->render();

		

		
	}



}