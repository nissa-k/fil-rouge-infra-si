<?php

class Database {

    public static function getConnection() {
        return new PDO(
            "mysql:host=localhost;dbname=filrouge;charset=utf8",
            "root",
            ""
        );
    }
}