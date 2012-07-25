<?php
/* 
| -------------------------------------------------------------- 
| This example shows you how to load a realm, and fetch an account
| --------------------------------------------------------------
*/

// Include the wowlib class
include 'Wowlib.php';

// Connection Information
$conn = array(
    'driver'	   => 'mysql',
    'host'         => 'localhost',
    'port'         => '3306',
    'username'     => 'admin',
    'password'     => 'admin',
    'database'     => 'auth'
);

// Init the wowlib
Wowlib::Init('trinity', $conn);

// Fetch realm, and Dump the account id of 5
$Trinity = Wowlib::getRealm();
$Account = $Trinity->fetchAccount(5);
var_dump( $Account->getUsername() );