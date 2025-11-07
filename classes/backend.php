<?php
/**
 * main
 * function init will be started from bootstrap.php
 **/
namespace VNVE;

 
class BackEnd
{	
	public $backgroundcolor;	#backgroundcolor of table and forms
	public $organisation;		#organisationname
	public $action;
	public $prefix;

	public function init($args)
	{
		GLOBAL $wp;
		$scripts = new scripts;
        $html = '';
		$GLOBALS['prefix'] = $this->prefix = isset($args['prefix']) ? $args['prefix'].'_': "";		#prefix given for databasetable
		$this->backgroundcolor=get_option('backgroundcolor');	#from options in background
		$this->organisation=get_option('organisation');			#from options in background
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

				case "backup":
				$html .= $this->Backup($args);
				break;

				default:
                $error = sprintf('unknownfunction %s onbekende functie',$args['function']);
				$html .= '<div class="isa_error" >' . $error . '</div>';
                break;
			}
			
		}
		echo $html;
		return;
	}
		/***
	 * backup/restore plugin files
	 */
	public function Backup($args)
	{
		$html = '';
		$slug = $args['action'];
		$html .= '<form role="form" action=' . $slug . ' method="post" enctype="multipart/form-data" onSubmit="return ValFormGHELP()">';
		$backup = new Backup;
		$html .= $backup->start($args);
		$html .= '</form>';
		echo $html;
        return;
	}
	/***
	 * docman manager
	 */
	public function Docman($args)
	{
		$bootstrap=new bootstrap();
		$scripts = new scripts;
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
		$slug = $args['action'];
		$html .= '<form role="form" action=' . $slug . ' method="post" enctype="multipart/form-data" onSubmit="return ValFormDocman()">';
		$docman = new DOCMAN;
		$html .= $docman->start($args);
		$html .= '</form>';
		echo $html;
        return;
	}
	/***
	 * grenzeloos is een lijst van onderwerpen met vetwijzingen naar documenten.
	 */
	public function Grenzenloos($args)
	{
		$bootstrap=new bootstrap();
		$scripts = new scripts;
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
		$function = isset($args['function']) ? $args['function'] : '';
		$task = isset($args['task']) ? $args['task'] : '';
		$helpfile = PRANA_PLUGINPATH . '/doc/manual_' . $function . '_' . $task .'.html';
		if(file_exists($helpfile)) { $html .= pranaToggleHelp($helpfile); }
		$slug = $args['action'];
		$html .= '<form role="form" action=' . $slug . ' method="post" enctype="multipart/form-data" onSubmit="return ValFormGrenzenloos()">';
		$grenzenloos = new GRENZENLOOS;
		$html .= $grenzenloos->start($args);
		$html .= '</form>';
		echo $html;
        return;
	}
}