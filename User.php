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

            
            $user = new User();
            // affecter les valeurs aux propriétes
            $user->setData($row);
            return $user;
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

    //User::register(login: "dev", password: 'bvb', email: "bvb@bvb", firstname: "wahil", lastname: "chettouf");

    if(isset($_SESSION["user"])) {
        $session = $_SESSION["user"];
        var_dump($session["login"]);
    }


    if(isset($_POST["dec"])) {
        $_SESSION["user"]->desconnect();
        header("refresh:0; URL:bvb.html");
    }

    if(isset($_POST["con"])) {
        $user = User::connect(login: "dev", password: 'bvb');
        $_SESSION["user"] = $user;
    }


    // if(isset($user) && $user) {
    //     echo($user->getLastname());
    //     echo "<br>";

    //     if($user->isConnected()) {
    //         echo "vous etez connecter";
    //     } else {
    //         echo "vous etez pas connecter";
    //     }
        
    // } else {
    //     echo "identifiant error ";
    // }

?>

<form action="" method="post">
    <input type="submit" name='con' value="connecter">
    <input type="submit" name='dec' value="deconnexion">
</form>