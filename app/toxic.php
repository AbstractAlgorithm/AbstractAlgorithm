<?php

/**
* Wrapper class for ASTNode. It hides all the details and allows just one action.
*/
final class Toxic
{
    /**
    * Wrapper function for ASTNode build&execute functions.
    *
    * @param text   input text to beparsed
    * @param vars   "local" variables
    * @return       result of evaluated code
    * @see          ASTNode
    */
    public static function Execute($text, $vars)
    {
        $tree = ASTNode::BuildAST($text, $vars);
        return $tree->exe();
    }
}

/**
* Class that parses and executes the templating code. It's based upon AST node<br>
* creation. It's overly complex, overly stupid and overly long, but it does the job. :D
*/
final class ASTNode
{
    private $expression;
    private $children;
    private $parent;
    private $locals;
    private $type;

    private static $current;
    private static $node_idx;
    private static $data;

    /**
    * Constructor for the AST node.
    * <ul>
    *   <li>expression  - parsed version to be executed</li>
    *   <li>children    - ast nodes in hierarchy</li>
    *   <li>parent      - parent of the node</li>
    *   <li>locals      - variables in "scope"</li>
    *   <li>type        - node's type (TEXT, VAR, IF, FOR, BLOCK)</li>
    * </ul>
    */
    private function __construct($e)
    {    
        $this->expression   = $e;
        $this->children     = array();
        $this->parent       = null;
        $this->locals       = array();
        $this->type         = 'TEXT';
    }

    /*
    * ------------------------------------------------------------------------------------------------------------------
    * ------------------- CREATING TREE --------------------------------------------------------------------------------
    * ------------------------------------------------------------------------------------------------------------------
    *
    * Functions used for parsing and creating the AST structure.<br>
    * Functions include add, parseExp, BuildAST.
    *
    */

    /**
    * Binds child and parent node.
    *
    * @param child child node to be added
    */
    private function add($child)
    {
        $child->parent = $this;
        $this->children[] = $child;
    }

    /**
    * Function that parses all the nodes that will be the children of the current<br>
    * Children are parsed until '$end'-ing criteria is met.
    *
    * @param $end part of expression that stops parsing in the branch
    */
    private static function parseChildren($end, &$exp)
    {
        $end = str_replace('|', '\b|\b', $end);
        $exp = self::$data[ ++self::$node_idx ];
        while ( !preg_match('/\b'.$end.'\b/i', $exp))
        {
            self::parseExp( $exp );
            self::$node_idx++;
            $exp = self::$data[ self::$node_idx ];
        }
    }

    /**
    * Parses the expression and determines what type is it.<br>
    * It builds dependacy tree.<br>
    * This is main parsing function.
    * 
    * @param exp expression to be parsed
    */
    private static function parseExp($exp)
    {
        if ($exp!='')
        {
            if ($exp[0] == '{')                                                 // VAR NODE
            {
                $exp            = preg_replace('/\{|\}/', '', trim($exp));      // strip curly braces
                
                $new_node       = new ASTNode($exp);                            // create node
                $new_node->type = 'VAR';
                self::$current  ->add($new_node);
            }
            else if ($exp[0] == '[')                                            // COMMAND NODE
            {
                $exp = preg_replace('/\[|\]/', '', $exp);                       // strip angle brackets

                if ( preg_match('/\s*block/i', $exp) )                          // -- block ?
                {
                    $exp = preg_replace('/\s+|block/i', '', $exp);              // leave just block name

                    $new_node       = new ASTNode($exp);                        // create node
                    $new_node->type = 'BLOCK';
                    self::$current  ->add($new_node);

                    self::$current = $new_node;                                 // add children
                    self::parseChildren('end', $exp);
                    self::$current =  $new_node->parent;
                }

                else if ( preg_match('/\s*foreach/i', $exp) )                   // -- foreach ?
                {
                    $exp = preg_replace('/\s+in\s+/i', '^', $exp);              // converts "foreach bla in blas"
                    $exp = preg_replace('/\s+|foreach/i', '', $exp);            // to "bla|blas"

                    $new_node       = new ASTNode($exp);                        // create node
                    $new_node->type = 'FOR';
                    self::$current  ->add($new_node);

                    self::$current  = $new_node;                                // add children
                    self::parseChildren('end', $exp);

                    self::$current =  $new_node->parent;                        // return to original parent
                }

                else if ( preg_match('/\s*if/i', $exp) )                        // -- if ?
                {
                    $exp = preg_replace('/\s+|if/i', '', $exp);                 // strip everything

                    $new_node       = new ASTNode($exp);                        // create node
                    $if_branch      = new ASTNode('');                          // 'true' branch
                    $else_branch    = new ASTNode('');                          // 'false' branch
                    $new_node       ->add($if_branch);
                    $new_node       ->add($else_branch);
                    $new_node->type = 'IF';
                    self::$current->add($new_node);

                    self::$current = $if_branch;                                // parse 'true' branch
                    self::parseChildren('end|else', $exp);

                    if (preg_match('/\belse\b/i', $exp))                        // parse 'false' branch
                    {
                        self::$current = $else_branch;
                        self::parseChildren('end', $exp);
                    }

                    self::$current = $new_node->parent;                         // revert
                }
            }
            else                                                                // TEXT NODE
            {
                $new_node       = new ASTNode($exp);                            // create node
                $new_node->type = 'TEXT';
                self::$current  ->add($new_node);
            }
        }
    }

    /**
    * Builds Abstract Syntax Tree from text and init variables.<br>
    * It parses the input text and builds simplified ASTree that will be<br>
    * later used to execute the page code.
    * <br>
    * Supports:
    * <ul>
    *     <li>if / ifelse</li>
    *     <li>foreach</li>
    *     <li>block</li>
    * </ul>
    *
    * @param text   text to parse
    * @param vars   initial "local scope" variables
    * @return       root of the AST
    * @see          Template
    * 
    */
    public static function BuildAST($text, $vars)
    {
        $regex_split    = '\[[^\]]*\]'  . '|'                                   // matches [...]
                        . '\{[^\}]*\}'  . '|'                                   // matches {...}
                        . '[^\{\[]*';                                           // matches everything else

        preg_match_all ('/'.$regex_split.'/', $text,  $matches);                // split text into code and text nodes
        self::$data = $matches[0];
        self::$node_idx = 0;
        $node_count = count(self::$data);



        $root = new ASTNode('');                                                // create root
        $root->locals = $vars;
        self::$current = $root;

        for ( ; self::$node_idx<$node_count; self::$node_idx++ )                // parse structure
        {
            $expr = self::$data[ self::$node_idx ];
            self::parseExp($expr);
        }

        return $root;
    }

    /*
    * ------------------------------------------------------------------------------------------------------------------
    * ---------------------- EXECUTION ---------------------------------------------------------------------------------
    * ------------------------------------------------------------------------------------------------------------------
    *
    * Functions used for executing instructions.<br>
    * Functions include getValue, exe and exeChildren.
    *
    */

    /**
    * Evaluates the expression/variable and calculates the result.<br>
    * It supports access to methods via '.method_name()', properties and key values via '.member'.
    *
    * @return result of calculated variable
    */
    private function getValue()
    {
        $exp = $this->expression;

        $negate = false;        
        if ($exp[0]=='!')
        {
            $negate = true;
            $exp = substr($exp, 1);
        }

        $fields_n_modif             = explode('|', $exp);                       // split expression to fields and modifs

        $exp                        = $fields_n_modif[0];                       // calc fields
        $fields                     = explode('.', $exp);

        $modif_exp                  = isset($fields_n_modif[1])                 // calc modifiers
                                    ? $fields_n_modif[1]
                                    : '';
        $modifs                     = explode(',', $modif_exp);

        $first_field = $fields[0];
        if (!isset($this->locals[$first_field]))
            @eval("\$result = $first_field;");
        else
            $result         = $this->locals[ $first_field ];                    // starting object

        for ($i=1, $n=count($fields); $i<$n; $i++)                              // calculating the result............(1)
        {
            if (strpos($fields[$i], '(')===false)                               // so it's a property/key
                $result             = isset( $result->{$fields[$i]})
                                    ? $result->{$fields[$i]}                    // property
                                    : $result[$fields[$i]];                     // array key
            else                                                                // so it's a method
            {
                $split_method       = explode('(', $fields[$i]);
                $method_name        = $split_method[0];                         // got method name as a string
                $method_args_str    = str_replace(')','', $split_method[1]);    // got parameters list as a string

                $method_args = array(); 
                if($method_args_str!='')
                {
                    $method_args_str = explode(',', $method_args_str);          // get parameters in their real form
                    foreach ($method_args_str as $arg_str)                      // ..
                    {                                                           // ..
                        @eval("\$val = $arg_str;");                             // ..   !!  EVAL()  !!  DANGER  !!
                        $method_args[] = $val;                                  // ..
                    }                                                           // ..
                }

                $result = call_user_func_array                                  // call method with parameters
                        (
                            array($result, $method_name),
                            $method_args
                        );
            }
        }

        foreach ($modifs as $modifier)                                          // apply modifiers...................(2)
            $result = ($modifier!='empty')
                    ? ($modifier!=''?call_user_func($modifier,$result):$result)
                    : (count($result)==0);

        if (gettype($result)=='boolean')                                        // invert results....................(3)
            $result ^= $negate;

        return $result;
    } 

    /**
    * Function that executes the command.
    *
    * @return resulting string (html)
    */
    public function exe()
    {
        $text_result = '';

        switch ($this->type)
        {
            case 'TEXT':                                                        // TEXT NODE
                $text_result        .= $this->expression;
                $text_result        .= $this->exeChildren();
            break;

            case 'VAR':                                                         // VAR NODE
                $text_result        = $this->getValue();
            break;

            case 'IF':                                                          // IF NODE
                $branch             = $this->getValue() ? 0 : 1;
                $this->children[$branch]->locals = $this->locals;
                $text_result        = $this->children[$branch]->exeChildren();                         
            break;

            case 'FOR':                                                         // FOREACH NODE
                $preserve           = $this->expression;                        // preserve expression for restoration
                $boom               = explode('^', $this->expression);
                $item_name          = $boom[0];
                $this->expression   = $boom[1];
                $collection         = $this->getValue();
                $this->expression   = $preserve;

                foreach ($collection as $item)
                {
                    $this->locals[$item_name] = $item;
                    $text_result    .= $this->exeChildren();
                }
            break;

            case 'BLOCK':                                                       // BLOCK NODE
                $text_result        = isset($this->locals[$this->expression])
                                    ? $this->getValue()                         // there's a replacement
                                    : $this->exeChildren();                     // default value
            break;
        }

        return (string)$text_result;
    }

    /**
    * Function that executes the children of the node.
    *
    * @return calculated string
    */
    private function exeChildren()
    {
        $res = '';
        foreach ($this->children as $kid)                                       // execute children
        {
            $kid->locals    = $this->locals;
            $res            .= $kid->exe();
        }
        return $res;
    }
    
    /*
    * ------------------------------------------------------------------------------------------------------------------
    * --------------------- DEBUGGING ----------------------------------------------------------------------------------
    * ------------------------------------------------------------------------------------------------------------------
    *
    * Functions used in debugging. They print AST like hierarchy, so I can see<br>
    * dependancies and relations amongst nodes. Quite fancy.<br>
    * Use it as 'echo $root->show();'
    *
    */
    private static function reqShow($obj, $lvl)
    {
        $res = htmlspecialchars($obj->expression);
        foreach ($obj->children as $key => $kid)
        {
            $res .= "\n";
            for ($i=0; $i<$lvl; $res .= "   ", $i++);
            $res .= "| ".self::reqShow($kid, $lvl+1);
        }
        return $res;
    }

    private function show()
    {
        return '<pre>'.self::reqShow($this, 1).'</pre>';
    }
}