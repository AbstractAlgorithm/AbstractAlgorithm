<?php

class Model
{
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
}