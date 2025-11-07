<?php
namespace VNVE;
#
# Beheer van grenenloos tabel
#
class Grenzenloos extends tableform
{
    public $fields = array();    #content of fields of a record which is modifie
    public $header_manager = "<h1>Beheer van alle artikelen</h1>";
    public $prefix;
    public $form;
    /**
     * Function Start
     * Will be started by function OnShortCode in bootstrap,php
     * Which means that this function will be started by shorcode in an joomla article: {docman}
     * A shortcode can be accompanied by arguments : {docman prefix="grens" function="search"}
     * prefix and function are reserved arguments
     * prefix is a prfix for the databasetable
     * function is a special function
     * In this case: function="manager" (default function)
     *               function="publicsearch" (search function for the public.)
     */
	public function Start($args)
	{
        $html = '';
        $options = new options();
        $this->single = 'titel';
        $this->plural = 'titels';
        $this->class = "grenzenloos";
        $this->form = isset($args['form']) ? $args['form'] : "";		#customized form
		$this->prefix = isset($args['prefix']) ? $args['prefix'].'_': "";		#prefix given for databasetable
        $this->params = $options->Parameters();						#read all parameters
        #print_r($this->params);
        #echo '<br>';
		$this->table = $this->prefix . Dbtables::titels['name'];
        $this->primarykey="id";	#the primary key of the records
        if(!isset($args['task']))
        {
            $html .= $this->Manager($args);
        }
        else
        {
            switch ($args['task'])
            {
               case "manager":
                    $html .= $this->Manager($args);
                    break;

                case "publicsearch":
                    $html .= $this->PublicSearch($args);
                     break;

                case "info":
                    $html .= phpinfo();
                    break;

                default:
                    $html .= $this->Manager($args);
                    break;
            }
        }
        return($html);
    }
     /**
     * * Start is the standard function for maintaining a databasetable.
     * This function is a important tool for the webmanager to maintain a table
     */
    public function Manager($args)
    {
        $html='';
        $this->columns=
		[
			["id","id","int"],         #table column name, columnname to be displayed, display orientation
			["nummer","nummer","int"],
			["oudnummer","oudnummer","int"],
			["seizoen","seizoen","string"],
			["auteur","auteur","string"],
			["titel","titel","string"],
			["artikel","bekijk bestand","download"],   #set viewer button
		];
		$this->filtercolumns=
		[
			["nummer","nummer","text"],
			["oudnummer","oudnummer","text"],
			["seizoen","seizoen","dropdown"],
			["auteur","auteur","text"],
			["titel","titel","text"],
			["artikel","bestand","text"],   #set viewer button
		];
		#$this->filtercolumns = array("nummer"=>"nummer","oudnummer"=>"oudnummer","seizoen"=>"seizoen","titel"=>"titel","auteur"=>"auteur","artikel"=>"bestand");
        $this->permissions = ["vw","cr","md","dl","xp"];
        $this->num_rows=explode(',',$this->params['numrows']);   #default rows per page
        $this->rows_per_page = $this->num_rows[1];
        $html .= $this->header_manager;
        $html .= $this->MaintainTable(); # start or restart tableform
        return($html);
    }
    /**
     * frontend function for the users. 1 form field for searching in multiple fields
     */
    public function PublicSearch($args)
    {
        $html='';
		/**
		test owlcarousel
		
        $pdf = '<figure>';
        $url = PRANA_PLUGINURL . '/documents/-4+52-01-03.pdf';
		$pdf .= sprintf('<iframe src="%s#page=1" type="application/pdf" width="400" height="400"',$url);
		$pdf .= '></iframe>';
		$download = sprintf('<a href="%s">%s</a>',$url,'onderschrift');
		$pdf .= sprintf('<figcaption>%s</figcaption>',$download);
		$pdf .= '</figure>';
        $html .= '<div class="owl-carousel owl-theme">';
        $html .= '<div class="item"><img src="' . PRANA_PLUGINURL . '/images/carousel/armenie.jpg" alt="armenie"></div>';
        $html .= '<div class="item"><img src="' . PRANA_PLUGINURL . '/images/carousel/scan duitsland 2022-1 001.jpg" alt="armenie"></div>';
        $html .= '<div class="item">' . $pdf . '</div>';
        $html .= '<div class="item"><img src="' . PRANA_PLUGINURL . '/images/carousel/armenie.jpg" alt="armenie"></div>';
        $html .= '<div class="item"><img src="' . PRANA_PLUGINURL . '/images/carousel/armenie.jpg" alt="armenie"></div>';
        $html .= '<div class="item"><img src="' . PRANA_PLUGINURL . '/images/carousel/armenie.jpg" alt="armenie"></div>';
        $html .= '<div class="item"><img src="' . PRANA_PLUGINURL . '/images/carousel/armenie.jpg" alt="armenie"></div>';
        $html .= '<div class="item"><img src="' . PRANA_PLUGINURL . '/images/carousel/armenie.jpg" alt="armenie"></div>';
        $html .= '<div class="item"><img src="' . PRANA_PLUGINURL . '/images/carousel/armenie.jpg" alt="armenie"></div>';
        $html .= '<div class="item"><img src="' . PRANA_PLUGINURL . '/images/carousel/armenie.jpg" alt="armenie"></div>';

        $html .= '</div>';
        $html .= '<div class="btns">';
        $html .= '<div class="customNextBtn"><i class="fa fa-arrow-right" aria-hidden="true"></i>
</div>';
        $html .= '<div class="customPreviousBtn"><i class="fa fa-arrow-left"></i>
</div>';
        $html .= '</div>';
        return($html);
		*/
        $forms = new Forms();
        /**
         * columns te be diaplayed
         */
        $this->columns= 
		[
        ["nummer","nummer","string"],
		["oudnummer","oudnummer","string"],
			["seizoen","seizoen","string"],
			["titel","titel","string"],
			["auteur","auteur","string"],
			["artikel","bekijk bestand","viewer"],   #set viewer button
			["artikel","download bestand","download"],   #set downloadbutton
        ];
        /**
         * columns to be searched 
         */
		$this->searchcolumns = array("nummer","oudnummer","seizoen","titel","auteur");
        $this->permissions = ["vw"];
        $this->rows_per_page=10;
        $this->num_rows=explode(',',$this->params['numrows']);
        $this->rows_per_page = $this->num_rows[1]; #default rows per page
        $this->searchword = TRUE; # search on words
        #
        # test area
        #
        #$dbio = new DBIO();
        #$id = $dbio->CreateRecord(array("table"=>$this->table,"fields"=>array("nummer"=>"99","oudnummer"=>"999","titel"=>"mijntitel","auteur"=>"theo","bladzijden"=>"blad1")));
        #$html .= "id=".$id;
        #$dbio = new DBIO();
        #$result = $dbio->ModifyRecord(array("table"=>$this->table,"key"=>"id","value"=>"3540","fields"=>array("nummer"=>"22","oudnummer"=>"222","titel"=>"titel2")));
        #$html .= "result=".$result;
        #return($html);
        $html .= $this->SearchTable(); # Just search and display records
        return($html);
    }
    /**
     * Display form for maintaining a record 
     * Will be started from tableform
     * $crmod = "create" of "modify"
     */
    public function FormTable($crmod) : string
	{
        $forms = new Forms();
        $html = '';
        $html .= '<div class="prana-display">';
        $html .= '<br>';
        $form = PRANA_PLUGINPATH . '/forms/grenzenloos.xml';
        if($this->form) {$form = PRANA_PLUGINPATH . '/forms/' . $this->form; } #custom form
        $xml = simplexml_load_file($form) or die("Error: Cannot create object");
        $this->fields['currentartikel'] = $this->fields['artikel'];     #copy artticle name for form
        $html .= $forms->Makeform(array("form"=>$xml,"values"=>$this->fields));
        #
		# checkbox for deleting artikel
		#
		if($this->fields['artikel'])
		{
            $html .= $forms->Check(array("label"=>prana_Ptext("verwijderen","bestand verwijderen"), "id"=>"deleteartikel", "width"=>"300px;","checked"=>FALSE,"required=>FALSE","confirm"=>"Bestand echt verwijderen?"));
		}
		$forms->buttons = [
							['id'=>'writerecord','value'=>prana_Ptext("opslaan","opslaan"), "onclick"=>"buttonclicked='store'"],
                            ['id'=>'cancel','value'=>prana_Ptext("annuleren","annuleren"),"status"=>"formnovalidate","onclick"=>"buttonclicked='cancel'"]
                        ];
		$html .= $forms->DisplayButtons();
        $html .='<input id="crmod" name="crmod" value="' . $crmod . '" type="hidden" />';
        $html .= '</div>';
        if(isset($this->fields['id'])) { $html .='<input id="primarykey" name="primarykey" value="' . $this->fields['id'] . '" type="hidden" />';}    #set primary
        return($html);
    }
    /**
     * DocumentExist() : bool
     */
    public function DocumentExist($id,$document) : string
    {
		if(empty($document) ){ return(0); }
        $file = PRANA_PLUGINPATH . '/' . $this->params['docdir'] . '/' . $document;
        if(!file_exists($file)) { return(""); }
        $file = PRANA_PLUGINURL . '/' . $this->params['docdir'] . '/' . $document;
        return($file);
    }
    /**
    * Download a document
    */
    public function DownloadDocument($document) : string
    {
        $form = new Forms();
        $html = '';
        $file = PRANA_PLUGINPATH . '/' . $this->params['docdir'] . '/' . $document;
        $html .= $form->DownloadFile($file);
        return($html);
    }
     /*
        Download viewer
    */
    public function DocumentViewer($document) : string
    {
        $html = '';
        $url = PRANA_PLUGINURL . '/' . $this->params['docdir'] . '/' . $document;
        $file = PRANA_PLUGINPATH . '/' . $this->params['docdir'] . '/' . $document;
        if(!file_exists($file))
		{
			$error = sprintf('bestand %s bestaat niet',$file);
			$html .= '<div class="isa_error" >' . $error . '</div>';
		}
        else
        {
			$url = str_replace("\\","",$url);   #remove backslahes
            $html .= '<iframe src="'.$url.'" height="750" width="750"></iframe>';
        }
        $html .= '<br><button class="prana-btnsmall" id="continue" name="continue">terug naar overzicht</button><br><br>';
        return($html);
    }
    /*
        Check the input of the form
    */
    public function CheckModify() : bool
    {
        $form = new Forms();
        $dbio = new DBIO();
        $targetdir = PRANA_PLUGINPATH . '/' . $this->params['docdir'];
        $filetypes = get_option('filetypes');
        $maxdocsize = get_option('maxdocsize');
        /**
         * delete the artikel from record and from store if no other records using it.
         */
        if(isset($_POST['deleteartikel']))
        {
            $response = $this->DeleteArticle($_POST['artikel']);
            $_POST['artikel'] = '';
        }
        /**
         * Upload the file which is choosen in inputform.
         */
        #print_r($_FILES);
        foreach ($_FILES as $file)
        {
            switch($file['error'])
            {
                case '0':

                if ( $form->UploadFile(array("file"=>$file,"targetdir"=>$targetdir,"filetypes"=>$filetypes,"maxkb"=>$maxdocsize,"overwrite"=>TRUE)) == FALSE)
                {
                    pranaAlert($form->uploaderror);
                    return(FALSE);
                }
				$_POST['artikel'] = str_replace("'","\'",$file['name']);   #replace ' by \'
                break;

                case '1':
                $error = sprintf("fout bij uploaden %s.<br>Bestand groter dan upload_max_filesize directive in php.ini (nu %d Mb)",$file['name'],ini_get('upload_max_filesize'));
				pranaAlert($error);
				return(FALSE);

                case '4':
                break;

                default:
                $error = sprintf("fout bij uploaden %s.<br>Error code %d",$file['name'],$file['error']);
				pranaAlert($error);
				return(FALSE);

			}
        }
        return(TRUE);
    }
    /**
	*	Wat doen we nadat een record is verwijderd?
	**/
	public function AfterDelete($id)
	{
        $dbio = new DBIO();
		$c=$dbio->ReadRecord(array("table"=>$this->table,"id"=>$id));
        if($c->artikel) { $this->DeleteArticle($c->artikel); }
		return;
	}
    /**
     * Delete an article only when there are no other records pointing to
     */
	public function DeleteArticle($article)
	{
        $dbio = new DBIO();
        $file = PRANA_PLUGINPATH . '/' . $this->params['docdir']. '/' . $article;;
		#Zijn er andere artikelen die naar hetzelfde bestand wijzen?
		$articlerecords = $dbio->ReadRecords(array("table"=>$this->table,"where"=>array("artikel"=>$article)));
        $nar = count($articlerecords);

		# Alleen bestand verwijderen als er geen andere artikelen meer naar toewijzen.
        if($nar == 0)
        {
            pranaAlert(sprintf(prana_Ptext('x','Bestand %s onbekende fout'),$article));
        }
		elseif($nar == 1)
		{
            if(!file_exists($file))
            {
                pranaAlert(sprintf(prana_Ptext('x','Bestand %s bestaat niet'),$article));
                return(0);
            }
			if (unlink($file)) 	{ pranaAlert(sprintf(prana_Ptext('x','Bestand %s verwijderd'),$article)); }
			else				{ pranaAlert(sprintf(prana_Ptext('x','Fout bij verwijderen Bestand %s'),$article)); }
		}
		else
		{
            pranaAlert(sprintf(prana_Ptext('x','Bestand %s niet verwijderd , omdat %d ander(e) record(s) naar toe verwijzen'),$article,$nar));
			return($nar);
		}
	}
}
?>