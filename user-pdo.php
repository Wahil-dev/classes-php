<?php
    class Userpdo {
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
            
            try {
                $this->cnx = new PDO("mysql:host=$this->server_name; dbname=$this->dbname", $this->username, $this->db_password);
                $this->cnx->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            } catch (PDOException $e){
                print "Erreur !: " . $e->getMessage() . "<br/>";
                die();
            }
        }

        public function register($login, $password, $email, $firstname, $lastname) {
            $sql = "INSERT INTO ".Userpdo::get_table_name()."(login, password, email, firstname, lastname) VALUES(?, ?, ?, ?, ?)";
            $request = $this->cnx->prepare($sql);

            if($request->execute([$login, $password, $email, $firstname, $lastname]) === TRUE) {
                echo 'user créer';
            } else {
                echo "Error Inscription";
            }
        }

        public function connect($login, $password) {
            $sql = "SELECT * FROM ".Userpdo::get_table_name()." WHERE login = ? && password = ?";
            $request = $this->cnx->prepare($sql);
            $request->bindParam(1, $login);
            $request->bindParam(2, $password);
            $request->execute();
            
            $row = $request->fetch(PDO::FETCH_ASSOC); //tableaux

            if($row == 0) {
                echo "user n'est pas connecter";
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

        public function update($login, $password, $email, $firstname, $lastname) {
            if(!$this->isConnected()) {
                echo "user n'est pas update";
                return false;
            }
            $sql = "UPDATE ".Userpdo::get_table_name()." SET login = ? , password = ? , email = ? , firstname = ? , lastname = ? WHERE id = ?";

            $request = $this->cnx->prepare($sql);
            $request->execute([$login, $password, $email, $firstname, $lastname, $this->id]);

            echo "user information update";
        }

        /* -------------------- Getters ------------------- */
        protected static function get_table_name() {
            return "utilisateurs";
        }

        public function getId() {
            return $this->id;
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

    $user = new Userpdo();

    //$user->register(login: "dev", password: '4', email: "44444444444@bvb", firstname: "4444444", lastname: "4444444444");

    $user->connect(login: "dev", password: 'bvb');
    echo "<br>";

    //var_dump($user->getAllInfos());

    $user->update(login: "dev", password: 'bvbbvb', email: "44444444444@bvb", firstname: "4444444", lastname: "4444444444");
    
    echo "<br>";

    //var_dump($user->getAllInfos());

    echo "<br>";

    //$user->delete();

    //var_dump($user->getId());
    echo "<br>";

    //var_dump($user->isConnected());
    echo "<br>";

    var_dump($user->getAllInfos());
    echo "<br>";

    //var_dump($user->getLastname());
    
    echo "<br>";


?>