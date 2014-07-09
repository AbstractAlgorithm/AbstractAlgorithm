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