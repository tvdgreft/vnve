<?php
namespace VNVE;
/**
 * prana_Ptext is gemaakt om de plugin eventueel later meertalig te maken.
 */
function prana_PText(string $short,string $long) : string 
{
	$html = '';
	$html .= $long;
	return($html);
}
function pranaParagraph($manual)
{
	$html = '';
	$file = PRANA_PLUGINPATH . '/paragraphs/' .  $manual;
	if(!file_exists($file)) { return("no paragraph file"); }
	$fh = fopen($file, 'r');
	$html= fread($fh, filesize($file));
	fclose($fh);
	return($html);
}
function pranaToggleHelp($manual)
{
	$html = '';
	if(!file_exists($manual)) { return(""); }
	$fh = fopen($manual, 'r');
	$help = fread($fh, filesize($manual));
	$html .= '<p onclick="ToggleFilters(\'helpbox\')"><a class="prana-button">HELP</a></p>';
	$html .= '<div id="helpbox" class="isa_info" style="display:none" >';
	$html .= $help;
	$html .= '</div>';
	return($html);
}
function pranaHelpModal($manual)
{
	$html = '';
	$html = HelpModal($file);
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
/**
 * pranaSendMail - send mail with joomla mailer
 * @param array $args[
 * 'to' => (string) recipient
 * 'subject' => (string) subject
 * 'body' => (string) object
 * 'attachement' => (string) file to be enclosed
 *
 */
 function pranaSendMail($args)
{	
	$html = '';
	#echo "mailto:" . $to . "<br>";

	$attachements = isset($args["attachements"]) ? $args["attachements"] : "";
	$headers = array('Content-Type: text/html; charset=UTF-8');

	if(PRANA_CMS == "wordpress")
	{
		if ( wp_mail( $args["to"], $args["subject"], $args["body"], $headers, $attachements ) ) 
		{
			$html .= 'E-mail succesvol verzonden!';
		} 
		else 
		{
			$html .= 'E-mail verzenden mislukt.';
		}
		return($html);
	}
	if(PRANA_CMS == 'joomla')
	{
		$mailer = \JFactory::getMailer();
		$config = \JFactory::getConfig();
		$sender = array( $config->get( 'mailfrom' ),$config->get( 'fromname' ) );
		$mailer->setSender($sender);
		$mailer->addRecipient($args['to']);
		$mailer->setSubject($args['subject']);
		$mailer->setBody($args['body']);
		$mailer->isHTML();
		#echo "attachement:" . $attachement . "<br>";
		if(isset($args['attachement'])) { $mailer->addAttachment($args['attachement']); }
		#echo "start sending (sendmail is off):" . $args['subject'] . "<br>";
		$send = TRUE;
		$send = $mailer->Send();   #TRUE or FALSE
		#echo "done<br>";
		return($send);
	}
}

#
# insert a log recordd
#
function pranaLog($args)
{
    $dbio = new DBIO;
    $dbio-> CreateRecord(array("table"=>Dbtables::logtable['name'] ,"fields"=>$args));
    return;
}
function pranaMenuLink($args)
{
    $dbio = new DBIO;
    $menu = $dbio->ReadRecord(array("table"=>"menu","id"=>$args["menu"]));
	$linkurl=\JRoute::_("index.php?Itemid={$args['menu']}");
    if(isset($args["key"])) { $linkurl .= '?key=' . $args["key"]; }
    $text = isset($args["text"]) ? $args["text"] : $menu->title;
	$l = '<p><a href="' . $linkurl . '">' . $text . '</a></p>';
	return($l);
}
/**
 * get dutch dat like vrijdag 27 maart 1947
 * sefull when setlocale($time,"nl_NL") is not installed on server
 */
function Dutchdate($time)
{
	$arrayday = array("maandag","dinsdag","woensdag","donderdag","vrijdag","zaterdag","zondag");
	$arraymonth = array("januari","februari","maart","april","mei","juni","juli","augustus","september","oktober","november","december");
	$dayofmonth=date('j',$time);
	$day = date("N",$time) -1;
	$month = date("n",$time)-1;
	$year = date("Y",$time);
	$datum = $arrayday[$day] . ' ' . $dayofmonth . ' ' . $arraymonth[$month] . ' ' . $year;
	return($datum);
}
/**
	 * pranaStrSplit
	 * Split a string at a space in strings of a maximum length
	 */
function pranaStrSplit(string $string,int $maxlength,string $sep)
{
	$length = 0;
	$substring = '';
	$strings=array();
	if(!str_contains($string,$sep))
	{
		array_push($strings,$string);
		return($strings);
	}
	$words=explode($sep,$string);
	foreach($words as $word)
	{
		$wl = strlen($word);
		$length += $wl + 1;
		if($length > $maxlength)
		{
			array_push($strings,$substring);
			$length = 0;
			$substring = '';
		}
		$substring .= $word . $sep;
	}
	if($substring != '')
	{
		array_push($strings,$substring);
	}
	return($strings);
}
function prana_ParseArgs($nargs,$default)
{
	$args=$default;
	foreach ($nargs as $arg=>$value)
	{
		$args[$arg] = $value;
	}
	return($args);
}
function prana_FileExtension($file)
{
	if(empty($file)) { $ext = ''; }
	elseif(filter_var($file, FILTER_VALIDATE_URL)) { $ext = 'link';  }
	else { $ext = substr(strrchr($file, '.'), 1); }	#the extension of the file
	return($ext);
}
function prana_DisplayTabel(array $table,array $headers) : string
{
	$html='';
	#print_r($table);
	$rows = count($table);
	#echo '<br>rows:'.$rows;
	$cols = count($headers);
	$html .= '<table class="compacttable">';
	$html .= '<tr>';
	for ($col=0; $col < $cols; $col++)
	{
		$thclass = "compactth";
		$type = $headers[$col][1] ? $headers[$col][1] : "string";	// default type is string
		if($type == "number" || $type == "euro") {$thclass = "compactthright"; }	// getallen rechts aansluiten
		$html .= '<th class="' . $thclass . '">' . $headers[$col][0] . '</th>';
	}
	for($row=0; $row<=$rows; $row++)
	{			
		$html .= '<tr class="compacttr">';
		for ($col=0; $col <$cols; $col++)
		{
			$tdclass = "compacttd";
			$type = $headers[$col][1] ? $headers[$col][1] : "string";	// default type is string
			if($type == "number" || $type == "euro") {$tdclass = "compacttdright"; }	// getallen rechts aansluiten
			$cel = '';
			if(isset($table[$row][$col]))
			{
				$cel = $table[$row][$col];
				if($type == "euro") 	{ $cel = $this->Euro($table[$row][$col]); }
				else 					{ $cel = $table[$row][$col];}
			}
			$html .= '<td class="' . $tdclass . '">' . $cel . '</td>';
		}
	}
	$html .= '</table>';
	return($html);
}
/**
*  converteert centen naar euro notatie (vb: 34560 = 345,60)
 */
function prana_Euro(string $cents) : string
{
    $html = '';
    if($cents == '') { return(''); }
    $euro = number_format((abs($cents) /100), 2, ',', '.');
    if($cents < 0) {$html .= '-';}
    $html .= $euro;
    return($html);
}
function prana_UrlExists($url) 
{
	if( strpos( $url , "localhost" ) == TRUE) 
	{
		$url = str_replace("https","http",$url);	#localhost is http in stead of https
	}
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_NOBODY, true);
	curl_exec($ch);
	$result = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE); 
	curl_close($ch);
	if($result != FALSE && $result != "404") { return TRUE; }
	return FALSE;
}
