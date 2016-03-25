<?php
error_reporting(E_ERROR); // Set this to 0 for for prod
error_reporting(E_ALL); // Set this to 0 for for prod

session_start();

define("CONFIG_FILE","config.ini");

class LolWut extends PDO {
    private static $instance;

    public static function Instance() {
        if(!isset(self::$instance)) {
            self::$instance = new LolWut();
            self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        }

        return self::$instance;
    }

    public function __construct() {
        self::mysql();
    }


    private function mysql() {
        $host = "localhost";
        $port = "3306";

        $name = "CISC332_DB";
        $user = "root";
        $pass = "root";

        $dsn = "mysql:host=$host;port=$port;dbname=$name";
        self::connect($dsn, $user, $pass);
    }

    /**
     * Connect to database and return PDO instance
     */
    private function connect($dsn, $user, $pass) {
        try {
            parent::__construct($dsn, $user, $pass);
            $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            die("Database error ".$e->getMessage());
        }
    }
}
