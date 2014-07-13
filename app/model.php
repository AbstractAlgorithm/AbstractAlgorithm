<?php

class Model
{
    protected static $map   = array();
    protected static $table = '';

    public static function Load($classname)
    {
        $filename = MODEL_DIR .'/'. $classname .'.php';
        if(file_exists($filename))
            require $filename;
        else
        {
            # throw some expection ...
        }
    }

    public static function Query($q)
    {
        $mysql_res = DB::Query($q);
        $res = array();
        $className = get_called_class();
        if(gettype($mysql_res)=='array')
            foreach($mysql_res as $item)
            {
                $new_instance = new $className();
                foreach ($className::$map as $inClass => $inDB)
                    $new_instance->{$inClass} = $item[$inDB];
                $res[] = $new_instance;
            }
            
        return $res;
    }

    public function update()
    {
        # depending on the argument count
    }

    public function save()
    {
        # save current
    }

    public function delete()
    {
        # remove current
    }

    public function __call($name, $args)
    {
        # chose what happens
    }

    #insert
}