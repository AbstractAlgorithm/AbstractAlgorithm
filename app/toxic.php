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
class ASTNode
{
    public $expression;
    public $children;
    public $parent;
    public $locals;
    public $type;

    public static $current;
    public static $node_idx;
    public static $data;

    /**
    * Constructor for the AST node.
    * <ul>
    *   <li>expression  - parsed version to be executed
    *   <li>children    - ast nodes in hierarchy
    *   <li>parent      - parent of the node
    *   <li>locals      - variables in "scope"
    *   <li>type        - node's type (TEXT, VAR, IF, FOR, REGION)
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
    * Parses the expression and determines what type is it.<br>
    * It builds dependacy tree.<br>
    * This is main parsing function.
    * 
    * @param exp expression to be parsed
    */
    private static function parseExp($exp)
    {

        if ($exp[0] == '{')                                                     // VAR NODE
        {
            $exp    = preg_replace('/\s*/', '', $exp);                          // remove all white spaces
            $exp    = str_replace('{', '', $exp);                               // strip front curly bracket
            $exp    = str_replace('}', '', $exp);                               // strip back curly bracket
            
            $new_node = new ASTNode($exp);                                      // create node
            $new_node->type = 'VAR';
            self::$current->add($new_node);
        }
        else if ($exp[0] == '[')                                                // COMMAND NODE
        {
            $exp = str_replace('[', '', $exp);                                  // strup angle brackets
            $exp = str_replace(']', '', $exp);

            if ( preg_match('/\s*region/i', $exp) )                             // -- region ?
            {
                $exp = preg_replace('/\s+|region/i', '', $exp);                 // leave just region name

                $new_node = new ASTNode($exp);                                  // create node
                $new_node->type = 'REGION';
                self::$current->add($new_node);

                self::$current = $new_node;                                     // add children
                self::$node_idx++;
                $exp = self::$data[ self::$node_idx ];
                while ( !preg_match('/\bend\b/i', $exp))
                {
                    self::parseExp( $exp );                                     // parse children
                    self::$node_idx++;
                    $exp = self::$data[ self::$node_idx ];
                }
                self::$current =  $new_node->parent;

            }

            else if ( preg_match('/\s*foreach/i', $exp) )                       // -- foreach ?
            {
                $exp = preg_replace('/\s+in\s+/i', '|', $exp);                  // converts "foreach bla in blas"
                $exp = preg_replace('/\s+|foreach/i', '', $exp);                // to "bla|blas"

                $new_node = new ASTNode($exp);                                  // create node
                $new_node->type = 'FOR';
                self::$current->add($new_node);

                self::$current = $new_node;                                     // add children
                self::$node_idx++;
                $exp = self::$data[ self::$node_idx ];
                while ( !preg_match('/\bend\b/i', $exp))
                {
                    self::parseExp( $exp );                                     // parse children
                    self::$node_idx++;
                    $exp = self::$data[ self::$node_idx ];
                }
                self::$current =  $new_node->parent;                            // return to original parent
            }

            else if ( preg_match('/\s*if/i', $exp) )                            // -- if ?
            {
                $exp = preg_replace('/\s+|if/i', '', $exp);                     // strip everything

                $new_node = new ASTNode($exp);                                  // create node
                $if_branch      = new ASTNode('');                              // 'true' branch
                $else_branch    = new ASTNode('');                              // 'false' branch
                $new_node->add($if_branch);
                $new_node->add($else_branch);
                $new_node->type = 'IF';
                self::$current->add($new_node);

                self::$current = $if_branch;

                self::$node_idx++;
                $exp = self::$data[ self::$node_idx ];
                while ( !preg_match('/\bend\b|\belse\b/i', $exp))               // parse 'true' branch
                {
                    self::parseExp( $exp );                                     // parse children
                    self::$node_idx++;
                    $exp = self::$data[ self::$node_idx ];
                }

                if (preg_match('/\belse\b/i', $exp))                            // parse 'false' branch
                {
                    self::$current = $else_branch;
                    self::$node_idx++;
                    $exp = self::$data[ self::$node_idx ];
                    while ( !preg_match('/\bend\b/i', $exp))
                    {
                        self::parseExp( $exp );                                 // parse children
                        self::$node_idx++;
                        $exp = self::$data[ self::$node_idx ];
                    }
                }

                self::$current = $new_node->parent;                             // revert
            }
        }
        else                                                                    // TEXT NODE
        {
            $new_node = new ASTNode($exp);                                      // create node
            $new_node->type = 'TEXT';
            self::$current->add($new_node);
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
    *     <li>region</li>
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

        // echo '-----------------------<br>';                                     // ---- DEBUGGING
        // echo $root->show();                                                     // ---- DEBUGGING
        // echo '-----------------------<br>';                                     // ---- DEBUGGING

        return $root;
    }

    /*
    * ------------------------------------------------------------------------------------------------------------------
    * ---------------------- EXECUTION ---------------------------------------------------------------------------------
    * ------------------------------------------------------------------------------------------------------------------
    *
    * Functions used for executing instructions.<br>
    * Functions include add, parseExp, BuildAST.
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
        if($exp[0]=='!')
        {
            $negate = true;
            $exp = substr($exp, 1);
        }

        $fields = explode('.', $exp);
        $result = $this->locals[ $fields[0] ];

        for($i=1, $n=count($fields); $i<$n; $i++)
        {
            if( strpos($fields[$i], '()') !== false )                           // so it's a method
            {
                $method_name = str_replace('()', '', $fields[$i]);
                $result = $result->{$method_name}();
            }
            else                                                                // so it's a property
            {
                if( isset( $result->{$fields[$i]}) )                            // property
                    $result = $result->{$fields[$i]};
                else                                                            // array key
                    $result = $result[ $fields[$i] ];
            }
        }

        if (gettype($result)=='boolean')                                        // TODO : not just booleans
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
        $exe_children = true;

        switch($this->type)
        {
            case 'TEXT':                                                        // TEXT NODE
                $text_result        .= $this->expression;
                $text_result        .= $this->exeChildren();
            break;

            case 'VAR':                                                         // VAR NODE
                $text_result        = $this->getValue();
            break;

            case 'IF':                                                          // IF NODE
                if ($this->getValue())
                    $text_result    = $this->children[0]->exe();
                else
                    $text_result    = $this->children[1]->exe();
            break;

            case 'FOR':                                                         // FOREACH NODE
                $boom               = explode('|', $this->expression);
                $item_name          = $boom[0];
                $this->expression   = $boom[1];
                $collection         = $this->getValue();

                foreach ($collection as $item)
                {
                    $this->locals[$item_name] = $item;
                    $text_result .= $this->exeChildren();
                }
            break;

            case 'REGION':                                                      // REGION NODE
                if (isset($this->locals[$this->expression]))
                {
                    $text_result    = $this->getValue();
                    $exe_children   = false;
                }
                else
                    $text_result    = $this->exeChildren();
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
            $kid->locals = $this->locals;
            $res .= $kid->exe();
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
            for($i=0; $i<$lvl; $res .= "   ", $i++);
            $res .= "| ".self::reqShow($kid, $lvl+1);
        }
        return $res;
    }

    private function show()
    {
        return '<pre>'.self::reqShow($this, 1).'</pre>';
    }
}