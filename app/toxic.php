<?php

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

    public function Execute()
    {
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
                $ex = str_replace('{', '', $this->expression);    // strip curly braces
                $ex = str_replace('}', '', $ex);    // strip curly braces
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

            if( $part =='()' )         // METHOD
                $current_obj = $current_obj->{substr($names[$i], 0,strlen($names[$i])-2)}();
            else {                     // PROPERTY
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

            if ( strpos($exp,'region') !== false )                              // -- region ?
            {
                $exp = preg_replace('/\s+|region/', '', $exp);                  // leave just region name

                $new_node = new ASTNode($exp);                                  // create node
                $new_node->type = 'REGION';
                self::$current->add($new_node);

            }

            else if ( strpos($exp,'if') !== false )                             // -- if ?
            {
                $exp = preg_replace('/\s+|if/', '', $exp);                      // strip everything

                $new_node = new ASTNode($exp);                                  // create node
                $if_branch      = new ASTNode('');
                $else_branch    = new ASTNode('');
                $new_node->add($if_branch);
                $new_node->add($else_branch);
                $new_node->type = 'IF';
                self::$current->add($new_node);

                self::$current = $if_branch;

                self::$node_idx++;
                $exp = self::$data[ self::$node_idx ];
                while ( !preg_match('/\bend\b|\belse\b/', $exp))
                {
                    self::parseExp( $exp );                                     // parse children
                    self::$node_idx++;
                    $exp = self::$data[ self::$node_idx ];
                }

                if (preg_match('/\belse\b/', $exp))
                {
                    self::$current = $else_branch;
                    self::$node_idx++;
                    $exp = self::$data[ self::$node_idx ];
                    while ( !preg_match('/\bend\b/', $exp))
                    {
                        self::parseExp( $exp );                                     // parse children
                        self::$node_idx++;
                        $exp = self::$data[ self::$node_idx ];
                    }
                }

                self::$current = $new_node->parent;
            }

            else if ( strpos($exp,'foreach') !== false )                        // -- foreach ?
            {
                $exp = preg_replace('/\s+in\s+/', '|', $exp);                   // converts "foreach bla in blas"
                $exp = preg_replace('/\s+|foreach/', '', $exp);                 // to "bla|blas"

                $new_node = new ASTNode($exp);                                  // create node
                $new_node->type = 'FOR';
                self::$current->add($new_node);

                self::$current = $new_node;                                     // add children
                self::$node_idx++;
                $exp = self::$data[ self::$node_idx ];
                while ( !preg_match('/\bend\b/', $exp))
                {
                    self::parseExp( $exp );                                     // parse children
                    self::$node_idx++;
                    $exp = self::$data[ self::$node_idx ];
                }
                self::$current = self::$current->parent;                        // return to original parent
            }
        }
        else                                                                    // TEXT NODE
        {
            $new_node = new ASTNode($exp);                                      // create node
            $new_node->type = 'TEXT';
            self::$current->add($new_node);
        }

        echo $new_node->type . ':-'.htmlspecialchars($new_node->expression).'<br>';
    }

    /**
    * Builds Abstract Syntax Tree from text and init variables.<br>
    * It parses the input text and builds simplified ASTree that will be later used to execute the page code.
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



        $root = new ASTNode('');
        $root->locals = $vars;
        self::$current = $root;

        for ( ; self::$node_idx<$node_count; self::$node_idx++ )
        {
            $expr = self::$data[ self::$node_idx ];
            self::parseExp($expr);
        }

        echo '-----------------------<br>';
        echo $root->show();
        echo '-----------------------<br>';

        return $root;
    }

    // -------------------------------------------------------
    // --------------------- DEBUGGING -----------------------
    // -------------------------------------------------------

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