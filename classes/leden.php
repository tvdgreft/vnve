<?php
namespace VNVE;
#
# lidmaatschap VNVE
#
class Leden extends Tableform
{
    protected $fields = array();    #content of fields of a record which is modified
    protected $prefix;
	public function Start($args)
	{
		$options = new options();
        $html = '';
        $this->single = 'lid';
        $this->plural = 'leden';
        $this->class = "leden";
        $this->prefix = isset($args['prefix']) ? $args['prefix'].'_': "";		#prefix given for databasetable
		$this->params = $options->Parameters();						#read all parameters
        $this->table = $this->prefix . Dbtables::leden['name'];
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

                case "formulier":
                $html .= $this->Formulier($args);
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
			["id","id","string"],         #table column name, columnname to be displayed, display orientation
            ["naam","naam","string"],
            ["email","email","string"],
		];
		$this->filtercolumns = array("email"=>"email");
        $this->permissions = ["vw"];
        $this->num_rows=explode(',',$GLOBALS['numrows']);   #default rows per page
        $this->rows_per_page = $this->num_rows[1];
        $html .= $this->MaintainTable(); # start or restart tableform
        return($html);
    }
   
    /**
     * Display form for maintaining a record 
     * Will be started from tableform when record is modifief or created
     * $crmod = "create" of "modify"
     */
    public function FormTable($crmod) : string
	{
        $form = new Forms();
        $html = '';
        
        $html .= '<div class="prana-display">';
        $html .= '<br>';
        if($crmod == "modify")
        {
            $html .= $form->Text(array("label"=>prana_Ptext("id","id"), "id"=>"primarykey", "value"=>$this->fields['id'], "width"=>"100px;", "readonly"=>TRUE));
        }
		$form->buttons = [
							['id'=>'writerecord','value'=>prana_Ptext("opslaan","opslaan"), "onclick"=>"buttonclicked='store'"],
                            ['id'=>'cancel','value'=>prana_Ptext("annuleren","annuleren"),"status"=>"formnovalidate","onclick"=>"buttonclicked='cancel'"]
                        ];
		$html .= $form->DisplayButtons();
        $html .='<input id="crmod" name="crmod" value="' . $crmod . '" type="hidden" />';
        $html .= '</div>';
        return($html);
    }
    /** 
     * Intro tekst
    */
    public function IntroFormulier() : string
    {
        $html = '';
        $html .= pranaHelp("leden.html");
        return($html);
    }
    /**
     * formulier lidmaatschap VNVE
     */
    public function Formulier($args)
    {
        $html='';
        #
        # Form is filled, handle the content
        #
        if(isset($_POST['verstuur']))
        {
            $html .= $this->Verstuur($args);
            return($html);
        }

        $forms = new Forms();
        $html .= '<div class="prana-display">';
        $html .= '<h2>' . pranaParagraph("ledenheader.html") . '</h2>';
        $forms->formdefaults['collabel']='col-md-3';
		$forms->formdefaults['colinput']='col-md-5';
        #
        # load the form in xml
        #
		$form = PRANA_PLUGINPATH . '/forms/leden.xml';
		$xml = simplexml_load_file($form) or die("Error: Cannot create object");
        $html .= $forms->Makeform(array("form"=>$xml));
        $forms->buttons = [
            ['id'=>'verstuur','value'=>"verstuur", "onclick"=>"buttonclicked='store'"],
            ['id'=>'cancel','value'=>"annuleren","status"=>"formnovalidate","onclick"=>"buttonclicked='cancel'"]
        ];
        $html .= pranaParagraph("ledenfooter.html");
        $html .= $forms->DisplayButtons();
        $html .= '<div>';
        return($html);
    }
      
    /*
        store request in table
    */
    public function Verstuur()
    {
        $html='';
        $forms = new Forms();
		$form = PRANA_PLUGINPATH . '/forms/ledentest.xml';
		$xml = simplexml_load_file($form) or die("Error: Cannot create object");
		$file = PRANA_PLUGINPATH . '/tmp/lid' . '_' . date('YmdHis');    #Make unique file
        $outputfile = $file . ".pdf";
        $html .= $forms->PrintForm(array("form"=>$xml,"output"=>"pdf","header"=>pranaParagraph("ledenheader.html"),"file"=>$outputfile));
        $subject = sprintf("formulier lidmaatschap");
        $bodyadmin = sprintf('Lidmaatschaps formulier van %s %s',$_POST["voorletters"],$_POST["achternaam"]);
        $body = pranaParagraph("ledenmail.html");
        $html .= pranaSendMail(array("to"=>$this->params['emailleden'],"subject"=>$subject,"body"=>$bodyadmin,"attachement"=>$outputfile));
        #pranaSendMail(array("to"=>$_POST['email'],"subject"=>$subject,"body"=>$body,"attachement"=>$outputfile));
        $html .= pranaParagraph("ledenfooter.html");
        return($html);
    }
    /**
     * 
      *  Check the input of the form
    */
    public function CheckModify() : bool
    {
        return(TRUE);
    }
    /**
	*	Wat doen we nadat een record is verwijderd?
	**/
	public function AfterDelete($id)
	{
		return;
	}
}
?>
