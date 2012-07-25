<?php
/* 
| --------------------------------------------------------------
| 
| WowLib Framework for WoW Private Server CMS'
|
| --------------------------------------------------------------
|
| Author:       Steven Wilson
| Author:       Tony Hudgins
| Copyright:    Copyright (c) 2012, Plexis Dev Team
| License:      GNU GPL v3
|
*/

class Wowlib
{
    // Wowlib Constants
    const VERSION = '1.0';
    
    // Our realm DB Connection
    public static $RDB;
    
    // Static Instances
    public static $emulator;
    public static $rootPath;
    protected static $initilized = false;
    protected static $realm = array();
    

/*
| ---------------------------------------------------------------
| Constructor
| ---------------------------------------------------------------
|
| @Param: (String) $emulator - The emulator name
| @Param: (Array) $DB - An array of database connection information
|   As defined below:
|       array(
|           'driver' - Mysql, Postgres etc etc
|           'host' - Hostname
|           'port' - Port Number
|           'database' - Database name
|           'username' - Database username
|           'password' - Password to the database username
|       )
| @Return (None) - nothing is returned
|
*/
    public static function Init($emulator, $DB)
    {
        // Load some things just once
        if(!self::$initilized)
        {
            // Define our root dir
            self::$rootPath = dirname(__FILE__);
            
            // Load the realm database connection
            try {
                self::$RDB = new \Wowlib\Database($DB);
            }
            catch(Exception $e) {
                throw new Exception( $e->getMessage() );
            }
            
            // Set Emulator Variable
            self::$emulator = strtolower($emulator);
            $ucEmu = ucfirst(self::$emulator);
            
            // Autoload Interfaces
            $path = path( self::$rootPath, 'interfaces' );
            $list = scandir($path);
            foreach($list as $file)
            {
                if($file == '.' || $file == '..'); continue; 
                include path($path, $file);
            }
            
            // Load the emulator, and the driver class
            require_once path(self::$rootPath, 'drivers', 'Driver.php');
            $file = path(self::$rootPath, 'emulators', self::$emulator, $ucEmu .'.php');
            if(!file_exists($file)) throw new Exception("Emulator '". self::$emulator ."' Doesnt Exist");
            require_once $file;
            
            // Init the realm class
            try {
                $class = "\\Wowlib\\". $ucEmu;
                self::$realm[self::$emulator] = new $class( self::$RDB );
            }
            catch( \Exception $e) {
                self::$realm[self::$emulator] = false;
            }
            
            // Set that we are initialized
            self::$initilized = true;
        }
    }
    
/*
| ---------------------------------------------------------------
| Driver Loader
| ---------------------------------------------------------------
|
| @Param: (String) $driver - The driver name
| @Params: (Array) $char && $world - An array of database connection 
|   information as defined below:
|       array(
|           'driver' - Mysql, Postgres etc etc
|           'host' - Hostname
|           'port' - Port Number
|           'database' - Database name
|           'username' - Database username
|           'password' - Password to the database username
|       )
| @Return (Object) - false if object failed to load
|
*/
    public static function load($driver, $char, $world)
    {
        // Make sure we are loaded here!
        if(!self::$initilized) throw new Exception('Cannot load driver, Wowlib was never initialized!');
        
        // Load a new instance of the Driver class
        return new \Wowlib\Driver(self::$emulator, $driver, $char, $world);
    }
    
/*
| ---------------------------------------------------------------
| Realm Loader
| ---------------------------------------------------------------
|
| @Param: (String) $emu - If passed, the Emulator class of this
|   Emu will be returned
| @Params: (Array) $DB - An array of database connection 
|   information as defined below. Not needed unless loading a new
|   realm that is previously unloaded
|       array(
|           'driver' - Mysql, Postgres etc etc
|           'host' - Hostname
|           'port' - Port Number
|           'database' - Database name
|           'username' - Database username
|           'password' - Password to the database username
|       )
| @Return (Object) - false if object failed to load
|
*/
    public static function getRealm($emu = null, $DB = array())
    {
        // Make sure we are loaded here!
        if(!self::$initilized) throw new Exception('Cannot fetch realm, Wowlib was never initialized!');
        
        // If we have specified an emulator, load it, and return it
        if($emu != null)
        {
            if(!isset(self::$realm[$emu]))
            {
                // Make sure we have DB conection info
                if(empty($DB)) throw new Exception('No Database information supplied. Unable to load realm.');
                
                // Load the emulator class
                $ucEmu = ucfirst($emu);
                $file = path(self::$rootPath, 'emulators', $emu, $ucEmu .'.php');
                if(!file_exists($file)) return false;
                require_once $file;
                
                // Init the realm class
                try {
                    $class = "\\Wowlib\\". $ucEmu;
                    $DB = new \Wowlib\Database($DB);
                    self::$realm[$emu] = new $class( $DB );
                }
                catch( \Exception $e) {
                    self::$realm[$emu] = false;
                }
            }
            return self::$realm[$emu];
        }
        return self::$realm[self::$emulator];
    }
    
/*
| ---------------------------------------------------------------
| Emulator Setter
| ---------------------------------------------------------------
|
| @Param: (String) $emu - The emulator name we are switching to
| @Return (None) - nothing is returned
|
*/
    public static function setEmulator($emu)
    {
        // Make sure we are loaded here!
        if(!self::$initilized) throw new Exception('Cannot set emulator, Wowlib was never initialized!');
        
        // Set Emulator Variable
        self::$emulator = strtolower($emu);
    }
}

// Require our database and functions files
if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
require Wowlib::$rootPath . DS . 'inc'. DS .'Functions.php';
require Wowlib::$rootPath . DS . 'inc'. DS .'Database.php';
?>