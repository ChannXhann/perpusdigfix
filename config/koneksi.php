<?php
// config/koneksi.php
if (!class_exists('Database')) {
    class Database {
        private $host = "localhost";
        private $username = "root";
        private $password = "";
        private $dbname = "perpusdig";
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
