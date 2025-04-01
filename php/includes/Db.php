<?php
    class Db extends MySQLI{
        static protected $istance = null;

        public function __costruct($host, $user, $password, $schema){
            parent::__costruct($host, $user, $password, $schema);
        }

        public function getIstance(){
            if(self::$istance == null){
                self::$istance = new Db('my_mariadb', 'root', 'ciccio', 'scuola');
            }
            return self::$istance;
        }

        public function select($table, $where = 1){
            $query = "SELECT * FROM $table WHERE $where";
            if($result = $this->query($query)){
                return $result->fetch_all(MYSQLI_ASSOC);
            }
            return [];
        }
    }
?>