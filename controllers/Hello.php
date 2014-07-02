<?php



class HelloController extends Controller {

	public function run() {


		Template::load('basic')

		->setTitle(function() {
			return 'Naslov';
		})

		->setContent(function() {
			return join("\n", array(

				'<h1>Cao</h1>',
				'<p>Ovo je neki tekst</p>',

				'<ul>',
					$this->exe(function() {

						// kvazi model
						$studenti = array(
							'dragan' => 20,
							'lukic' => 26,
							'milica' => 21,
						);

						$lista = '';
						foreach ($studenti as $ime => $g) {
							$lista .= Template::load('list_item')
										->setItem("$ime ima $g godin".($g%10==1?'u':'').($g%10>1&&$g%10<5?'e':'').($g%10>4||$g%10==0?'a':''))
										->get()."\n";
						}
						return $lista;
					}),
				'</ul>',

				));

		})

		->setScript(function() {
			return self::getName();
		})

		->render();
	}
}