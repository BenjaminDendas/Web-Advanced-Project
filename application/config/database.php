<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/*
 * 
 * Edit this file and upate with your own MySQL database details.
*/

$db['default'] = array(
        'dsn'   	=> 'mysql:host=localhost;dbname=ticketingsysteem',
        'username'	=> 'root',
        'password' 	=> 'root',
        'dbdriver' 	=> 'pdo',
        'dbprefix' 	=> '',
        'pconnect' 	=> TRUE,
        'db_debug' 	=> TRUE,
        'cache_on' 	=> FALSE,
        'cachedir' 	=> '',
        'char_set' 	=> 'utf8',
        'dbcollat' 	=> 'utf8_general_ci',
        'swap_pre' 	=> '',
        'encrypt' 	=> FALSE,
        'compress' 	=> FALSE,
        'stricton' 	=> FALSE,
        'failover' 	=> array()
);

$active_group = 'default';
$active_record = TRUE;
/* End of file database.php */
/* Location: ./application/config/database.php */