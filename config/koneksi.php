<?php
// config/koneksi.php
if (!class_exists('Database')) {
    class Database {
        private $host = "127.0.0.1:3306";
        private $username = "u137138991_root1";
        private $password = "Adminperpusdig123";
        private $dbname = "u137138991_perpusdig";
        public $koneksi;

        public function __construct() {
            $this->koneksi = new PDO("mysql:host=$this->host;dbname=$this->dbname", $this->username, $this->password);
            $this->koneksi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        public function getConnection()
        {
            return $this->koneksi;
        }
    }
}
?>
