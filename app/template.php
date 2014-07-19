<?php

require 'toxic.php';

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
        $this->text = file_get_contents(VIEW_DIR.'/'.$filename.'.tmp');
    }

    /**
    * Enables dev to explictly set the field with desired value.
    *
    * @param field  name of the field
    * @param val    value to be inserted
    * @return       template
    */
    private function set($field, $val)
    {
        $this->vars[$field] = is_callable($val)
                            ? (string)($val())
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
        foreach (Core::$config as $key => $value)                               // add environment variables
            $this->vars['CONFIG_'.$key] = $value;

        $this->text = Toxic::Execute($this->text, $this->vars);                 // parse and execute

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
        if(count($arguments)==1)
            $this->set($name, $arguments[0]);
        else
            $this->set($name, $arguments);

        return $this;
    }
}