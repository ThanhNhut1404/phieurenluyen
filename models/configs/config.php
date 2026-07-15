<?php
/* This class is used to connect to the database and return the connection object. */
    class Database {

        public $connect;

        public function __construct() {

            $init = parse_ini_file("config.ini");
            $servername = $init["servername"];
            $dbname = $init["dbname"];
            $username = $init["username"];
            $password = $init["pass"];

            date_default_timezone_set("Asia/Ho_Chi_Minh");
        
            $opt = array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            );
    
            try{
                $this->connect = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password,$opt);
            }
            catch(PDOException $e) {
                echo ("
                    <div class='container-center'>
                        <div class='center-xy'>
                            <p> <b>Lỗi kết nối Database </b> <br/></p>
                            <p> Mã lỗi: <i>".$e->getMessage()."</i> <br/></p>
                        </div>
                    </div>
                    "); 
            }
        }

    }
?>