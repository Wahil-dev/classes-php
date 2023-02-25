<?php
    require_once("db.php");

    class User {
        private $id;
        public $login;
        protected $password;
        protected $email;
        protected $firstname;
        protected $lastname;
        
        protected $tbname = "utilisateurs";
        protected $cnx;

        public function __construct() {
            $this->cnx = (new Cnx())->getConn();
        }

        public static function register($login, $password, $email, $firstname, $lastname) {
            global $cnx;
            $cnx = new Cnx();
            $sql = "INSERT INTO ".User::get_table_name()."(login, password, email, firstname, lastname) VALUES(?, ?, ?, ?, ?)";
            $request = $cnx->getConn()->prepare($sql);

            if($request->execute([$login, $password, $email, $firstname, $lastname]) === TRUE) {
                $last_id = $cnx->getConn()->insert_id;
                return $cnx->getConn()->query("SELECT * FROM ".User::get_table_name()." WHERE id = $last_id")->fetch_object();
            } else {
                echo "Error: " . $sql . "<br>" . $cnx->getConn()->error;
            }
        }

        public static function connect($login, $password) {
            $cnx = new Cnx();
            $sql = "SELECT * FROM ".User::get_table_name()." WHERE login = ? && password = ?";
            $request = $cnx->getConn()->prepare($sql);
            $request->bind_param("ss", ...[$login, $password]);
            $request->execute();

            $result = $request->get_result();
            $row = $result->fetch_assoc(); //tableaux

            if($result->num_rows == 0) {
                return false;
            }

            $_SESSION["user"] = $row;
            $user = new User();
            // affecter les valeurs aux propriétes
            $user->setData($row);
            return $user;
        }

        public function desconnect() {
            session_start();
            session_unset();
            session_destroy();

            // pour vider les propriétes de calss
            $this->emptyTheObjectProperty();
        }

        public function emptyTheObjectProperty() {
            foreach($this->getAllInfos() as $property => $value) {
                $this->$property = null;
            }
        }

        public function delete() {
            $request = $this->cnx->prepare("DELETE FROM ".$this->get_table_name()." WHERE id = $this->id");
            $request->execute();

            // pour vider les propriétes de calss
            $this->emptyTheObjectProperty();
            return $request;
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
            $data = ["login" => $this->login, "password" => $this->password, "email" => $this->email, "firstname" => $this->firstname, "lastname" => $this->lastname];

            return $data;
        }




        /* -------------------- Setters ------------------- */
        protected function setData($properties) {
            foreach($properties as $property => $value) {
                $this->$property = $value;
            }
        }


    }

    //User::register(login: "dev", password: 'bvb', email: "bvb@bvb", firstname: "wahil", lastname: "chettouf");

    $user = User::connect(login: "dev", password: 'bvb');

    if(isset($user) && $user) {
        var_dump($user->getLastname());
        $user->delete();
        unset($user);
    } else {
        echo "identifiant error ";
    }