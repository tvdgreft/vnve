<?php
/**
 * Make backup van alle tabellen van de boekhouding die geopend is.
 */
namespace VNVE;

class Backup
{
    public $backupdir = PRANA_PLUGINPATH . "/backup/";      #map voor backup bestanden
    public $header = "<h1>backup/restore van vnve plugin bestanden</h1>";
    protected $prefix;

    /**
     * Wordt gestart vanuit menu of een knop in het formulier
     */
    public function Start($args)
    {
        $html = "";
        $this->prefix = isset($args['prefix']) ? $args['prefix'].'_': "";		#prefix given for databasetable
        if(isset($_POST['backup']))    # backup maken
        {
            $html .= $this->Backup();
        }
        if(isset($_POST['delete']))    # backup verwijderen
        {
            $html .= $this->DeleteBackup();
        }
        if(isset($_POST['restore']))    # backup restoren
        {
            $html .= $this->RestoreBackup();
        }
        $html .= $this->DisplayBackupFiles();
        return($html);
    }
    /**
     * Toon formulier voor het maken van een backup en het terugzetten van een backup
     */
    public function Backup()
    {
        $html = '';
        $html .= $this->header;
        $dbtables = new Dbtables;
        $dbio = new DBIO();
        $prefix=date('YmdHis') . '_';
        foreach ($dbtables->tables() as $table)   #welke tabellen zijn gedefinieerd?
        {
            $name = $this->prefix . $table['name'];
            $html .= sprintf("tabel %s<br>",$name);
            $content = $dbio->ReadAssocRecords($name);
            $backupfile = $this->backupdir . $prefix.$name;
            if( file_put_contents($backupfile, json_encode($content)) == FALSE)
            {
                $error = 'Kan backupfile niet aanmaken';
                $html .= '<div class="isa_error">' . $error . '</div>';
            }
        }
        return($html);
    }
    /**
     * Display the backups which are made
     * in a table and put buttons to remove or restore the tables
     */
    public function DisplayBackupFiles()
    {
        $html = '';
        $html .= $this->header;
        if (is_dir($this->backupdir))
        {
            $files = glob($this->backupdir . "/*");
            $nfiles = count($files);
            if($nfiles <= 0)
            {
                $error = 'Er zijn geen backups opgeslagen';
                $html .= '<div class="isa_error">' . $error . '</div>';
            }
            else
            {
                foreach($files as $file)
                {
                    if(is_file($file))
                    {
                        $filename = basename($file);
                        #$html .= $filename . '<br>';
                        $parts=preg_split("/_/",$filename,-1);
                        $dates[] = $parts[0];
                    }
                }
                $dates=array_unique($dates);
                $html .= '<table class="compacttable">';
                #
                # laat alle backups zien in een tabel
                # plaats buttons voor verwijderen of restoren
                #
                foreach($dates as $date)
                {
                    $html .= '<tr class="compacttr">';
                    $html .= '<td class="compacttd">' .$date . '</td>';
                    $message=sprintf('backup %s definitief wijderen , zeker weten?',$date);
                    $html .= '<td class="compacttd"><button type="submit" name="delete" onclick="return confirm(\'' . $message. '\');" value="' . $date . '">verwijderen</button></td>';
                    $message=sprintf('backup %s terugzetten , zeker weten?',$date);
                    $html .= '<td class="compacttd"><button type="submit" name="restore" onclick="return confirm(\'' . $message. '\');" value="' . $date . '">restore</button></td>';
                    $html .= '</tr>';
                }
                $html .= '</table>';
            }
            $html .= '<br><br>';
            $form = new forms;
            $form->buttons = [
                ['id'=>'backup','value'=>'backupmaken'],
                ['id'=>'cancel','value'=>'annuleren','status'=>'formnovalidate','onclick'=>'buttonclicked="cancel"']
            ];
            $html .= $form->DisplayButtons();
    
        }
        else
        {
            $error = 'backup map bestaat niet';
            $html .= '<div class="isa_error">' . $error . '</div>';
        }
        return($html);
    }
     /**
     * DeleteBackup
     * Verwijder alle tabellen van een backup
     */
    public function DeleteBackup()
    {
        $html = '';
        $date = $_POST['delete'];
        $html .= sprintf("<p>de backup van %s wordt verwijderd<br>",$date);
        foreach(glob($this->backupdir . "/" . $date . "*") as $file)
        {
            if(unlink($file))
            {
                $basename = basename($file);
                $html .= sprintf("%s is verwijderd<br>",$basename);
            }
        }
        $html .= '</p>';
        return($html);
    }
     /**
     * terugzetten van backupbestanden.
     */
    public function RestoreBackup()
    {
        $html = '';
        $date = $_POST['restore'];
        $html .= sprintf("<p>de backup van %s wordt teruggezet<br>",$date);
        foreach(glob($this->backupdir . "/" . $date . "*") as $file)
        {
            $html .= $this->RestoreFile($file);
        }
        $html .= '</p>';
        return($html);
    }
    /**
     * Huidige tabel vervangen door inhoud backup bestand
     */
    public function RestoreFile($file)
    {
        $dbio = new Dbio();
        $dbtables = new Dbtables;
        $html = '';
        $basename = basename($file);
        $parts=preg_split("/_/",$basename,-1);
        $tablefile = $parts[2];
        foreach ($dbtables->tables() as $table)   #welke tabellen zijn gedefinieerd?
        {
            if($tablefile != 'donateurs') { continue; }
            if($table['name'] === $tablefile)
            {
                $encodedrows = file_get_contents($file);
                $rows = json_decode($encodedrows);
                $dbio->RestoreTable(array("table"=>$table['name'],"columns"=>$table['columns'],"rows"=>$rows));
                $html .= sprintf("%s is teruggeplaatst<br>",$basename);
            }
        }
        return($html);
    }
}