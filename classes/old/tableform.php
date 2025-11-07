<?php
namespace VNVE;
class tableform
{
    #########################################################################################################
	# tableform
	#########################################################################################################
    #Variables to be set:
	
	public $class;		#the class which extends tableform
	public $single;		#single name of record
	public $plural;		#plural name of record
	public $table;		#tablename
	public $logtable;	#table for logging
	public $primarykey;		#field with primary key of table
	public $columns = array();	#array of columnnames to be displayed e.g.  [["id","nr","int"],["name","naam","string"]]
	public $joincolumns = array(); #is een kolom met als resultaat van een join met de lopende tabel en een andere tabel
						# kop van kolom,veld in lopende tabel, te joinen tabel, "veld in te joinen tabel, veld te tonen uit andere tabel
						# bv ["gebouw","house","appartments","huisnummer","building"]
	public $searchcolumns;	#columns to be searched by a given searchkey (public search)
	public $searchword = TRUE; #search on words in stead of part of a string
	public $searchfilter;	#filter used withi searchtable (public search)
	public $onsearch;			#last given searchkey (public seaech)
	public $allcolumns;	#all columns of the table
	public $aligns;		#aligns of the columns (left,right or center)
	public $rows_per_page;	#maximum number of rows per page
	public $num_rows;	#array of number of rows per page
	public $rows;		#current number of rows
	public $backgroundcolor;	#backgroundcolor of table and forms
	public $filtercolor;	#backgroundcolor of filterbox in table
	public $permissions;	#permissions for maintaining table cr,md,dl,vw,cp(kopie maken)dm(demo records laden)
	public $onpage = 1;	#pagenummer will be changed bij POST value during 
	public $nextpage;	#clicked on nextpage in previuos run
	public $previouspage;	#clicked in previouspage in previuos run
	public $pages;			#number of pages
	public $onsort = "id";		#column to be sorted
	public $sortorder = "DESC";	#order of sorting (ASC or DESC)
	public $sortclicked = False;	#sort is clicked
	public $filters;	#user defined filters
	public $filtercolumns;	#Columns to be filtered  e.g. array("soort"=>"soortlabel","type"=>"typelabel")
	public $buttonclass =  "prana-button col"; #default button class
	
	#
	# MaintainTable
	# This function reads the records from the database eventually filtered by the filteroptions.
	# The records are sorted if the headername of the column has been clicked.
	# This function displays a filterbox to make it possible to filter on the fields given in the filterarguments (filtercolumns)
	# The columns, defined in the columns argument (columns)
	# The records are printed in pages. Number of records per page is given as argument (rows_per_page)
	# At the button a button is displayed for creating a new record en a button to export the records to csv file
	#
	# POST values which are made foor forther actions on the list:
	# nextpage : currentpage
	# previouspage : correntpage
	# onsort : current sort column
	# onpage : current page
	 #
    # start er restart tableform
    #
    public function MaintainTable()
    {   
        $html = '';
		#echo "<br>voor<br>";
		#print_r($_POST);
		$this->GetPosts();
		#echo "<br>na<br>";
        if(isset($_POST['createrecord'])) 
        { 
            $html .= $this->CreateRecord(); # function in tableform.php
            return($html);
        }
        if(isset($_POST['modifyrecord'])) 
        { 
            $html .= $this->ModifyRecord(); # function in tableform.php
            return($html);
        }
        if(isset($_POST['copyrecord'])) 
        { 
            $html .= $this->CopyRecord(); # function in tableform.php
            return($html);
        }
        #
        # write record to database
        #
        if(isset($_POST['writerecord'])) 
        { 
            $html .= $this->WriteRecord();
            $html .= $this->DisplayTable();
            return($html);
        }
		#
        # write record to database and ask for next record to create
        #
        if(isset($_POST['writerecordandnext'])) 
        { 
            $html .= $this->WriteRecord();
			$html .= $this->CreateRecord(); # function in tableform.php
            return($html);
        }
        if(isset($_POST['deleterecord'])) 
        { 
            $html .= $this->DeleteRecord();
            $html .= $this->DisplayTable();
            return($html);
        }
		if(isset($_POST['logrecord'])) 
        { 
            $html .= $this->LogRecord();
            $html .= $this->DisplayTable();
            return($html);
        }
		/**
		 *  initial load ask for file
		 */
        if(isset($_POST['initialload']))
        {
            $html = $this->InitialLoad();
			return($html);
        }
		/**
		 *  write records to database and display records
		 */
		if(isset($_POST['writeinitialload']))
        {
            $this->WriteInitialLoad();
			$html .= $this->DisplayTable();
			return($html);
        }
		#
		# export table, start form for exporting the table
		#
        if(isset($_POST['export']))
        {
            $html = $this->ExportRecords();
			return($html);
        }
		/**
		 * restore the filters
		 */
        $filters = array();
        if(isset($_POST['filter']))
        {
            foreach ($this->filtercolumns as $c => $label)
            {
                if(isset($_POST[$c])) { $filters[$c] = $_POST[$c]; }
                $this->filters = (object)$filters;
            }
			#echo "<br>new filters<br>";
			#print_r($this->filters);
        }
		$html .= $this->DisplayTable();
        
        $html .='<input id="' . $this->class . '" name="' . $this->class . '" type="hidden" />';
		return($html);
	}
	public function DisplayTable()
	{
		$self = new self();
		$dbio = new dbio();		#class for database I/O
		$form = new forms();	#class for formfields
		$html = '';
		if(isset($this->filtercolumns) && count($this->filtercolumns))
		{
			$html .= '<div class="row">';				# set search box at the right
			$html .= '<div class="col-md-6">';			# left part of window 
			$html .= '<p onclick="ToggleFilters(\'filterbox\')"><a class="prana-button">Toon zoekscherm</a></p>';
			$html .= '</div>';
			if($this->num_rows)
			{
				$options = array_combine($this->num_rows,$this->num_rows); #convert to assoc array
				$options += array("alle" => "*");
				$html .= $form->Dropdown(array("label"=>"regels per pagina", "id"=>"rows_per_page", "value"=>$this->rows_per_page, "collabel"=>"col-md-2", "colinput"=>"col-md-1" ,"options"=>$options, "width"=>"50px", "group"=>TRUE , "submit" => TRUE));
			}
			$html .= '<div id="filterbox" style="display:none" class="col-md-6 prana-box">'; #from the middle starts the filterbox
			$html .= '<h3>ZOEKEN</h3>';
			
			# print filterform
			#
			if(isset($this->filtercolumns))
			{
				foreach ($this->filtercolumns as $c => $label)
				{
					$value="";
					#
					# has filters a content?
					#
					if(isset($this->filters->$c))
					{
						$value=$this->filters->$c;
					}
					#echo "<br>before filter" . $value;
					$form->formdefaults['required']=FALSE;
					$form->formdefaults['collabel']="col-md-5";
					$form->formdefaults['colinput']="col-md-7";
				
					$html .= $form->Text(array("label"=>$label, "id"=>$c, "value"=>$value, "popover"=>"info zoeken"));
				}
			}
			$html .= '<button class="prana-btnsmall" id="filter" name="filter">zoeken</button>';
			$html .= '</div>';	# end of filter box
			$html .= '</div>';	# end of row.
		}
		$html .= '<br><br>';
		/**
		 * Display the records
		 */
		$html .= $this->DisplayRecords();
		$html .= $this->PageButtons();
		$html .= '<br>';
		/**
		 * The footer of the display . create button, exportbutton, initialload button
		 */
		$html .= '<div style="float:left" class="row">';
		/**
		 * Upload records
		 */
		if(in_array("dm", $this->permissions)) #initial load button if there are no records
		{
			$html .= sprintf('<button class="%s" name="initialload">upload</button>',$this->buttonclass);
			$html .= '&nbsp;';
		}
		#
		# create new record
		#
		if( in_array("cr", $this->permissions))
		{
			$html .= sprintf('<button class="%s" name="createrecord">nieuw record</button>',$this->buttonclass);
			$html .= '&nbsp;';
		}
		if( in_array("xp", $this->permissions))
		{
			$html .= sprintf('<button class="%s" name="export">exporteren</button>',$this->buttonclass);
			$html .= '&nbsp;';
		}
		#$html .= $this->ExportRecords();		# export records in csv file
		$html .= '</div>';
		$html .= '<br><br><br>';
		#
		# set post values
		#
		$this->rows_per_page=0;
		$html .= $this->SetPosts();
		return($html);
	}
	/**
	 * Search the table by public
	 * just one search key for multiple rows
	 * entering a number should be 
	 * select *from yourTableName
	* where
	* yourColumnName regexp '(^|[[:space:]])yourWord([[:space:]]|$)';
	 */
	public function SearchTable()
    {   
        $html = '';
		$dbio = new DBIO();
		/**
		 * search butten clicked
		 */
		if(isset($_POST['search'])) 
		{
			$this->onsearch = $_POST['search'];
		}
		/**
		 * download file clicked
		 */
		if(isset($_POST['download'])) 
        { 
            $html .= $this->DownloadDocument($_POST['download']);
        }
		/**
		 * download braille
		 */
		if(isset($_POST['downloadbraille'])) 
        { 
            $html .= $this->DownloadBraille($_POST['downloadbraille']);
			return($html);		# don't show the table
        }
		if(isset($_POST['viewer'])) 
        { 
            $html .= $this->DocumentViewer($_POST['viewer']);
        }
		$html .= isset($this->params["intro"]) ? $this->params["intro"] : '';
		$html .= $this->DisplayPublic();
        $html .='<input id="' . $this->class . '" name="' . $this->class . '" type="hidden" />';
		return($html);
	}
	/**
	 * Display the search form used by public (frontend)
	 */
	public function DisplayPublic()
	{
		$self = new self();
		$dbio = new dbio();		#class for database I/O
		$form = new forms();	#class for formfields
		$this->GetPosts();
		$html = '';
		/**
		 * display search field
		 */
		$html .= '<div class="row">';
		$html .= $form->Text(array("submit" => TRUE,"label"=>"zoeken op", "id"=>"search", "value"=>$this->onsearch, "autofocus"=>TRUE , "group"=>TRUE,  "collabel"=>"col-md-2", "colinput"=>"col-md-3" ,"width"=>"300px", "required"=>FALSE));
		/**
		 * search button
		 */
		$html .= sprintf('<button class="prana-btnsmall col" name="newsearch">zoeken</button>');
		/**
		 * dropdown box for number of rows per page
		 */
		if($this->num_rows)
		{
			$options = array_combine($this->num_rows,$this->num_rows); #convert to assoc array
			$options += array("alle" => "*");
			$html .= $form->Dropdown(array("label"=>"regels per pagina", "id"=>"rows_per_page", "value"=>$this->rows_per_page,"collabel"=>"col-md-2","colinput"=>"col-md-1","options"=>$options, "width"=>"50px", "group"=>TRUE,"submit" => TRUE));
		}
		$html .= '</div>';
		/** 
		 * display records
		 */
		$html .= $this->DisplayRecords();
		$html .= $this->PageButtons();
		
		#
		# set post values
		#
		$this->rows_per_page=0;
		$html .= $this->SetPosts();
		return($html);
	}
	/**
	 * search on a certain column and display records with content 
	 */
	public function DisplayContent() : string
	{
		$html = '';
		/**
		 * search butten clicked
		 */
		if(isset($_POST['search'])) 
		{
			$this->onsearch = $_POST['search'];
		}
		/**
		 * download file clicked
		 */
		if(isset($_POST['download'])) 
        { 
            $html .= $this->DownloadDocument($_POST['download']);
        }
		/**
		 * downloadbraille clicked
		 */
		if(isset($_POST['downloadbraille'])) 
        { 
            $html .= $this->DownloadBraille($_POST['downloadbraille']);
        }
		if(isset($_POST['viewer'])) 
        { 
            $html .= $this->DocumentViewer($_POST['viewer']);
        }
		$html .= $this->DisplayRecords();
		return($html);
	}
	/**
	 * Display the rows of a databasetable in a table
	 */
	public function DisplayRecords() : string
	{
		$dbio = new dbio();		#class for database I/O
		$html = '';
		#
		# new search
		# set pagenumber on 1
		#
		if(isset($_POST['search'])) 
		{
			$this->onpage=1;
		}
		#
		# sort button clicked.
		# get sort column and switch the sortorder
		#
		if(isset($_POST['onsort']))
		{ 	$this->onsort = $_POST['onsort']; 		#column to be sorted
			$this->sortorder = $_POST['sortorder'];	#sortdirection
		}
		echo "<br>onsort=".$this->onsort."<br>sortorder=".$this->sortorder;
		if(isset($_POST['filter']) || isset($_POST['newsearch'])) # Er is een nieuwe zoek opdracht gegeven
		{
			$this->onpage = 1;
		}
		$this->sortclicked = FALSE;
        if(isset($_POST['sort'])) 
		{
			$this->onpage = 1;
			$this->onsort = $_POST['sort'];
			if(isset($_POST['sortorder']))
			{
				if($_POST['sortorder'] == "ASC") {$this->sortorder = "DESC";}
				if($_POST['sortorder'] == "DESC") {$this->sortorder = "ASC";}
			}
		}
		if($this->nextpage) { $this->onpage += 1; } # next page given so go the next page
		if($this->previouspage) { $this->onpage -= 1; } # next page given so go the next page
		#
		# count number of records andcalculate number of pages
		#
		#echo "<br>display records";
		$pb = $dbio->ReadRecords(array("table"=>$this->table,"columns"=>$this->columns,"filters"=>$this->filters,
										"search"=>array($this->searchcolumns,$this->onsearch,$this->searchword),"searchfilter"=>$this->searchfilter));
		$this->rows=count($pb);
		/**
		 * Set the number of rows per page
		 * If number of page not defines: get the first number in array of numbers per page else show all rows
		 */
		if(!$this->rows_per_page) { $this->rows_per_page = isset($this->num_rows[0]) ? $this->num_rows[0] : $this->rows; }
		if($this->rows_per_page == '*') { $this->rows_per_page = $this->rows; }   #display all records
		#echo "rpp=".$this->rows_per_page."rows=".$rows.'<br>';
		$sort = $this->onsort . ' ' . $this->sortorder;
		#echo "sort=" . $sort . "onpage=" . $this->onpage . "onsearch=" . $this->onsearch;
		#return($html);
		$pb = $dbio->ReadRecords(array("table"=>$this->table,"columns"=>$this->columns,"page"=>$this->onpage,"maxlines"=>$this->rows_per_page,
									"sort"=>$sort,"filters"=>$this->filters,
									"search"=>array($this->searchcolumns,$this->onsearch,$this->searchword),"searchfilter"=>$this->searchfilter,"execute"=>TRUE));
		$this->pages=ceil($this->rows/$this->rows_per_page);
		#echo sprintf("<br>pages=%d rows=%d ",$this->pages,$this->rows);
		/**
		 * display records
		 */
		$html .= '<br>';
		$html .= '<table class="compacttable">';
		$html .= '<tr class="compacttrh">';
		/**
		 * Set headers of table
		 */
		foreach ($this->columns as $c)
		{
			$thclass = "compactth";
			$type = $c[2] ? $c[2] : "string";	// default type is string
			if($type == "int" || $type == "euro" || $type=="stringright") {$thclass = "compactthright"; }	// getallen rechts aansluiten
			$sortfield='<button class="pbtn-header" name="sort" value="' . $c[0] . '">' . $c[1]  . '</button>';
			$html .= '<th class="' . $thclass .'">' . $sortfield . '</th>';
		}
		/**
		 * Zijn er kolommen die via een join opgezocht moeten worden?
		 * Op deze kolommen kan niet worden gesorteerd.
		 * header,kolom in jointable,type,kolom in current table,jointable
		 */
		foreach ($this->joincolumns as $c)
		{
			$type = $c[2] ? $c[2] : "string";	// default type is string
			if($type == "int" || $type == "euro" || $type=="stringright") {$thclass = "compactthright"; }	// getallen rechts aansluiten
			$html .= '<th class="' . $thclass .'">' . $c[0] . '</th>';
		}

		if (in_array("vw", $this->permissions)) {$html .= '<th class="compactth"></th>';}	#Empty header for view button
		if (in_array("dl", $this->permissions)) {$html .= '<th class="compactth"></th>';}	#Empty header for delete button
		if (in_array("md", $this->permissions)) {$html .= '<th class="compactth"></th>';}	#Empty header for modigy button
		if (in_array("cp", $this->permissions)) {$html .= '<th class="compactth"></th>';}	#Empty header for copy button
		if (in_array("lg", $this->permissions)) {$html .= '<th class="compactth"></th>';}	#Empty header for log button
		$html .= '</tr>';
		#
		# print rows
		#
		$primarykey = $this->primarykey;
		foreach ( $pb as $p )
		{
			$html .= '<tr class="compacttr">';
			/**
			 * display the columns which are defined
			 */
			foreach($this->columns as $c)
			{
				
				$type = $c[2] ? $c[2] : "string";	// default type is string
				$tdclass = "compacttd";
				if($type == "int" || $type == "euro") {$tdclass = "compacttdright"; }	// getallen rechts aansluiten
				/**
				 * If field is a downlod, then show downloadbutton
				 */
				$name = $c[0];
				if(($type == "pdf" || $type == "txt") && $p->$name)		#download a pdf file
				{
					/**
					 * Downloadlink only if file exists
					 */
					$path_parts = pathinfo($p->$name);
					$file = $path_parts['filename'] . '.' . $type;
					
					#$ext = substr(strrchr($p->$name, '.'), 1);	#the extension of the file

					if($this->DocumentExist($file))
					{
						$btn = '<i class="fa fa-download" style="font-size:24px;color:blue;"></i>-'.$type;
						$html .= '<td class="compacttd"><button type="submit" id="download" name="download" class="btn btn-link btn-xs" value="' . $file . '">'.$btn.'</button></td>';
					}
					else
					{
						$btn = '<i class="fa fa-download" style="font-size:24px;"></i>-'.$type;
						$html .= '<td class="compacttd">' . $btn . '</td>';
					}
				}
				/*
				elseif($type == "viewer" && $p->$name)
				{
					$ext = substr(strrchr($p->$name, '.'), 1);	#get the extension of the file
					#$btn = '<i class="fa fa-file-pdf-o" style="font-size:24px;color:red;"></i>'.$ext;
					if($this->DocumentExist($p->$name))
					{
						$btn = '<i class="fa fa-eye" style="font-size:24px;color:blue;"></i>-'.$ext;
						$html .= '<td class="compacttd"><button type="submit" id="viewer" name="viewer" class="btn btn-link btn-xs" value="' . $p->$name . '">' . $btn . '</button></td>';
				
					}
					else
					{
						$btn = '<i class="fa fa-eye" style="font-size:24px;"></i>-'.$ext;
						$html .= '<td class="compacttd">' . $btn . '</td>';
					}	
				}
				*/
				elseif($type == "download" && $p->$name)
				{
					/**
					 * Downloadlink only if file exists
					 */
					$ext = substr(strrchr($p->$name, '.'), 1);	#the extension of the file
					if($url = $this->DocumentExist($p->$name))
					{
						$btn = '<i class="fa fa-download" style="font-size:24px;color:blue;"></i>-'.$ext;
						/* door op de volgende link te klikken komn we in $this->DownloadDocument.
						Die gaat naar forms->downloadfile en dat werkt niet goed onder wordpress */
						#$html .= '<td class="compacttd"><button type="submit" id="download" name="download" class="btn btn-link btn-xs" value="' . $p->$name . '">'.$btn.'</button></td>';
						$html .= '<td class="compacttd"><a href="'.$url.'" download>' . $btn . '</a></td>'; #download direct
					}
					else
					{
						$btn = '<i class="fa fa-download" style="font-size:24px;"></i>-'.$ext;
						$html .= '<td class="compacttd">' . $btn . '</td>';
					}
				}
				elseif($type == "showbraille" && $p->$name)    #show braille on screen
				{
					$path_parts = pathinfo($p->$name);
					$file = $path_parts['filename'] . '.' . "txt";
					if($this->DocumentExist($file))
					{
						$btn = '<i class="fa fa-eye" style="font-size:24px;color:blue;"></i>-'."brl";
						$html .= '<td class="compacttd"><button type="submit" id="showbraille" name="showbraille" class="btn btn-link btn-xs" value="' . $p->id . '">' . $btn . '</button></td>';
				
					}
					else
					{
						$btn = '<i class="fa fa-eye" style="font-size:24px;"></i>-'."brl";
						$html .= '<td class="compacttd">' . $btn . '</td>';
					}	
				}
				elseif($type == "printbraille" && $p->$name)    #print braille at CBB
				{
					$ext = substr(strrchr($p->$name, '.'), 1);	#get the extension of the file
					#$btn = '<i class="fa fa-file-pdf-o" style="font-size:24px;color:red;"></i>'.$ext;
					if($file = $this->BrailleDocument($p->$name))
					{
						$btn = '<i class="fa fa-print" style="font-size:24px;color:blue;"></i>-'."brl";
						$html .= '<td class="compacttd"><button type="submit" id="printbraille" name="printbraille" class="btn btn-link btn-xs" value="' . $p->id . '">' . $btn . '</button></td>';
				
					}
					else
					{
						$btn = '<i class="fa fa-print" style="font-size:24px;"></i>-'."brl";
						$html .= '<td class="compacttd">' . $btn . '</td>';
					}	
				}
				elseif($type == "downloadbraille" && $p->$name)    #download the unicode braille
				{
					$ext = substr(strrchr($p->$name, '.'), 1);	#get the extension of the file
					$path_parts = pathinfo($p->$name);
					$file = $path_parts['filename'] . '.' . "txt";
					#$btn = '<i class="fa fa-file-pdf-o" style="font-size:24px;color:red;"></i>'.$ext;
					if($this->DocumentExist($file))
					{
						$btn = '<i class="fa fa-download" style="font-size:24px;color:blue;"></i>-'."utxt";
						$html .= '<td class="compacttd"><button type="submit" id="downloadbraille" name="downloadbraille" class="btn btn-link btn-xs" value="' . $p->id . '">' . $btn . '</button></td>';
				
					}
					else
					{
						$btn = '<i class="fa fa-download" style="font-size:24px;"></i>-'."brl";
						$html .= '<td class="compacttd">' . $btn . '</td>';
					}	
				}
				elseif($type == "string" || $type == "int" || $type == "euro")	#display value
				{
					$html .= '<td class="' . $tdclass .'">' . $p->$name . '</td>';
				}
				else		#default function should be defined in calling class
				{
					$btn = '<i class="fa fa-download" style="font-size:24px;color:blue;"></i>-';
					$btn = $type;
					$html .= '<td class="compacttd"><button type="submit" id="' . $type . '" name="' . $type . '" class="btn btn-link btn-xs" value="' . $p->id . '">'.$btn.'</button></td>';
				}

			}
							/**
				 * Zijn er kolommen die via een join opgezocht moeten worden?
				 * Op deze kolommen kan niet worden gesorteerd.
				 * c[0]=kolom in jointable to be displayed
				 * c[1]=header
				 * c[2]=type
				 * c[3]=jointable
				 * c[4]=joinfield current tabel
				 * c[5]=joinfield joined table
				 * e.g. ["gebouw","gebouw","string",$appartments,"house","huisnummer"]
				 */
				foreach ($this->joincolumns as $c)
				{
					$type = $c[2] ? $c[2] : "string";	// default type is string
					if($type == "int" || $type == "euro" || $type=="stringright") {$thclass = "compacttdright"; }	// getallen rechts aansluiten
					$name=$c[4];
					#$html .= '<td class="' . $tdclass .'">' . $p->$name . '</td>';
					$result=$dbio->ReadUniqueRecord(array("table"=>$c[3],"key"=>$c[5],"value"=>$p->$name));
					$name=$c[0];
					$value = empty($result) ? "" : $result->$name;;
					$html .= '<td class="' . $tdclass .'">' . $value . '</td>';
				}

			
			/**
			* view / modify / delete / copy buttons
			**/
			if (in_array("vw", $this->permissions)) 
			{
				#$html .= '<td><button type="submit" class="btn btn-link btn-xs showrecord" name="showrecord" value="' . $p->id . '"><i class="fa fa-eye"></i></button>';
				#$html .= '<td class="compacttd showrecord"><a class="btn btn-link btn-xs"><i class="fa fa-eye"></a></td>';
				$toggleid="toggle".$p->id;
				$html .= '<td class="compacttd " onclick="ToggleRow(\''.$toggleid.'\')"><i class="fa fa-eye" style="font-size:20px;color:blue;"></td>';
			}
			
			if (in_array("dl", $this->permissions)) 
			{ 
				$message=sprintf('%s %d verwijderen , zeker weten?',$this->single,$p->$primarykey);
				$html .= '<td class="compacttd"><button type="submit" name="deleterecord" class="btn btn-link btn-xs" onclick="return confirm(\'' . $message. '\');" value="' . $p->$primarykey . '"><i class="fa fa-trash"></i></button></td>';
			}
			if (in_array("md", $this->permissions)) 
			{ 
				$html .= '<td class="compacttd"><button type="submit" name="modifyrecord" class="btn btn-link btn-xs" value="' . $p->id . '"><i class="fa fa-edit"></i></button></td>';
			}
			if (in_array("cp", $this->permissions)) 
			{ 
				$html .= '<td class="compacttd"><button type="submit" name="copyrecord" class="btn btn-link btn-xs" value="' . $p->id . '"><i class="fa fa-copy"></i></button></td>'; 
			}
			if (in_array("lg", $this->permissions)) 
			{ 
				$html .= '<td class="compacttd"><button type="submit" name="logrecord" class="btn btn-link btn-xs" value="' . $p->id . '"><i class="fa fa-binoculars"></i></button></td>'; 
			}

			$html .= '</tr>';
			/**
			 * display revordinformation
			 */
			if (in_array("vw", $this->permissions)) 
			{
				$primarykey = $this->primarykey;
				$detail = $dbio->DisplayAllFields(array("table"=>$this->table,"key"=>$this->primarykey,"value"=>$p->$primarykey));

				/**
				* Row with record details will be displayed when clicked on view icon
				*/
				$cols = count($this->columns);	#number of columns
				$html .= '<tr id='.$toggleid.' style="display:none" >';
				$html .= '<td colspan="'.$cols.'">'.$detail.'</td>'; #span over all columns and show when onclick
				$html .= '</tr>';
			}
		}
		$html .= '</table>';
		$html .= '<br>';

		return($html);
	}
	/**
	 * buttons for next and previous page
	 */
	public function PageButtons()
	{
		$html = '';
		$html .= sprintf("aantal records: %d pagina %d van %d",$this->rows,$this->onpage,$this->pages);
		if($this->pages > 1) 
		{ 
			$html .= sprintf(' bladeren: ');
			if($this->onpage > 1) { $html .= '<button type="submit" class="btn btn-link btn-sx" name="previouspage" value="' . $this->onpage . '"><i class="fa fa-caret-square-o-left" style="font-size:24px"></i></button>'; }
			if($this->onpage < $this->pages) { $html .= '<button type="submit" class="btn btn-link btn-sx" name="nextpage" value="' . $this->onpage . '"><i class="fa fa-caret-square-o-right" style="font-size:24px"></i></button>'; }
		}
		return($html);
	}
	public function CreateRecord()
    {
        $html = '';
        $dbio = new DBIO();
        $columns = $dbio->columns($this->table);
        foreach ($columns as $c)
        {
            if($c != $this->primarykey)
            {
                $this->fields[$c]='';
            }
        }
        $html = '';
		$html .=  sprintf('<h2>Nieuwe %s aanmaken</h2>',$this->single);
        $html .= $this->FormTable("create");
		$html .= $this->SetPosts();
        return($html);
    }
	public function ModifyRecord()
    {
        $html = '';
        $dbio = new DBIO();
        $columns = $dbio->columns($this->table);
		$p = $dbio->ReadUniqueRecord(array("table"=>$this->table,"key"=>$this->primarykey,"value"=>$_POST['modifyrecord']));
        foreach ($columns as $c)
        {
            $this->fields[$c]=$p->$c;
        }
		$html .=  sprintf('<h2> %s wijzigen</h2>',$_POST['modifyrecord']);
		$html .= $this->FormTable("modify");
		$html .= $this->SetPosts();
        return($html);
    }
	public function CopyRecord()
    {
        $html = '';
        $dbio = new DBIO();
        $columns = $dbio->columns($this->table);
        $p = $dbio->ReadUniqueRecord(array("table"=>$this->table,"key"=>$this->primarykey,"value"=>$_POST['copyrecord']));
        foreach ($columns as $c)
        {
			if($c != $this->primarykey)
            {
				$this->fields[$c]=$p->$c;
            }
        }
        $html .=  sprintf('<h2>%s kopieren</h2>',$this->single);
        $html .= $this->FormTable("create");
		$html .= $this->SetPosts();
        return($html);
    }
	/*
		write a record to the database
		fields should be in POST parameters
		$_POST['crmod'] = 'create' or 'modify'
	*/
	public function WriteRecord()
	{
        $html = '';
        $dbio = new DBIO();
		if(!$this->CheckModify()) 
		{	
			$error = "Error: Bad input:- record not written";
			$html .= '<div class="isa_error" >' . $error . '</div>'; 
			return($html);	#check if input is valid
		}
        $fields = array();
        $columns = $dbio->columns($this->table);
        foreach ($columns as $c)
        {
            if(isset($_POST[$c]))
            {
                $fields += [$c=>$this->GetPostField($c)];
            }
        }
        if($_POST['crmod'] == "create")
        {
            $id=$dbio->CreateRecord(array("table"=>$this->table,"fields"=>$fields));
            $html .= sprintf('%s %d is aangemaakt', $this->single, $id);
           
        }
        if($_POST['crmod'] == "modify")
        {
            $dbio->ModifyRecord(array("table"=>$this->table,"fields"=>$fields,"key"=>$this->primarykey,"value"=>$_POST["primarykey"]));
            $html .= sprintf('record %d is gewijzigd', $_POST['primarykey']);
        }
        return($html);
    }
	/**
	 * Delete a record from the table
	 */
	public function DeleteRecord()
	{
		$html = '';
		$dbio = new dbio();
		$html .= sprintf('%s %d is verwijderd', $this->single, $_POST['deleterecord']);
		$html = $this->AfterDelete($_POST['deleterecord']);   #nog iets anders te doen als record wordt verwijderd?
		$dbio->DeleteRecord(array("table"=>$this->table,"key"=>$this->primarykey,"value"=>$_POST['deleterecord']));
		return($html);
	}
	/**
	 * log record
	 * display background infomation for webmanager
	 * e.g the logging of an action on the record
	 */
	public function LogRecord()
    {
        $html = '';
        $dbio = new DBIO();
        $columns = $dbio->columns($this->table);
        $p = $dbio->ReadUniqueRecord(array("table"=>$this->table,"key"=>$this->primarykey,"value"=>$_POST['logrecord']));
        foreach ($columns as $c)
        {
            $this->fields[$c]=$p->$c;
        }
		$html .=  sprintf('<h2> %s logging</h2>',$_POST['logrecord']);
		$html .= $this->ShowLog();
		$html .= $this->SetPosts();
        return($html);
    }
	/**
	 * Download a document
	 */
	/**
	 * Initial load of the database table
	 * Ask for a csv file with all columns of the table
	 */
	public function InitialLoad()
    {
		$html = '';
        $form = new Forms();
		$html .= "<h2>Upload records</h2>";
		$html .= "Upload records vanuit een csv-betsand<br>";
		$html .= "kolommen moeten zijn geschieden door ;<br>";
        $html .= $form->File(array("label"=>"Bestand kiezen", "id"=>"bestand", "value"=>"iniload","accept"=>".csv"));
        $form->buttons = [
            ['id'=>'writeinitialload','value'=>"Inital load inlezen"],
            ['id'=>'cancel','value'=>"annuleren","status"=>"formnovalidate","onclick"=>"buttonclicked='cancel'"]
        ];
        $html .= $form->DisplayButtons();
        $html .='<input id="writeinitialload" name="writeinitialload" type="hidden" />';
        $html .= $this->SetPosts();	// safe post values created bij tableform
        return($html);
    }
	public function WriteInitialLoad()
    {
		$dbio = new dbio();
		$html = '';
		$html .= sprintf("loadiniload","initial load wordt geladen");
		$fp = fopen($_FILES['bestand']["tmp_name"],"rb");
		$initialload = array();
        if(($header = fgetcsv($fp, 0, ";")) !== FALSE)
        {
            //Loop through the CSV rows.
            while (($row = fgetcsv($fp, 0, ";")) !== FALSE) 
            {
                $rows[] = array_combine($header, $row);
            }
        }
        foreach ($rows as $row)
        {
            $dbio->CreateRecord(array("table"=>$this->table,"fields"=>$row));
		}
		$html .= $this->SetPosts();	// safe poast values created bij tableform
        return($html);
	}
	#
	# Export records to be used in Excell using
	# A button is placed and a javascript exports the cvs file
	#
	public function ExportRecords()
	{
		global $wpdb;
		$dbio = new dbio();
		$this->allcolumns = $dbio->DescribeColumns(array("table"=>$this->table));	#get information about all columns
		$export = '';
		$export .= sprintf('<h2>exporteer tabel naar csv bestand</h2>');
		$export .= sprintf('Door op onderstaande knop te drukken wordt de tabel %s naar een csv bestand geexporteerd<br>',$this->plural);
		$export .= sprintf('Veldscheider is ;<br><br>');
		$export .= '<table style="display:none">';
		$export .= '<tr>';
		foreach ($this->allcolumns as $c)
		{
			$export .= '<th>' . $c->Field . '</th>';
		}
		$export .= '</tr>';
		$pb = $dbio->ReadRecords(array("table"=>$this->table,"columns"=>$this->columns,"filters"=>$this->filters));
		foreach ( $pb as $p )
		{
			$export .= '<tr>';
			foreach ($this->allcolumns as $c)
			{
				$name=$c->Field;
				$s=$p->$name ? $p->$name : '';			#empty field to empty string
				$field = str_replace(["\r\n", "\n", "\r"], "", $s); # remove crlf
				$field = str_replace(';',':',$field);				#replace ; by : cause ; is fieldseparator
				$export .= '<td>' . $field . '</td>';
			}
			$export .= '</tr>';
		}
		$export .= '</table>';
		$filename = $this->table . '.csv';	#add csv extension
		$export .= '<span style="display:none">'.$filename.'</span>';
		$export .= '<button class="prana-button col exporttable">export</button>';   #javascript exportcsv.js does the rest
		$export .= '&nbsp;';
		$export .= sprintf('<button class="%s" name="cancel">annuleren</button>',$this->buttonclass);
		return($export);
	}
	/**
     * treat other fields in radio or checkboxes
	 * $args['formfields'] = list of formfields
     * 0: type = fieldtype (text,checkbox,date,radio) or:
	 * 			%hr% = horizontal line
	 * 			%startrow% = start a row of fields
	 * 			%endrow% = end of row
     * 1: id = formfieldid (the same as the table coloum name)
     * 2: label = The label of the formfield
     * 3: required = Field should not be empty (True or False)
	 * 4: pairs = pairs of items in radio
     * 5: options = als type = checkbox or radio:  "rood,wit,blauw"
     * 6: otheroption = add other option , for type=checkbox,radio
     * 7: width = width of formfield
     * 
     */
	public function GetPostField($id)
	{
		if(!isset($this->formfields)) { return($_POST[$id]); }
		foreach($this->formfields as $f)
		{
			if($f[1] == $id && $f[6])	#oher option is defined
			{
				$other = $id . 'other';
				if(isset($_POST[$other]))
				{
					if($f[0] == 'radio')
					{
						return($_POST[$other]);
					}
					if($f[0] == 'checkboxes')	#add other field to checkbox
					{
						$values = array();
						foreach($_POST[$id] as $item)
						{
							if($item != "other")
							{
								array_push($values,$item);
							}
						}
						array_push($values,$_POST[$other]);
						return($values);
					}
				}
			}
		}
		return($_POST[$id]);
	}
	public function SetPosts()
	{
		#
		# set post values
		#
		$html = '';
		$html .='<input id="onpage" name="onpage" type="hidden" value=' . $this->onpage .  ' />';	#current page
		$html .='<input id="onsort" name="onsort" type="hidden" value=' . $this->onsort .  ' />';	#current sort column
		$html .='<input id="sortorder" name="sortorder" type="hidden" value=' . $this->sortorder .  ' />';	#direction of sorting (ASC or DESC)
		if($this->rows_per_page) { $html .='<input id="rows_per_page" name="rows_per_page" type="hidden" value=' . $this->rows_per_page .  ' />'; }
		#
        # geef hprimarykeyige filters door een POST values om ze weer terug te kunnen krijgen bij volgende klik
        #
        if($this->filters) { $html .='<input id="filters" name="filters" type="hidden" value=' . urlencode(json_encode($this->filters)) .  ' />'; }
		return($html);
	}
	public function GetPosts()
	{
		# get the values of the previous run:
		if(isset($_POST['previouspage'])) {$this->previouspage = $_POST['previouspage']; }
		if(isset($_POST['nextpage'])) {$this->nextpage = $_POST['nextpage']; }
		if(isset($_POST['onpage'])) {$this->onpage = $_POST['onpage']; }
		if(isset($_POST['onsort'])) {$this->onsort = $_POST['onsort']; }
		if(isset($_POST['sortorder'])) {$this->sortorder = $_POST['sortorder']; }
		if(isset($_POST['rows_per_page'])) {$this->rows_per_page = $_POST['rows_per_page']; }
		if(isset($_POST['filters'])) { $this->filters=json_decode(urldecode($_POST['filters'])); } #zet filters terug
	}
}

?>