<?php

class ASTNode {

	public $expression;
	public $children;
	public $parent;
	public $locals;
	public $exeChildren;

	private function __construct($e) {
		
		$this->expression = $e;
		$this->children = array();
		$this->parent = null;
		$this->locals = array();
		$this->exeChildren = true;
	}

	public function execute() {

		$res = '';

		if($this->expression!='') {	

			$firstChar = $this->expression[0];
			if($firstChar=='#') {

				// strip hashtags
				$ex = preg_replace('/\s*#\s*/', '', $this->expression);

	// IF
				if( substr($ex, 0, 2)=='if' ) {

					foreach ($this->children as $kid)
						$kid->exeChildren = true;
					$this->exeChildren = true;

					// onda se izvrsava if
					if( self::testIf($this) ) {
						foreach ($this->children as $kid) {
							if( preg_match('/\belse\b/i', $kid->expression) ) {
								// $kid->expression = '';
								$kid->exeChildren = false;
								break;
							}
						}
					}
					// izvrsava se else grana
					else {
						$this->exeChildren = false;
						foreach ($this->children as $kid) {
							if( preg_match('/\belse\b/', $kid->expression) ) {
								$res .= $kid->execute();
								break;
							}
						}
					}
					
				}

	// BLOCK
				else if( substr($ex, 0, 5)=='block' ) {
					$blk = self::testBlock($this);
					if($blk!=null) {
						$res .= $blk;
						$this->exeChildren = false;
					}
				}

	// FOREACH
				else if( substr($ex, 0, 7)=='foreach' ) {
					// TODO : foreach
					preg_match('/foreach\s+(\w+)\s+in\s+([^\s]+)\s*/', $ex, $matches);
					$this->exeChildren = false;
					$prom = self::getValue($matches[2], $this->locals);
					$localName = $matches[1];

					foreach ($prom as $value) {
						$this->locals[$localName] = $value;
						foreach ($this->children as $kid) {
							
							$kid->locals = $this->locals;
							$res .= $kid->execute();
						}	
					}
					unset($this->locals[$localName]);
				}

			}

	// VARIABLE
			else if($firstChar=='{') {
				$ex = str_replace('{', '', $this->expression);	// strip curly braces
				$ex = str_replace('}', '', $ex);	// strip curly braces
				$res .= self::getValue($ex, $this->locals);
			}
	// HTML
			else
				// this is some html
				$res .= $this->expression;
		}


		// go though all children | TODO : child locals, merge
		if($this->exeChildren)
			foreach ($this->children as $kid) {
				$kid->locals = $this->locals;
				$res .= $kid->execute();
			}

		return $res;
	}

	// used for testing if clause
	private static function testIf($astnode) {

		preg_match('/if\s+([^\s]+)\s*/', $astnode->expression, $var);
		if($var[1][0]=='!')
			return !(self::getValue( substr($var[1],1), $astnode->locals ));
		else
			return self::getValue($var[1], $astnode->locals);
	}

	// used for determing whether to use default block content or not
	private static function testBlock($astnode) {

		preg_match('/block\s+(\w+)\s*/', $astnode->expression, $var);
		if(isset($astnode->locals[$var[1]]))
			return self::getValue($var[1], $astnode->locals);
		else
			return null;
	}

	// used to retreive the value of an object
	private static function getValue($str_var, $locals) {

		// explode variable name
		$names = explode('.', $str_var);

		// object to be returned
		$current_obj = $locals[$names[0]];

		// iterate over the properties/fields
		for($i=1, $n=count($names); $i<$n; $i++) {

			$part = strlen($names[$i])>=2 ? substr($names[$i], strlen($names[$i])-2) : $names[$i];

			if( $part =='()' ) 		// METHOD
				$current_obj = $current_obj->{substr($names[$i], 0,strlen($names[$i])-2)}();
			else { 					// PROPERTY
				if( isset( $current_obj->{$names[$i]}) )
					$current_obj = $current_obj->{$names[$i]};
				else
					$current_obj = $current_obj[ $names[$i] ];
			}
		}
		return $current_obj;
	}

	
	// -------------------------------------------------------
	// ------------------- CREATING TREE ---------------------
	// -------------------------------------------------------

	private function add($kid) {
		$this->children[] = $kid;
		$kid->parent = $this;
	}

	public static function buildAndExecute($text, $vars) {



		$root = new ASTNode('');
		$root->locals = $vars;

		$current = $root;

		// create abstract syntax tree strcture
		self::buildTree($current, $text);

		// echo $root->show();
		return $root->execute();
		
	}

	private static function buildTree($current, $text) {
		// regular expressions used for parsing
		$reg_expression = '\#[^#]*#';
		$reg_variable = '\{[^\}]*\}';
		$reg_rest = '[^\{#]*';

		$reg_toChild = '/' .
		join('|', array('#\s*if'     ,
						'#\s*foreach',
						'#\s*else'   ,
						'#\s*block'  ))
		. '/i';

		$reg_fromChild = '/#\s*end/i';

		// split into text nodes
		preg_match_all('/'.$reg_expression.'|'.$reg_variable.'|'.$reg_rest.'/', $text, $matches);
		$data = $matches[0];

		// build tree strcture
		foreach ($data as $key => $e) {
			
			if( preg_match($reg_toChild, $e) ) {
				$kid = new ASTNode($e);
				$current->add($kid);
				$current = $kid;
				continue;
			}
			if( preg_match($reg_fromChild, $e) ) {
				while( !preg_match('/\bforeach\b|\bblock\b|\bif\b/', $current->expression))
					$current = $current->parent;
				$current = $current->parent;
				continue;
			}
			$kid = new ASTNode($e);
			$current->add($kid);
		}
	}

	// -------------------------------------------------------
	// --------------------- DEBUGGING -----------------------
	// -------------------------------------------------------

	private static function reqShow($obj, $lvl) {
		$res = htmlspecialchars($obj->expression);
		foreach ($obj->children as $key => $kid) {
			$res .= "\n";
			for($i=0; $i<$lvl; $res .= "   ", $i++);
			$res .= "|   ".self::reqShow($kid, $lvl+1);
		}
		return $res;
	}


	private function show() {
		return '<pre>'.self::reqShow($this, 1).'</pre>';
	}

}


class Template {

	public static function load($filename) {
		return new Template($filename);
	}

	private $vars;
	private $text;

	private function __construct($filename) {
		$this->vars = array();
		$this->text = file_get_contents("templates/$filename.tmp");
	}

	public function set($field, $val) {

		if(is_callable($val))
			$this->vars[(string)$field] = (string)$val();
		else
			$this->vars[(string)$field] = $val;

		return $this;
	}

	public function render() {
		echo $this->get();
	}

	public function get() {
		$this->parse();
		return $this->text;
	}

	private function parse() {
		$this->text = ASTNode::buildAndExecute($this->text, $this->vars);
	}

	public function __call($name, $arguments)
    {

    	// SET<field>
    	if(substr($name, 0,3)=='set')
        	$this->set(strtolower( substr($name, 3) ), $arguments[0]);

        // <field>
        else
        	$this->set($name, $arguments[0]);

        return $this;
    }

	

}