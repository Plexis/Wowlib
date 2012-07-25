<?php
/*
| ---------------------------------------------------------------
| Function: path()
| ---------------------------------------------------------------
|
| Combines several strings into a file path.
|
| @Params: (String | Array) - The pieces of the path, passed as 
|   individual arguments. Each argument can be an array of paths,
|   a string foldername, or a mixture of the two.
| @Return: (String) - The path, with the corrected Directory Seperator
|
*/
if(!function_exists('path'))
{
    function path()
    {
        // Determine if we are one windows, And get our path parts
        $IsWindows = strtoupper( substr(PHP_OS, 0, 3) ) === "WIN";
        $args = func_get_args();
        $parts = array();
        
        // Trim our paths to remvove spaces and new lines
        foreach( $args as $part )
        {
            $parts[] = (is_array( $part )) ? trim( implode(DS, $part) ) : trim($part);
        }

        // Get our cleaned path into a variable with the correct directory seperator
        $newPath = implode( DS, $parts );
        
        // Do some checking for illegal path chars
        if( $IsWindows )
        {
            $IllegalChars = "\\/:?*\"<>|\r\n";
            $Pattern = "~[" . $IllegalChars . "]+~";
            $tempPath = preg_replace( "~^[A-Z]{1}:~", "", $newPath );
            $tempPath = trim( $tempPath, DS );
            $tempPath = explode( DS, $tempPath );
            
            foreach( $tempPath as $part )
            {
                if( preg_match( $Pattern, $part ) )
                {
                    show_error( "illegal_chars_in_path", array( $part ) );
                    return null;
                }
            }
        }
        
        return $newPath;
    }
}

/*
| ---------------------------------------------------------------
| Function: wowlib_list_files()
| ---------------------------------------------------------------
|
| This method is used to list an array of file names in a directory
|
| @Params: (String) - The full path to where we are going
| @Return: (Array)
|
*/
if(!function_exists('wowlib_list_files'))
{
    function wowlib_list_files($path)
    {
        // Make sure we have a path, and not a file
        if(is_dir($path))
        {
            // Make sure our path is correct
            if($path[strlen($path)-1] != DS) $path = $path . DS;
            
            // Open the directory
            $handle = @opendir($path);
            if ($handle === false) return false;
            
            // Files array
            $files = array();
            
            // Loop through each file
            while(false !== ($f = readdir($handle)))
            {
                // Skip "." and ".." directories
                if($f == "." || $f == "..") continue;

                // make sure we establish the full path to the file again
                $file = $path . $f;
                
                // If is directory, call this method again to loop and delete ALL sub dirs.
                if( !is_dir($file) ) 
                {
                    $files[] = $f;
                }
            }
            
            // Close our path
            closedir($handle);
            return $files;
        }
        return false;
    }
}

/*
| ---------------------------------------------------------------
| Function: wowlib_list_folders()
| ---------------------------------------------------------------
|
| This method is used to get an array of folders within a directory
|
| @Params: (String) - The full path to where we are going
| @Return: (Array):
|       array(
|           0 => "foldername"
|           1 => "foldername"
|       );
|
*/
if(!function_exists('wowlib_list_folders'))
{
    function wowlib_list_folders($path)
    {
        // Make sure we have a path, and not a file
        if(is_dir($path))
        {
            // Make sure our path is correct
            if($path[strlen($path)-1] != DS) $path = $path . DS;
            
            // Open the directory
            $handle = @opendir($path);
            if ($handle === false) return false;
            
            // Folders array
            $folders = array();
            
            // Loop through each file
            while(false !== ($f = readdir($handle)))
            {
                // Skip "." and ".." directories
                if($f == "." || $f == "..") continue;

                // make sure we establish the full path to the file again
                $file = $path . $f;
                
                // If is directory, call this method again to loop and delete ALL sub dirs.
                if(is_dir($file)) 
                {
                    $folders[] = $f;
                }
            }
            
            // Close our path
            closedir($handle);
            return $folders;
        }
        return false;
    }
}
?>