<?php

class ORM {

    protected static $dbName = 'none';

    private static $dbMap = array();

    private function getFields() {
        $d = get_object_vars($this);
    }
    
    public static function __callStatic($name, $arguments) {

        if(substr($name, 0,3)=='get' || $name=='all') {
            return self::getQuery(substr($name, 3), isset($arguments[0])?$arguments[0]:null );
        }

        if(substr($name, 0, 6)=='update') {
            return self::updateQuery();
        }
        
        // TODO : update, delete, insert
    }

    public static function getLast($n, $col) {
        $className = get_called_class();
        return $className::getFromTo(0, $n, $col, 'DESC');
    }

    public static function getFromTo($start, $end, $col, $orderType) {
        $className = get_called_class();
        $ordby = '';
        foreach ($className::$dbMap as $key => $value)
            if($value==$col)
                $ordby = $key;

        $q_str = 'SELECT * FROM '.$className::$dbName.' ORDER BY '.$ordby.' '.strtoupper($orderType).' LIMIT '.$start.','.($end-$start);
        return self::serialize( DB::query($q_str) );
    }

    public static function execute($query, $ser) {
        $res = DB::query($query);
        return $ser ? self::serialize($res) : $res;
    }

    protected static function getQuery($fields, $sortby) {

        $query = 'SELECT ';
        if($fields=='' || $fields=='All')
            $query .= '* ';
        else
            $query.= self::parseCamelCase($fields);

        $className = get_called_class();
        $query .= ' FROM ' . $className::$dbName;
        if($sortby!=null)
            $query .= ' ORDER BY '.$sortby;
        $res = DB::query($query);

        return self::serialize($res);
    }

    private static function updateQuery() {
        return '';
    }

    public static function removeAt($col_name, $val) {
        $className = get_called_class();
        $_=is_string($val);
        $query = 'DELETE FROM '.$className::$dbName.' WHERE '.$col_name.'='.($_?'\'':'') . $val . ($_?'\'':'');
        DB::query($query);
    }

    // helper functions

    protected static function serialize($mysqli_query) {
        $res = array();
        $className = get_called_class();

        while($v = DB::getNext($mysqli_query)) {

            $data = new $className();
            $map = $className::$dbMap;
            foreach($map as $inTable => $property)
                $data->{$property} = $v[ $inTable ];
            $res[] = $data;
        }
        return $res;
    }

    private static function parseCamelCase($str) {
        $pieces = preg_split('/(?=[A-Z])/', $str);
        foreach($pieces as $key => $val)
            $pieces[$key] = strtolower($val);
        return substr( join(', ', $pieces), 2 );
    }

}