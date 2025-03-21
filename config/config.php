<?php
    class Connexion {
        private $host = "localhost";
        private $db_name = "uaccolab";
        private $username = "root";
        private $password = "";
        public $db;

        public function get_connexion() {
            $this->db = null;
            try{
                $this->db = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
                $this->db->exec("set names utf8");
                $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
            }catch(PDOException $exception){
                require_once('./views/views-lost.php');
                die;
            }
            return $this->db;
        }
    }