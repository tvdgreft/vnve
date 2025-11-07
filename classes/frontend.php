<?php
/**
 * main
 * function init will be started from bootstrap.php
 **/
namespace VNVE;

 
class FrontEnd
{	
	public $backgroundcolor;	#backgroundcolor of table and forms
	public $organisation;		#organisationname
	public $prefix;				#prefix of vnve tables

	public function init($args)
	{
		GLOBAL $wp;
		$scripts = new scripts;
        $html = '';
		$GLOBALS['prefix'] = $this->prefix = isset($args['prefix']) ? $args['prefix'].'_': "";		#prefix given for databasetable
		$html .= $scripts->LoadScripts();			#load scripts
		/**
		 * which function should be started
		 */
		if(!isset($args['function']))
        {

            $error = "Geen functie opgegeven in plugin shortcode";
			$html .= '<div class="isa_error" >' . $error . '</div>';
        }
        else
        {
            switch ($args['function'])
            {
				case "docman":
				$html .= $this->Docman($args);
				break;

               	case "grenzenloos":
				$html .= $this->Grenzenloos($args);
				break;

				case "leden":
				$html .= $this->Leden($args);
				break;

				default:
                $error = sprintf('unknownfunction %s onbekende functie',$args['function']);
				$html .= '<div class="isa_error" >' . $error . '</div>';
                break;
			}
			
		}
		return($html);
	}
	/***
	 * documents
	 */
	public function Docman($args)
	{
		$bootstrap=new bootstrap();
		$html = '';
		/**
		* create table if not exist
		**/
		$dbio = new DBIO;
		if($dbio->CreateTable($this->prefix. Dbtables::docman['name'],Dbtables::docman['columns']) == FALSE)
		{
			$html .= 'database error';
			return($html);
		}
		$function = isset($args['function']) ? $args['function'] : '';
		$task = isset($args['task']) ? $args['task'] : '';
		$html .= '<form role="form" action=' . bootstrap::CurrentUrl() . ' method="post" enctype="multipart/form-data" onSubmit="return ValForm()">';
		$docman = new Docman;
		$html .= $docman->start($args);
		$html .= '</form>';
        return($html);
	}
	/***
	 * grenzeloos is een lijst van onderwerpen met vetwijzingen naar documenten.
	 */
	public function Grenzenloos($args)
	{
		$bootstrap=new bootstrap();
		$html = '';
		/**
		* create table if not exist
		**/
		$dbio = new DBIO;
		if($dbio->CreateTable($this->prefix. Dbtables::titels['name'],Dbtables::titels['columns']) == FALSE)
		{
			$html .= 'database error';
			return($html);
		}
		$helpfile = PRANA_PLUGINPATH . '/doc/manual';
		$helpfile = isset($args['function']) ? $helpfile . '_' . $args['function'] : $helpfile;
		$helpfile = isset($args['task']) ? $helpfile . '_' . $args['task'] :  $helpfile;
		$helpfile .= '.html';
		if(file_exists($helpfile)) { $html .= HelpModal($helpfile); }
		$html .= '<form role="form" action=' . bootstrap::CurrentUrl() . ' method="post" enctype="multipart/form-data" onSubmit="return ValFormGrenzenloos()">';
		$grenzenloos = new GRENZENLOOS;
		$html .= $grenzenloos->start($args);
		$html .= '</form>';
        return($html);
	}
	/***
	 * lidmaatschap vnve
	 */
	public function Leden($args)
	{
		$html = '';
		/**
		* create table if not exist
		**/
		$dbio = new DBIO;
		#$dbio -> CreateTable($this->prefix . Dbtables::leden['name'],Dbtables::leden['columns']);
		$html .= '<form role="form" action=' . bootstrap::CurrentUrl() . ' method="post" enctype="multipart/form-data" onSubmit="return ValForm()">';
		$leden = new Leden;
		$html .= $leden->start($args);
		$html .= '</form>';
        return($html);
	}
}