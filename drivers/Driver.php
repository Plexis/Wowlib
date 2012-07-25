<?php
/* 
| --------------------------------------------------------------
| 
| WowLib Framework for WoW Private Server CMS'
|
| --------------------------------------------------------------
|
| Author:       Steven Wilson
| Copyright:    Copyright (c) 2012, Plexis Dev Team
| License:      GNU GPL v3
|
*/

// All namespace paths must be Uppercase first letter!
namespace Wowlib;

class Driver
{
    public $CDB;
    public $WDB;
    
    // Out wowlib driver and emulator
    protected $driver;
    protected $emulator;
    

/*
| ---------------------------------------------------------------
| Constructor
| ---------------------------------------------------------------
|
*/
    public function __construct($emulator, $driver, $char, $world)
    {
        // Load the character DB
        try {
            $this->CDB = new \Wowlib\Database($char);
        }
        catch(\Exception $e) {
            $this->CDB = false;
        }
        
        
        // Load world DB
        try {
            $this->WDB = new \Wowlib\Database($world);
        }
        catch(\Exception $e) {
            $this->WDB = false;
        }

        // Finally set our emulator and driver variables
        $this->emulator = $emulator;
        $this->driver = $driver;
    }
    
/*
| ---------------------------------------------------------------
| Extenstion loader
| ---------------------------------------------------------------
|
*/
    public function __get($name)
    {
        // Just return the extension if it exists
        $name = strtolower($name);
        if(isset($this->{$name})) return $this->{$name};
        
        // Create our classname
        $class = ucfirst( $name );
        $driver = strtolower($this->driver);
        
        // Check for the extension
		$file = path( \Wowlib::$rootPath, 'drivers', $this->emulator, $driver, $class .'.php' );
        if( !file_exists( $file ) )
        {
            // Extension doesnt exists :O
            show_error('Failed to load wowlib extentsion %s', array($name), E_ERROR);
            return false;
        }
        
        // Load the extension file
        require_once( $file );
        
        // Load the class
        $class = "\\Wowlib\\{$this->driver}\\". $class;
        try {
            $this->{$name} = new $class($this);
        }
        catch(\Exception $e) {
            $this->{$name} = false;
        }
        return $this->{$name};
    }
}
?>