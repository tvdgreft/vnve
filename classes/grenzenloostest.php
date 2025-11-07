<?php
namespace VNVE;
#
# Beheer van grenenloos tabel
#
class Grenzenloos extends tableform
{
    protected $fields = array();    #content of fields of a record which is modified
    
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
        $forms = new forms();
        $html = '';
		$welkom = "<h1>Grenzenloos zoeken</h1>";
		$html .= $welkom;
        $x=prana_PText("xx","xxx");
		$form = bootstrap::PLUGINPATH . '/forms/formtest.xml';
		$xml = simplexml_load_file($form) or die("Error: Cannot create object");
		$html .= '<div class="prana-display">';
        $html .= '<br>';
        $html .= $forms->Makeform(array("form"=>$xml));
		$forms->buttons = [
            ['id'=>'writerecord','value'=>"opslaan", "onclick"=>"buttonclicked='store'"],
            ['id'=>'cancel','value'=>"annuleren","status"=>"formnovalidate","onclick"=>"buttonclicked='cancel'"]
        ];
        $html .= $forms->DisplayButtons();
		return($html);
    }
}
?>