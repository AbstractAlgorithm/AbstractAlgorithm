<?php

require 'toxic.php';

use ASTNode as Toxic;

/**
* "View" of the framework. Responsible for generating and rendering the content.
*/
class Template
{
    /**
    * Nice wrapper function for writing shorter and more readable code.
    *
    * @param filename   file to be read and parsed
    * @return           template that can be used
    */
    public static function load($filename) { return new Template($filename); }

    private $vars;
    private $text;

    /**
    * Constructor for the class.
    *
    * @param filename   file to be read and parsed
    * @return           template that can be used
    */
    private function __construct($filename)
    {
        $this->vars = array();
        $this->text = file_get_contents(VIEW_DIR . $filename.'.tmp');
    }

    /**
    * Enables dev to explictly set the field with desired value.
    *
    * @param field  name of the field
    * @param val    value to be inserted
    * @return       template
    */
    public function set($field, $val)
    {
        $this->vars[(string)$field] = is_callable($val)
                                        ? (string)$val()
                                        : $val;

        return $this;
    }

    /**
    * Function that prints the template text.
    */
    public function render() { echo $this->get(); }

    /**
    * Retrieves the template's text.
    *
    * @return template text
    */
    public function get() { return $this->parse()->text; }

    /**
    * Builds AST hierarchy and executes the code, thus generating text.
    *
    * @return template;
    */
    private function parse()
    {
        $ASTree     = Toxic::BuildAST($this->text, $this->vars);
        //$this->text = Toxic::Execute($ASTree);

        return $this;
    }

    /**
    * Magic functions used for setting field values. Quite handy.
    * 
    * @param fieldname  name of the field
    * @return           template
    */
    public function __call($name, $arguments)
    {        
        if(substr($name, 0,3)=='set')                                           // SET<field>
            $this->set(strtolower( substr($name,3) ), $arguments[0]);
        else                                                                    // <field>
            $this->set($name, $arguments[0]);

        return $this;
    }
}