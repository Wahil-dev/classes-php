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

            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
            $this->conn = new mysqli($this->server_name, $this->username, $this->db_password, $this->dbname);
            if($this->conn->connect_error) {
                die("Echec de la connexion : ". $this->conn->connect_error);
            }
        }

        public function register($login, $password, $email, $firstname, $lastname) {
            $sql = "INSERT INTO ".$this->tbname."(login, password, email, firstname, lastname) VALUES(?, ?, ?, ?, ?)";
            $request = $this->conn->prepare($sql);

            if($request->execute([$login, $password, $email, $firstname, $lastname]) === TRUE) {
                $last_id = $this->conn->insert_id;
                return $this->conn->query("SELECT * FROM ".$this->tbname." WHERE id = $last_id")->fetch_object();
            } else {
                echo "Error: " . $sql . "<br>" . $this->conn->error;
            }
        }

        public function connect($login, $password) {
            $sql = "SELECT * FROM ".$this->tbname." WHERE login = ? && password = ?";
            $request = $this->conn->prepare($sql);
            $request->bind_param("ss", ...[$login, $password]);
            $request->execute();

            $result = $request->get_result();
            $row = $result->fetch_assoc();

            return $row;
        }


        /* -------------------- Getters ------------------- */

    }


$model = new User();
$user = $model->connect("dev", "bvb");
print_r ($user["login"]);