<?php 
    class Connect extends PDO
    {
        public function __construct()
        {
<<<<<<< HEAD
            parent::__construct("mysql:host=localhost;dbname=security_project", "root", "",
            array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
            $this->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->setAttribute( PDO::ATTR_EMULATE_PREPARES, false);
=======
            try {
                parent::__construct(
                    "mysql:host=localhost;
                    dbname=security_project", 
                    "root", 
                    "",
                    array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
                );
                $this->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->setAttribute( PDO::ATTR_EMULATE_PREPARES, false);
            } catch (PDOException $e) {
                echo "Connection failed: " . $e->getMessage();
                die();
            }
>>>>>>> main
        }
    }
?> 