<?php

use Bolt\Utils\{Env};
use Bolt\Lib\Database\{DatabaseConnection, RecordOperations, Collection, CollectionTypes};

DatabaseConnection::init([
    "host" =>  Env::get("DATABASE_HOST"),
    "username" => Env::get("DATABASE_USERNAME"),
    "password" =>  Env::get("DATABASE_PASSWORD"),
    "database" =>  Env::get("DATABASE_NAME"),
]);

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
