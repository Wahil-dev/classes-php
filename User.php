<?php
    class User {
        private $id;
        protected $login;
        protected $password;
        protected $email;
        protected $firstname;
        protected $lastname;

        protected $server_name;
        protected $username;
        protected $db_password;
        protected $dbname;
        protected $tbname;
        protected $conn;

        public function __construct($server_name = "localhost", $username = "root", $db_password = "", $dbname = "classes", $tbname = "utilisateurs")
        {
            $this->server_name = $server_name;
            $this->username = $username;
            $this->db_password = $db_password;
            $this->dbname = $dbname;
            $this->tbname = $tbname;
            $this->conn = new mysqli($this->server_name, $this->username, $this->db_password, $this->dbname);
        }

        public function register($login, $password, $email, $firstname, $lastname) {
            if($this->conn->connect_error) {
                die("Echec de la connexion : ". $this->conn->connect_error);
            }

            $data = [$login, $password, $email, $firstname, $lastname];
            $sql = "INSERT INTO ".$this->tbname."(login, password, email, firstname, lastname) VALUES(?, ?, ?, ?, ?)";
            $request = $this->conn->prepare($sql);

            if($request->execute($data) === TRUE) {
                $last_id = $this->conn->insert_id;
                return $this->conn->query("SELECT * FROM ".$this->tbname." WHERE id = $last_id")->fetch_object();
            } else {
                echo "Error: " . $sql . "<br>" . $this->conn->error;
            }
        }
    }

