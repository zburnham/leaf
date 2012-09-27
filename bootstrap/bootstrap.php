<?php
/**
 * Bootstrap.php
 * Runs bootstrap files.  Opens ./files directory and executes files in turn.
 * 
 * @author zburnham
 * @version 0.0.1
 */
//LEFTOFF Need to flesh this out.

$bootstrap = array();
$bootstrapHandle = opendir('bootstrap/files');
while (FALSE !== ($entry = readdir($bootstrapHandle))) {
    //var_dump(is_file('bootstrap/files/' . $entry));
    if (is_file('bootstrap/files/' . $entry)) {
        $bootstrap[str_replace('.php','', $entry)] = include('bootstrap/files/' . $entry);
    }
}
closedir($bootstrapHandle);
/**
 * Array. 
 */
return $bootstrap;