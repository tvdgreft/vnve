<?php

/**
 * prana_Ptext is gemaakt om de plugin eventueel later meertalig te maken.
 */
function prana_PText(string $short,string $long) : string 
{
	$html = '';
	$html .= $long;
	return($html);
}
function pranaAlert(string $message)
{
		echo "<script>alert('$message');</script>";
}
function  pranaConfirm(string $message){
    echo 
    "<script>
    var confirm='yes';
    var yes = 'yes';
    </script>";
    $var = "<script>document.write(confirm);</script>";
    $yes = "<script>document.write(yes);</script>";
    echo $var;
    if($var == $yes) {return(TRUE);}
    return (FALSE);
}
