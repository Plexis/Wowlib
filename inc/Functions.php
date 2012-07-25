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
?>