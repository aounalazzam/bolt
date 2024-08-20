<?php

use Bolt\Lib\Database\{RecordOperations, Collection, CollectionTypes};

class Database
{
    // static RecordOperations $<name>;
}

try {
    /* 
    Database::$<name> = Collection::create("<name>", [
        "stringColumn" => CollectionTypes::string(length:50, nullable?:true, default?:''),
        "integerColumn" => CollectionTypes::number(length:50, nullable?:true, default?:1),
        "arrayColumn" => CollectionTypes::array(nullable?:true),
        "translatableStringColumn" => CollectionTypes::translatableString(nullable?:true),
        "relationalColumn" => CollectionTypes::relational(tableName:'table-name',nullable?:true),
        "hashColumn" => CollectionTypes::hash(nullable?:true),
        "jsonColumn" => CollectionTypes::json(nullable?:true),
    ]); 
    */
} catch (Exception $e) {
    echo $e->getMessage();
    die;
}
