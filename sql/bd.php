<?php 

require_once 'conexion.php';

class BD {
    public static $instancia=null; //secrea una instancia para la conexión
    public static function crearinstancia(){ //Se crea un método llamado crearinstancia
        if ( !isset(self::$instancia)) { //tiene conexión, es decir la instancia tiene algo, sino, hace lo de andentro
            $opciones[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
            //self::$instancia = new PDO('mysql:host=localhost; dbname=tecnopre_pntm', 'tecnopre_rootbd', '2020*tecnopresta', $opciones);
            
            //FUNCIONA LOCAL self::$instancia = new PDO('mysql:host=localhost; dbname=tecnopre_pntm', 'root', '', $opciones);
            //echo "Conectado...";
            self::$instancia = new \PDO(DB_Str, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
        }
        return self::$instancia; //de otra manera se geresa null
    }
}
?>


