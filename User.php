<?php
    class User {
        private $id;
        public $login;
        protected $password;
        protected $email;
        protected $firstname;
        protected $lastname;
        
        protected $tbname = "utilisateurs";

        protected $server_name;
        protected $username;
        protected $db_password;
        protected $dbname;
        protected $cnx;

        public function __construct($server_name = "localhost", $username = "root", $db_password = "", $dbname = "classes") {
            session_start();

            $this->server_name = $server_name;
            $this->username = $username;
            $this->db_password = $db_password;
            $this->dbname = $dbname;

            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

            $conn = new mysqli($this->server_name, $this->username, $this->db_password, $this->dbname);
            if($conn->connect_error) {
                die("Echec de la connexion : ". $conn->connect_error);
            }

            $this->cnx = $conn;
        }

        public function register($login, $password, $email, $firstname, $lastname) {
            $sql = "INSERT INTO ".User::get_table_name()."(login, password, email, firstname, lastname) VALUES(?, ?, ?, ?, ?)";
            $request = $this->cnx->prepare($sql);

            if($request->execute([$login, $password, $email, $firstname, $lastname]) === TRUE) {
                echo 'user créer';

                $last_id = $this->cnx->insert_id;
                return $this->cnx->query("SELECT * FROM ".User::get_table_name()." WHERE id = $last_id")->fetch_object();
            } else {
                echo "Error: " . $sql . "<br>" . $this->cnx->error;
            }
        }

        public function connect($login, $password) {
            $sql = "SELECT * FROM ".User::get_table_name()." WHERE login = ? && password = ?";
            $request = $this->cnx->prepare($sql);
            $request->bind_param("ss", ...[$login, $password]);
            $request->execute();

            $result = $request->get_result();
            $row = $result->fetch_assoc(); //tableaux

            if($result->num_rows == 0) {
                return false;
            }

            // set session user
            $this->loguer();
            echo "user connecter";

            // affecter les valeurs aux propriétes
            $this->setData($row);
            return $row;
        }

        public function desconnect() {
            session_unset();
            session_destroy();

            // effacer le quand tu mit unser($user)
            foreach($this->getAllInfos() as $property => $value) {
                $this->$property = null;
            }
        }


        public function delete() {
            $request = $this->cnx->prepare("DELETE FROM ".$this->get_table_name()." WHERE id = $this->id");
            $request->execute();
            $this->desconnect();

            return $request;
        }

        public function isConnected() {
            return $this->id != null;
        }

        protected function loguer() {
            $_SESSION["user"] = $this->getAllInfos();
        }

        /* -------------------- Getters ------------------- */
        protected static function get_table_name() {
            return "utilisateurs";
        }

        public function getLogin() {
            return $this->login;
        }

        public function getEmail() {
            return $this->email;
        }

        public function getFirstname() {
            return $this->firstname;
        }

        public function getLastname() {
            return $this->lastname;
        }

        public function getAllInfos() {
            $data = ["id" => $this->id, "login" => $this->login, "password" => $this->password, "email" => $this->email, "firstname" => $this->firstname, "lastname" => $this->lastname];

            return $data;
        }




        /* -------------------- Setters ------------------- */
        protected function setData($properties) {
            foreach($properties as $property => $value) {
                $this->$property = $value;
            }
        }


    }

    $user = new User();

    //$user->register(login: "dev", password: 'bvb', email: "bvb@bvb", firstname: "wahil", lastname: "chettouf");

    $user->connect(login: "dev", password: 'bvb');
    echo "<br>";

    $user->delete();

    var_dump($user->isConnected());


?>