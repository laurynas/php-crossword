<?
// print out variable dump
function dump( $var )
{
	print "<pre>";
	var_dump( $var );
	print "</pre>";
}

function getmicrotime(){
    list($usec, $sec) = explode(" ",microtime());
    return ((float)$usec + (float)$sec);
}
?>