<?php

namespace Asfop\HasOne;

use Asfop\HasOne\contract\DBInterface;

class DB
{
    protected $db;


    public function __construct($db)
    {
        $this->db = $db;
    }

    public function get($tableName, $select, $whereFiled, $value)
    {
        $result = $this->db::table($tableName)->select($select)
            ->whereIn($whereFiled, $value)
            ->get();
        return !empty($result) ? json_decode(json_encode($result), true) : [];
    }
}
