<?php 
//Probably not going to ever touch this. Changes will instead be made in dbconfig.ini if needed. 
$ini = parse_ini_file('dbconfig.ini');

$db = new PDO(
                "mysql:host=" . $ini['servername'] .
                ";port=" . $ini['port'] . 
                ";dbname=" . $ini['dbname'],
                $ini['username'],
                $ini['password']);

$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

?>