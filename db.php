<?php 
    if(!session_id()) {
        session_start();
    }
    class Cnx {
        protected $server_name = "localhost";
        protected $username = "root";
        protected $db_password = "";
        protected $dbname = "classes";

        protected $cnx;

        public function __construct() {
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

            $conn = new mysqli($this->server_name, $this->username, $this->db_password, $this->dbname);
            if($conn->connect_error) {
                die("Echec de la connexion : ". $conn->connect_error);
            }

            $this->cnx = $conn;
        }

        public function getConn() {
            return $this->cnx;
        }

    }