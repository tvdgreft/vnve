<?php
namespace VNVE;
#
# Maintain table of documents
#
class Docman extends Tableform
{
    protected $fields = array();    #content of fields of a record which is modified
	public $header_manager = "<h1>Beheer van alle documenten</h1>";
    protected $prefix;
    protected $form;
    
    /**
     * Function Start
     * Will be started by function OnShortCode in bootstrap,php
     * Which means that this function will be started by shorcode in an joomla document: {docman}
     * A shortcode can be accompanied by arguments : {docman prefix="grens" function="search"}
     * prefix and function are reserved arguments
     * prefix is a prfix for the databasetable
     * function is a special function
     * In this case: function="manager" (default function)
     *               function="publicsearch" (search function for the public.)
     */
	public function Start($args)
	{
        $options = new options();
        $this->single = 'titel';
        $this->plural = 'titels';
        $this->class = "docman";
        $this->form = isset($args['form']) ? $args['form'] : "";		#customized form
        $this->prefix = isset($args['prefix']) ? $args['prefix'].'_': "";		#prefix given for databasetable
        $this->params = $options->Parameters();						#read all parameters
        $this->table = $this->prefix . Dbtables::docman['name'];
        $this->primarykey="id";	#the primary key of the records
        if(!isset($args['task']))
        {
            $html = $this->Manager($args);
        }
        else
        {
            $html = "No Function" . $args["function"];
            switch ($args['task'])
            {
               case "manager":
                $html = $this->Manager($args);
                break;

                case "publicsearch":                   #search with one searchfield on multiple columns and display records with content
                $html = $this->PublicSearch($args);
                break;

                case "content":                   #search records with content of a certain columns and display records with content
                $html = $this->Content($args);
                break;

                case "carousel":                   #search records with content of a certain columns and display records with content
                $html = $this->Carousel($args);
                break;

                case "slides":                   #search records with content of a certain columns and display records with content
                $html = $this->Slides($args);
                break;
                
                case "mostrecent":
                $html = $this->MostRecent($args);
                break;
    
                default:
                $html = $this->Manager($args);
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
        #$dbio = new DBIO();
        #$table = 'grens_' . Dbtables::titels['name'];
        #$records=$dbio->DistinctRecords(array("table"=>$table,"column"=>"seizoen"));
        #print_r($records);
        $this->columns=
		[
			["id","id","string"],         #table column name, columnname to be displayed, display orientation
			["title","titel","string"],
            ["status","status","string"],
            ["rubric","rubriek","string"],
		];
		$this->filtercolumns = array("title"=>"titel","rubric"=>"rubriek","publisher"=>"uitgever");
        $this->permissions = ["vw","cr","md","dl"];
        $this->rows_per_page=10;
        $this->num_rows=explode(',',$this->params['numrows']);
        $this->rows_per_page = $this->num_rows[1]; #default rows per page
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
         * columns te be diaplayed
         */
        $this->columns= 
		[
            ["docid","docid","string"],
			["title","titel","string"],
			["pubdate","uitgifte","string"],
            ["rubric","rubriek","string"],
			["document","document bekijken","viewer"],
			["document","document downloaden","download"],
		];
        /**
         * columns to be searched 
         */
		$this->searchcolumns = array("title");
        if(isset($args['rubric']))
        { 
            $this->searchfilter = array("rubric"=>$args['rubric']);
        } 
        $this->permissions = ["vw"];
        $this->rows_per_page=10;
        $this->num_rows=explode(',',$this->params['numrows']);
        $this->rows_per_page = $this->num_rows[1]; #default rows per page
        $this->template = "table";
        $html .= $this->SearchTable(); # Just search and display records
        return($html);
    }
    /**
     * just display all records of a certein rubric or title
     * no search fields or button.
     */
    public function Content($args)
    {
        $html='';
        /**
         * columns te be diaplayed
         */
        $this->columns= 
		[
			["title","titel","string"],
			["pubdate","uitgifte","string"],
            ["expirationdate","verloopdatum","string"],
            ["rubric","rubriek","string"],
			["document","document bekijken","viewer"],
			["document","document downloaden","download"],
		];
        /**
         * columns to be searched 
         */
		if(isset($args['rubric']))
        { 
            $this->searchcolumns = array("rubric");
            $this->onsearch = $args['rubric'];
            $this->permissions = ["vw"];
            $html .= $this->DisplayContent(); # Just search and display records
        }
        return($html);
    }
    /**
     * make a slideshow of pdf files of records withi a certain rubric
     */
    public function Slides($args)
    {
        $html='';
        /**
         * columns te be diaplayed
         */
        $this->columns= 
		[
			["document","document bekijken","viewer"],
		];
        /**
         * columns to be searched 
         */
        if(isset($args['rubric']))
        { 
            $this->searchcolumns = array("rubric");
            $this->onsearch = $args['rubric'];
            $this->permissions = ["vw"];
            $this->template="slides";
            $html .= $this->DisplayContent(); # Just search and display records
        }
        return($html);
    }
    /**
     * make a slideshow of pdf files of records withi a certain rubric
     */
    public function Carousel($args)
    {
        $html='';
        /**
         * columns te be diaplayed
         */
        $this->columns= 
		[
			["document","document bekijken","viewer"],
		];
        /**
         * columns to be searched 
         */
        if(isset($args['rubric']))
        { 
            $this->searchcolumns = array("rubric");
            $this->onsearch = $args['rubric'];
            $this->permissions = ["vw"];
            $this->template="carousel";
            $html .= $this->DisplayContent(); # Just search and display records
        }
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
        $form = PRANA_PLUGINPATH . '/forms/docman.xml';
        if($this->form) {$form = PRANA_PLUGINPATH . '/forms/' . $this->form; } #custom form
        $xml = simplexml_load_file($form) or die("Error: Cannot create object");
        $this->fields['currentdocument'] = $this->fields['document'];     #copy documentname for form
        $html .= $forms->Makeform(array("form"=>$xml,"values"=>$this->fields));
        #
		# checkbox for deleting document
		#
		if($this->fields['document'])
		{
            $html .= $forms->Check(array("label"=>"bestand verwijderen", "id"=>"deletedocument", "width"=>"300px;","checked"=>FALSE,"required=>FALSE","confirm"=>"Bestand echt verwijderen?"));
		}
		$forms->buttons = [
							['id'=>'writerecord','value'=>"opslaan", "onclick"=>"buttonclicked='store'"],
                            ['id'=>'cancel','value'=>"annuleren","status"=>"formnovalidate","onclick"=>"buttonclicked='cancel'"]
                        ];
		$html .= $forms->DisplayButtons();
        $html .='<input id="crmod" name="crmod" value="' . $crmod . '" type="hidden" />';
        if(isset($this->fields['id'])) { $html .='<input id="primarykey" name="primarykey" value="' . $this->fields['id'] . '" type="hidden" />';}    #set primary
        $html .= '</div>';
        return($html);
    }
    /**
     * DocumentExist() : bool
     */
    public function DocumentExist($id,$document) : string
    {
        $dbio = new DBIO();
        $c=$dbio->ReadRecord(array("table"=>$this->table,"id"=>$id));
        if($c->status == "restricted") { return(0); }               #file is restricted
        #
        # test of document is verlopeb
        #
        if($c->expirationdate)
        {
            $currenttime = strtotime(date('Y-m-d'));
            $expirationtime = strtotime($c->expirationdate);
            if($c->expirationdate != "0000-00-00" && $currenttime > $expirationtime) { return(0); }
        }
        $url=$document;
        if(strchr($document,"http") == FALSE)    #if $document is not an url, document should be in docdir
        { 
            $url = PRANA_PLUGINURL . '/' . $this->params['docdir'] . '/' . $document;
        }
        if(prana_UrlExists($url) == false) { return(""); }
        return($url);
    }
    /**
    * Download a document
    */
    public function DownloadDocument($document) : string
    {
        $form = new Forms();
        $html = '';
        $url=$document;
        if(strchr($document,"http") == FALSE)    #if $document is not an url, document should be in docdir
        { 
            $url = PRANA_PLUGINURL . '/' . $this->params['docdir'] . '/' . $document;
        }
        if(prana_UrlExists($url) == false) 
        { 
            $html .= $form->DownloadFile($file);
        }
        return($html);
    }
      /*
        Download viewer
    */
    public function DocumentViewer($document) : string
    {
        $html = '';
        $url=$document; 
        if(strchr($document,"http") == FALSE)    #if $document is not an url, document should be in docdir
        { 
            $url = PRANA_PLUGINURL . '/' . $this->params['docdir'] . '/' . $document;
        }
        if(prana_UrlExists($url) == false) 
        { 
			$error = sprintf('bestand %s bestaat niet',$file);
			$html .= '<div class="isa_error" >' . $error . '</div>';
		}
        else
        {
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
         * delete the document from record and from store if no other records using it.
         */
        if(isset($_POST['deletedocument']))
        {
            $response = $this->DeleteDocument($_POST['currentdocument']);
            $_POST['document'] = '';
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
                $_POST['document'] = $file['name'];
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
        $this->DeleteDocument($c->document);
		return;
	}
    /**
     * Delete an document only when there are no other records pointing to
     */
	public function DeleteDocument($document)
	{
        $dbio = new DBIO();
        $file = JPATH_SITE . $GLOBALS['docdir'] . '/' . $document;
		#Zijn er andere documenten die naar hetzelfde bestand wijzen?
		$documentrecords = $dbio->ReadRecords(array("table"=>$this->table,"where"=>array("document"=>$document)));
        $nar = count($documentrecords);

		# Alleen bestand verwijderen als er geen andere documenten meer naar toewijzen.
        if($nar == 0)
        {
            pranaAlert(sprintf('Bestand %s onbekende fout',$document));
        }
		elseif($nar == 1)
		{
            if(!file_exists($file))
            {
                pranaAlert(sprintf('Bestand %s bestaat niet',$document));
                return(0);
            }
			if (unlink($file)) 	{ pranaAlert(sprintf('Bestand %s verwijderd',$document)); }
			else				{ pranaAlert(sprintf('Fout bij verwijderen Bestand %s',$document)); }
		}
		else
		{
            pranaAlert(sprintf('Bestand %s niet verwijderd , omdat %d ander(e) record(s) naar toe verwijzen',$document,$nar));
			return($nar);
		}
	}
    /**
     * display most recent documents
     */
    public function MostRecent($args)
	{
        $html='';
        $dbio = new DBIO();
        $html .= "<h2>Meest recente documenten</h2>";
        $documents = $dbio->ReadRecords(array("table"=>$this->table,"page"=>1,"maxlines"=>10,"sort"=>"pubdate DESC"));
        foreach($documents as $d)
        {
            $date = DutchDate(strtotime($d->pubdate));
            $html .= $date;
            $href = \JURI::base().$GLOBALS['docdir'] . "/" . $d->document;
			$html .= '<br><a style="font-size: 14px;" href="'.$href.'">'. $d->title . '</a><br>';
        }
        return($html);
    }
}
?>
