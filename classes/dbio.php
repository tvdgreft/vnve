<?php
/**
 * dbio - database functions
 **/
namespace VNVE;

class dbio
{
	public $dbiodefaults = array(
		'table' => '',
		'sort' => '',
		'filters' => '',
		'where' => '',
		'sql' => '',
		'search' => '',
		'searchfilter' => '',
		'searchonly' => '',
		'searchexcept' => '',
		'page' => '',	#current pagenummer
		'maxlines' => '',	#
		'output' => 'OBJECT',
		'execute' => TRUE
	);
	/**
	* create a new table
	* $table = name of the table (without joomla prefix)
	* $columns = array of colums like: `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	* example:
	* $dbio -> CreateTable($prefix . Dbtables::titels['name'],Dbtables::titels['columns']);
	* const titels = ["name"=>"docman", "columns"=>"
	*	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    *    `crdate` datetime NOT NULL,					#creationdate of record
    *    `nummer` int(5) NOT NULL,		#nummer
    *    `oudnummer` int(5) NOT NULL,				#oud nummer
    *    `seizoen` varchar(255) NOT NULL,
    *    `titel` varchar(512) NOT NULL,
    *    `auteur` varchar(255) NOT NULL,						#auteur
    *    `bladzijden` varchar(255) NOT NULL,			#bladzijden
    *    `artikel` varchar(255),
	*	  PRIMARY KEY (`id`)"]; 
	**/
	public function CreateTable($table,$columns)
	{
		if(PRANA_CMS == "joomla")
		{	
			$db = \JFactory::getDbo();
			if(!$table) { return(FALSE); }
			$query = 'CREATE TABLE IF NOT EXISTS `' . '#__' . $table . '` (' . $columns . ') ENGINE=InnoDB DEFAULT CHARSET=utf8;';
			$db->setQuery($query);
			$db->execute();
		}
		if(PRANA_CMS == "wordpress")
		{
			global $wpdb;
			if(!$table) { return(FALSE); }
			$query = 'CREATE TABLE IF NOT EXISTS `' . $wpdb->prefix . $table . '` (' . $columns . ') ENGINE=InnoDB DEFAULT CHARSET=utf8;';
			$wpdb->query($query);
			if($wpdb->last_error !== '')
			{
				$wpdb->print_error();
				return(FALSE);
			}
		}
		return(TRUE);
	}	
	public function DeleteTable($table)
	{
		if(PRANA_CMS == "joomla")
		{	
			$db = \JFactory::getDbo();
			if(!$table) { return(FALSE); }
			$query = 'DROP TABLE IF EXISTS ' . '#__' . $table;
			$db->setQuery($query);
			$db->execute();
			return(TRUE);
		}
		if(PRANA_CMS == "wordpress")
		{
			global $wpdb;
			if(!$table) { return(FALSE); }
			$query = 'DROP TABLE IF EXISTS ' . $wpdb->prefix . $table;
			$wpdb->query($query);
			if($wpdb->last_error !== '')
			{
				$wpdb->print_error();
				return(FALSE);
			}
			return(TRUE);
		}
	}
	/**
	 * restore a table from a backupfile
	 */
	public function RestoreTable($args)
	{
		if(PRANA_CMS == "joomla")
		{	
			$db = \JFactory::getDbo();
			$table = isset($args["table"]) ? '#__' . $args["table"] : "";
			$this->DeleteTable($args["table"]);
			$this->CreateTable($args["table"],$args["columns"]);
			foreach ($args["rows"] as $row)
			{
				$columns = array();
				$values = array();
				foreach ($row as $f =>$value)
				{
					array_push($columns,$f);
					if(is_array($value)) {$value = json_encode($value);}
					array_push($values,$this->quote($value));
				}
				$query = $db->getQuery(true);
				$query
				->insert($this->quotename($table))
				->columns($this->quotename($columns))
				->values(implode(',', $values));
				$db->setQuery($query);
				$db->execute();
			}
		}
		if(PRANA_CMS == "wordpress")
		{
			global $wpdb;
		}
		return;
	}
	/**
	 * create record
	 * the fields created and modified are set to the current date
	 * $args['table'] - databasetable
	 * $args['fields'] - array of fields $fields=array("field1"=>$value,"field2"=>$value .... )
	 */
	public function CreateRecord($args)
	{
		if($this->IsColumn($args["table"],'crdate')) { $args['fields'] += ["crdate" => date("Y-m-d H:i:s")]; }
		if($this->IsColumn($args["table"],'created')) { $args['fields'] += ["created" => date("Y-m-d H:i:s")]; }
		if(PRANA_CMS == "joomla")
		{	
			$db = \JFactory::getDbo();
			$query = $db->getQuery(true);
			$table = isset($args["table"]) ? '#__' . $args["table"] : "";
			$columns = array();
			$values = array();
			foreach ($args["fields"] as $f =>$value)
			{
				array_push($columns,$f);
				if(is_array($value)) {$value = json_encode($value);}
				array_push($values,$this->quote($value));
			}
			$query
			->insert($this->quotename($table))
			->columns($this->quotename($columns))
			->values(implode(',', $values));
			$db->setQuery($query);
			$db->execute();
			return $db->insertid();
		}	
		if(PRANA_CMS == "wordpress")
		{	
			global $wpdb;
			$wptable = $wpdb->prefix . $args["table"];
			$query = 'INSERT INTO ' . $wptable . '(';
			foreach ($args["fields"] as $f =>$value)
			{
				$query .= $f .',';
			}
			$query = rtrim($query,',');	#remove last komma
			$query .= ')';
			$query .= ' VALUES (';
			foreach ($args["fields"] as $f =>$value)
			{
				if($f == "created") { $value = date("Y-m-d H:i:s"); }
				if($f == "modified") { $value = date("Y-m-d H:i:s"); }
				$query .= '"' . $value . '",';
			}
			$query = rtrim($query,',');	#remove last komma
			$query .= ')';
			$result=$wpdb->query($query);
			return $wpdb->insert_id;
		}
	}
	/**
	* update record
	* $args['table'] - databasetable
	* $args['fields'] - array of fields $fields=array("field1"=>$value,"field2"=>$value .... )
	* $args['key'] - name of unique key
	* $args['value'] - value of unique key
	 */
	public function ModifyRecord($args)
	{
		if($this->IsColumn($args["table"],'modified')) { $args['fields'] += ["modified" => date("Y-m-d H:i:s")]; }
		if($this->IsColumn($args["table"],'mddate')) { $args['fields'] += ["mddate" => date("Y-m-d H:i:s")]; }
		if(PRANA_CMS == "joomla")
		{	
			$db = \JFactory::getDbo();
			$query = $db->getQuery(true);

			$table = isset($args["table"]) ? '#__' . $args["table"] : "";
			$fields = array();
			foreach ($args["fields"] as $f =>$value)
			{
				if(is_array($value)) { $value = json_encode($value);}
				array_push($fields,$this->quotename($f) . '=' . $this->quote($value));
			}
			$conditions = array(
				$this->quotename($args["key"]) . ' = ' . $this->quote($args['value'])
			);
			$query->update($this->quotename($table))->set($fields)->where($conditions);
			$db->setQuery($query);
			$result = $db->execute();
			return($result);
		}
		if(PRANA_CMS == "wordpress")
		{	
			global $wpdb;
			$wptable = $wpdb->prefix . $args["table"];
			$query = 'UPDATE('.$wptable . ')';
			$query .= ' SET';
			foreach ($args["fields"] as $f =>$value)
			{
				if($f == "modified") { $value = date("Y-m-d H:i:s"); }
				$query .= ' ' . $f . '="' .$value . '",';
			}
			$query = rtrim($query,',');	#remove last komma
			$query .= ' WHERE ' . $args["key"] . ' ="' . $args["value"].'"';
			
			$result=$wpdb->query($query);
			return($result);
		}
	}
	/**
	 * copy record
	 * the fields created and modified are set to the current date
	 * $args['table'] - databasetable
	 * $args['fields'] - array of fields $fields=array("field1"=>$value,"field2"=>$value .... )
	 */
	public function CopyRecord($args)
	{
		$db = \JFactory::getDbo();
		$query = $db->getQuery(true);
		$table = isset($args["table"]) ? '#__' . $args["table"] : "";
		$columns = array();
		$values = array();
		foreach ($args["fields"] as $f =>$value)
		{
			array_push($columns,$f);
			if(is_array($value)) {$value = json_encode($value);}
			array_push($values,$this->quote($value));
		}
		// Prepare the insert query.
		$query
		->insert($this->quotename($table))
		->columns($this->quotename($columns))
		->values(implode(',', $values));

	// Set the query using our newly populated query object and execute it.
		$db->setQuery($query);
		$db->execute();
		return $db->insertid();
	}
	/**
	 * delete a record on unique key
	 * $args['table'] - databasetable
	* $args['key'] = name of unique key
	* $args['value'] = value of unique ke
	 */
	public function DeleteRecord($args)
	{
		if(PRANA_CMS == "joomla")
		{	
			$db = \JFactory::getDbo();
			$table = isset($args["table"]) ? '#__' . $args["table"] : "";
			$key = isset($args["key"]) ? $args["key"] : "";
			$value = isset($args["value"]) ? $args["value"] : "";
			$conditions = $key . ' = "' . $value . '"';
			$query='DELETE FROM '. $table . ' WHERE (' . $conditions . ')';
			#echo $query;
			$db->setQuery($query);
			$result = $db->execute();
		}
		if(PRANA_CMS == "wordpress")
		{	
			global $wpdb;
			$wptable = $wpdb->prefix . $args["table"];
			$key = isset($args["key"]) ? $args["key"] : "";
			$value = isset($args["value"]) ? $args["value"] : "";
			$conditions = $key . ' = "' . $value . '"';
			$query='DELETE FROM '. $wptable . ' WHERE (' . $conditions . ')';			
			$result=$wpdb->query($query);
		}
		return($result);
	}
	/**
	* delete all recods in table
	* $args['table'] - databasetable
	**/
	public function DeleteAllRecords($args)
	{
		
		if(PRANA_CMS == "joomla")
		{	
			$db = \JFactory::getDbo();
			$table = isset($args["table"]) ? '#__' . $args["table"] : "";
			$query = 'DELETE FROM ' . $table;
			$db->setQuery($query);
			$result = $db->execute();
		}
		if(PRANA_CMS == "wordpress")
		{	
			global $wpdb;
			$wptable = $wpdb->prefix . $args["table"];
			$query = 'DELETE FROM ' . $wptable;
			$result=$wpdb->query($query);
		}
		return($result);
	}
	/**
 	* Returns the number of records in the database.
 	* $args['table'] - databasetable
 	*/

	public function CountRecords($args) 
	{
		if(PRANA_CMS == "joomla")
		{	
			$table = isset($args["table"]) ? '#__' . $args["table"] : "";
			$db = \JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select(array('e.*'));
			$query->from($this->quotename($table, 'e'));
			$db->setQuery($query);
			$count = count($db->loadObjectList());
		}
		if(PRANA_CMS == "wordpress")
		{	
			global $wpdb;
			$wptable = $wpdb->prefix . $args["table"];
			$query = "SELECT COUNT(*) FROM " . $wptable;
			$count = $wpdb->get_var($query);

		}
		return($count);
 	}
	#
	# get description of all columns
	#
	
	public function DescribeColumns($args)
	{
		$table = isset($args["table"]) ? '#__' . $args["table"] : "";
		if(PRANA_CMS == "joomla")
		{
			$table = isset($args["table"]) ? '#__' . $args["table"] : "";
			$result=$wpdb->query($query);
			$db = \JFactory::getDbo();
			$query = 'DESCRIBE '.$table;
			$db->setQuery($query);
			$result = $db->loadObjectList();
			return($result);
		}
		if(PRANA_CMS == "wordpress")
		{

			global $wpdb;
			$table = $wpdb->prefix . $args["table"];
			$query = sprintf('DESCRIBE %s',$table);
			$result = $wpdb->get_results($query,OBJECT_K);
			return($result);
		}
	}
	/**
	* lijst van alle kolommen
	* bv:
	* Array ( [0] => id [1] => created [2] => modified [3] => catalogusnummer [4] => auteur [5] => titel [6] => annotatie [7] => uitgever [8] => jaarvanuitgave ) 
	*/
	public function Columns($table)
	{
		if(PRANA_CMS == "joomla")
		{	
			$db = \JFactory::getDbo();
			$jtable = '#__' . $table;
			$cols = $db->getTableColumns($jtable);
			$columns = array();
			foreach ($cols as $key => $value) 
			{
				array_push($columns,$key);
			}
		}
		if(PRANA_CMS == "wordpress")
		{

			global $wpdb;
			$columns = array();
			foreach ( $wpdb->get_col("DESC " .$wpdb->prefix . $table,0) as $col ) 
			{
				array_push($columns,$col);
			}
		}
		return($columns);
	}
	/**
	* Is column present in table?
	**/
	public function IsColumn($table,$col)
	{
		$cols = $this->Columns($table);
		return(in_array($col,$cols));
	}
	#
	# read a record 
	# $args['table'] - databasetable
	# $args['id'] - id of record
	public function ReadRecord($args)
	{
		if(PRANA_CMS == "joomla")
		{	
			$table = isset($args["table"]) ? '#__' . $args["table"] : "";
			$id = isset($args["id"]) ? $args["id"] : "";
			$db = \JFactory::getDbo();
			$query = $db->getQuery(true);
			$query
				->select(array('e.*'))
				->where($this->quotename('e.id') . " = " . $this->quote($id))
				->from($this->quotename($table, 'e'));
			$db->setQuery($query);
			$row = $db->loadObject();
		}
		if(PRANA_CMS == "wordpress")
		{
			global $wpdb;
			$table = $wpdb->prefix . $args["table"];
			$query = sprintf("SELECT * FROM %s WHERE %s=%s",$table,$this->quotename("id"),$this->quote($args["id"]));
			$row =  $wpdb->get_row($query);
		}
		return($row);	
	}
	/**
	* read all records of  table
	* $args['table'] - databasetable
	* $args['sort'] - sort on this field
	**/
	public function ReadAllRecords($args)
	{
		$sort  = isset($args["sort"]) ? $args["sort"] : "id";
		if(PRANA_CMS == "joomla")
		{	
			$db = \JFactory::getDbo();
			$table = isset($args["table"]) ? '#__' . $args["table"] : "";
			$query = $db->getQuery(true);
			$query
				->select(array('e.*'))
				->from($this->quotename($table, 'e'))
				->order($sort);
			$db->setQuery($query);
			$rows = $db->loadObjectList();
		}
		if(PRANA_CMS == "wordpress")
		{
			global $wpdb;
			$table = $wpdb->prefix . $args["table"];
			$query = sprintf("SELECT * FROM %s ORDER BY %s",$table,$sort);
			$rows = $wpdb->get_results($query,OBJECT_K);
		}
		return($rows);
	}
	/**
     * read all records in associative array
     */
    public function ReadAssocRecords($table)
	{
		if(PRANA_CMS == "joomla")
		{	
			$db = \JFactory::getDbo();
			$table = '#__' . $table;
			$query = $db->getQuery(true);
			$query
				->select(array('e.*'))
				->from($db->quoteName($table,'e'));
			$db->setQuery($query);
			$rows = $db->loadAssocList();
		}
		if(PRANA_CMS == "wordpress")
		{
			global $wpdb;
			$table = $wpdb->prefix . $table;
			$query = sprintf("SELECT * FROM %s",$table);
			$rows = $wpdb->get_results($query,ARRAY_A);
		}
		return($rows);
	}
	#
	# read a record with unique key
	# $args['table'] - databasetable
	# $args['key'] - name of unique key
	# $args['value'] - value of unique key 
	public function ReadUniqueRecord($args)
	{
		if(PRANA_CMS == "joomla")
		{
			$db = \JFactory::getDbo();
			$table = isset($args["table"]) ? '#__' . $args["table"] : "";
			$key = isset($args["key"]) ? "e." . $args["key"] : "";
			$value = isset($args["value"]) ? $args["value"] : "";
			$query = $db->getQuery(true);
			$query
				->select(array('e.*'))
				->where($this->quotename($key) . " = " . $this->quote($value))
				->from($this->quotename($table, 'e'));
			$db->setQuery($query);
			$row = $db->loadObject();
		}
		if(PRANA_CMS == "wordpress")
		{
			global $wpdb;
			$table = $wpdb->prefix . $args["table"];
			$query = sprintf("SELECT * FROM %s WHERE %s=%s",$table,$this->quotename($args["key"]),$this->quote($args["value"]));
			$row =  $wpdb->get_row($query);
		}
		return($row);
	}
	/**
	* UniqueValues
	* make a list of unique values in a column of a table
	* $args['table'] - databasetable
	* $args['column'] - sort on this field
	**/
	public function UniqueValues($args)
	{
		if(PRANA_CMS == "wordpress")
		{
			global $wpdb;
			$uniquevalues = array();
			$table = $wpdb->prefix . $args["table"];
			$query = sprintf("SELECT DISTINCT %s FROM %s",$this->quotename($args["column"]),$table);
			$results = $wpdb->get_results($query,ARRAY_N);
			foreach($results as $result)
			{
				if($result[0]) {array_push($uniquevalues,$result[0]); }
			}
		}
		return($uniquevalues);
	}
	/**
	* frequency table
	* make a frequencytable of a certain column
	* $args['table'] - databasetable
	* $args['column'] - sort on this field
	* $args['where'] - where clause e.g. Coltype=ecol
	**/
	public function FrequencyTable($args)
	{
		if(PRANA_CMS == "wordpress")
		{
			global $wpdb;
			$table = $wpdb->prefix . $args["table"];
			$query = sprintf("SELECT %s, COUNT(*) AS frequentie FROM %s GROUP BY %s ORDER BY frequentie DESC",$this->quotename($args["column"]),$table,$this->quotename($args["column"]));
			$rows = $wpdb->get_results($query,ARRAY_A);
		}
		return($rows);
	}

	/**
	* ReadRecords 
	* $args['table'] - databasetable
	* $args['columns'] - column info (not required) e.g.  [["id","nr","int"],["name","naam","string"]]
	* $args['sort'] - column to be sorted followed by direction (column ASC/DESC) ASC is default vale
	* $args['filters'] - Array ( [column1] => value [column2] => value ........ )
	*					Checks if value appears in content
	*					and conditions of columns
	* 					Bij filters : value may be preceded by:
	*					# : search on full content
	*					< : content should be <= value
	*					> : content should be >= value
	* $args['sql']    SQL statement
	* $args['where']		Array ( [column1] => value [column2] => value ........ ) 
	*					Checks if value is exact the contenst of a field
	*					or conditions of columns
	* $args["search'] - array(array ('column1','column2' ....),$value)
	*					- match $value in the given columns
	* $args['searchonly']	- search only on records with this content Array ( [column1] => value [column2] => value ........ ) 
	* $args['searchexcept']	- search records except with this content Array ( [column1] => value [column2] => value ........ ) 
	* $args['searchfilter']	- filter on table before searching (only in combination worg $ags['search'])
	* $args['page'} - current pagenumber
	* $args['maxlines'] - maxlines per page
	* $args['output'] - (string) (Optional) Any of ARRAY_A | ARRAY_N | OBJECT | OBJECT_K constants. default=OBJECT
	*/
	public function ReadRecords($args)
	{
		$args = prana_ParseArgs( $args, $this->dbiodefaults );
		#echo '<br>args: ';
		#print_r($args);
		#echo '<br>';
		if(PRANA_CMS == 'joomla') {$db = \JFactory::getDbo();}
		if(PRANA_CMS == 'wordpress') { global $wpdb; }
		#
		# make conditions for the query
		#
		$orconditions=array();
		$andconditions=array();
		$orandconditions=array();
		$where = '';
		#
		# translate filters to query conditions
		#
		/**
		 * test if values are equal woth content of collumns
		 */
		if($args["sql"])
		{
			$where = $args["sql"];	# where clause is defined
		}
		if($args["where"])
		{
			#echo "<br>where";
			#print_r($args["where"]);
			#echo "<br>";
			foreach($args["where"] as $f => $value)
			{
				array_push($andconditions,$this->quotename($f) . ' = ' . $this->quote($value));
			}
		}
		if($args["searchonly"])
		{
			#echo "<br>searchonly";
			#print_r($args["searchonly"]);
			#echo "<br>";
			foreach($args["searchonly"] as $f => $value)
			{
				array_push($andconditions,$this->quotename($f) . ' = ' . $this->quote($value));
			}
			echo '<br>searchonly andconditions:<br>';
			print_r($andconditions);
		}
		if($args["searchfilter"])
		{
			#echo "<br>searchfilter";
			#print_r($args["where"]);
			#echo "<br>";
			foreach($args["searchfilter"] as $f => $value)
			{
				array_push($andconditions,$this->quotename($f) . ' = ' . $this->quote($value));
			}
		}

		if($args["searchexcept"])
		{
			#echo "<br>where";
			#print_r($args["where"]);
			#echo "<br>";
			foreach($args["searchexcept"] as $f => $value)
			{
				array_push($andconditions,$this->quotename($f) . ' != ' . $this->quote($value));
			}
		}
		/**
		 * all records where the key is part of the given columns
		 * 	$args['searchtype'] = 'string' => key is a string , search for complete string
		 * 	$args['searchtype'] = 'andword' => key is one or more words , do an and search on these words
		 * 	$args['searchtype'] = 'orword' => key is one or more words , do an or search on these words
		 * in any of the given columns
		 * OR condition
		 * select *from yourTableName
		 */
		if($args["search"])
		{
			#echo "<br>search";
			#print_r($args["search"]);
			#echo '<br>';
			$columns = $args["search"][0];
			$value = $args["search"][1];
			$searchtype = $args["search"][2];
			if($columns && $value)
			{
				foreach ($columns as $f)
				{
					switch ($searchtype)
					{
						case "orwords":
						$words = explode(" ",$value);
						#echo "words<br>";
						print_r($words);
						#echo "<br>";
						foreach($words as $w)
						{
							$s = '(^|[[:space:]])' . $w . '([[:space:]]|$)';
							array_push($orconditions, $this->quoteName($f) . ' regexp ' . $this->quote($s));
						}
						break;

						case "andwords":
						$tconditions=array();
						$words = explode(" ",$value);
						#echo "words<br>";
						#print_r($words);
						#echo "<br>";
						foreach($words as $w)
						{
							$s = '(^|[[:space:]])' . $w . '([[:space:]]|$)';
							array_push($tconditions, $this->quoteName($f) . ' regexp ' . $this->quote($s));
						}
						array_push($orandconditions,$tconditions);
						break;
					
						case "string":
						$key = '%'.$value.'%'; #match on content
						array_push($orconditions, $this->quoteName($f) . ' LIKE ' . $this->quote($key));
						break;
					}
				}
			}
		}
		/**
		 * all records where all combinations of values and columns is positive.
		 * AND condition.
		 */
		if($args["filters"])
		{
			#echo'filter<br>';
			#print_r($args["filters"]);
			#echo "<br>";
			foreach($args["filters"] as $f => $value)
			{
				#echo $f . "<>" . $value;
				#echo "<br>";
				if(!$value) { continue; }
				#
				# If < or > before value search on <= resp >=
				#
				if(preg_match('/^>(.*)/',$value,$match))   
				{
					$value = $match[1];
					#echo "<br>push". $value;
					array_push($andconditions,$this->quotename($f) . ' >= ' . $this->quote($value));
				}
				elseif(preg_match('/^<(.*)/',$value,$match))   
				{
					$value = $match[1];
					array_push($andconditions,$this->quotename($f) . ' <= ' . $this->quote($value));
				}
				#
				# if key is numeric then check if key is solid (not a part of string)
				#
				elseif(is_numeric($value))
				{
					# columns defined and is column integer, match full record
					if(isset($args["columns"]) && $this->Columntype(array("columns"=>$args["columns"],"column"=>$f)) == "int")
					{
						#echo $value . "value is numeric and numeric field ";
						$key = '^'.$value.'$';
						array_push($andconditions,$this->quotename($f) . ' REGEXP '. $this->quote($key));
					}
					else
					{
						# nu test die op deel van field, kan beter TODO
						$key = '^(.*?(\b'.$value.'\b)[^$]*)$';
						array_push($andconditions,$this->quotename($f) . ' REGEXP '. $this->quote($key));
					}
				}
				else
				{
					#
					# empty / not empty field
					#
					if(preg_match("/NOTNULL/",$value))
					{
						array_push($andconditions,'(' . $this->quotename($f) . ' != ' . $this->quote("") . ' and ' . $this->quotename($f) . ' IS NOT NULL' . ')');

					}
					elseif(preg_match("/NULL/",$value))
					{
						#echo '<br> is NULL';
						array_push($andconditions,'(' . $this->quotename($f) . ' = ' . $this->quote("") . ' or ' . $this->quotename($f) . ' IS NULL' . ')');
					}
					elseif(preg_match("/#/",$value))
					{
						$key=$this->quote(substr($value,1));   #search on full content
						array_push($andconditions,$this->quotename($f) . ' = ' . $key);
					}
					else
					{
						$key = $this->quote('%'.$value.'%'); #match on content
						array_push($andconditions,$this->quotename($f) . ' LIKE ' . $key);
					}
				}
			}
		}
		#
		# start the query
		#
		if(PRANA_CMS == 'wordpress')
		{
			#echo "<br>start query";
			#echo "<br>table=" . $args["table"];
			#echo "<br>andconditions<br>";
			#print_r($andconditions);
			#echo "<br>orandconditions<br>";
			#print_r($orandconditions);
			#echo "<br>orconditions<br>";
			#print_r($orconditions);
			#echo '<br>';
			/*
				wordpress
			*/
			$where = '';
			$inwhere = FALSE;
			$rows = array();
			$query = sprintf ('select * from %s%s',$wpdb->prefix,$args["table"]);
			#
			# where clause is given
			#
			if($where != '')
			{
				$where= '(' . $where . ')';
			}
			else
			{
				#
				# get and conditioons (only when conditions is empty)
				#
				if(!empty($andconditions))
				{
					$inand= FALSE;
					$where .= '(';
					foreach ($andconditions as $c)
					{
						if($inand == TRUE) { $where .= ' and '; $where .= $c;}
						else {$where .= ' ' .$c; }
						$inand = TRUE;
					}
					$where .= ')';
				}
				#
				# get or conditioons (only when conditions is empty)
				#
				if(!empty($orconditions))
				{
					if(isset($inand)) { $where .= ' and ';}
					$inor = FALSE;
					$where .= '(';
					foreach ($orconditions as $c)
					{
						if($inor == TRUE) { $where .= ' or '; $where .= $c;}
						else {$where .= ' ' .$c; }
						$inor = TRUE;
					}
					$where .= ')';
				}
				#
				# and or and conditions and ((b and c) or (d and e) or ....)
				#
				if(!empty($orandconditions))
				{
					if(isset($inand)) { $where .= ' and ';}

					$inorand= FALSE;
					$where .= '(';
					foreach ($orandconditions as $tandconditions)
					{
						if($inorand == TRUE) { $where .= ') or ('; }
						$inandand=FALSE;
						foreach($tandconditions as $c)
						{
							if($inandand == TRUE) { $where .= ' and '; $where .= $c;}
							else {$where .= ' ' . $c; }
							$inandand = TRUE;
						}
						$inorand=TRUE;
					}
					$where .= ')';
				}
			}
			if($where) { $query .= ' where ' . $where; }
			if($args["sort"] && $args["sort"] != 'no') 
			{ 
				$s = explode(" ",$args["sort"]);
				if(!isset($s[1])) { $s[1]="ASC"; } #ASC is default order direction
				$order = $this->quotename($s[0]) . $s[1];
				$query .= sprintf(" ORDER BY %s ", $order);
			}
			if($args["maxlines"])
			{
				$offset=0;
				if(is_numeric($args["maxlines"])) { $offset=($args["page"]-1)*$args["maxlines"]; }
				$query .= sprintf(' LIMIT %d OFFSET %d',$args['maxlines'],$offset);
			}
			#echo '<br>query<br>'.$query;
			if($args["execute"] == TRUE)
			{
				$rows = $wpdb->get_results($query,OBJECT_K);
			}
		}	
		if(PRANA_CMS == 'joomla')
		{
			$query = $db->getQuery(true);
			$query->select('*');
			###
			$query->from($this->quotename('#__' . $args["table"]));
			if($conditions != '')
			{
				#echo '<br>'. $conditions;
				$query->where($conditions);
			}
			foreach ($andconditions as $index => $c)
			{
				if($index) $query->andwhere($c);
				else $query->where($c);
			}
			foreach ($orconditions as $index => $c)
			{
				#print_r($orconditions);
				if($index) $query->orwhere($c);
				else $query->where($c);
			}
			if($args["sort"] && $args["sort"] != 'no') 
			{ 
				$s = explode(" ",$args["sort"]);
				if(!isset($s[1])) { $s[1]="ASC"; } #ASC is default order direction
				$order = $this->quotename($s[0]) . $s[1];
				#echo "<br>order=".$order;
				$query->order($this->quotename($s[0]) . $s[1]);
			}
			#
			# $limit is maximum number of rows to be displayed
			# $page = current pagenumber
			# so calculate offset
			#
			if($args["maxlines"])
			{
				$offset=0;
				if(is_numeric($args["maxlines"])) { $offset=($args["page"]-1)*$args["maxlines"]; }
				$query->setLimit($args["maxlines"],$offset);
			}
			$db->setQuery($query);
			$rows = $db->loadObjectList();
		}
		return($rows);
	}
	/*
		* args["tableA"]  - relations
		* args["tableB"]  - appartments
		* $args["colA"]   - house
		* $args["colB"]   - huisnummer
		* $args["col"]	  - gebouw
		* $args["value"]  - 411
		* ["gebouw","house","appartments","huisnummer","building"]
 */
	public function JoinRecord($args)
	{
		$db = \JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);
    	$query->select(array('a.*', 'b.*'));
    	$query->from($this->quotename('#__' . $args["tableA"] , 'a'));
		$query->join('INNER', $this->quotename('#__' . $args["tableB"], 'b') . ' ON ' . $this->quotename('a.'.$args["colA"]) . ' = ' . $this->quotename('b.'.$args["colB"]));
    	$query->where($this->quotename('a.'.$args["col"]) . ' = ' . $this->quote($args["value"]));
		#$db->setQuery($query);
		$row = $db->loadObject();
		return($row);
	}	
	/**
	* display all fields of a record
	* $args['table'] - databasetable
	* $args['key'] - name of unique key
	* $args['value'] - value of unique key
	 */
	public function DisplayAllFields($args)
	{
		$html = '';
		#
		# get the column names in the table
		#
		$columns = $this->Columns($args["table"]);
		$p=$this->ReadUniqueRecord($args);
		#
		# display content of all fields
		#
		foreach($columns as $c)
		{    # table => relationtable

			$html .= '<div class="row" style="margin-bottom:2px;">';
			$html .= 	'<div class="col-md-2">';
			$html .= $c;
			$html .=	'</div>';
			$html .= '<div class="col-md-8">';
			$html .= $p->$c;
			$html .= '</div>';
			$html .= '</div>';
		}
		return($html);
	}
	/**
	 * Select all the different values from a column in a table:
	 * $args['table'] - databasetable
	 * $args["column'] - distinct column
	 */
	public function DistinctRecords($args)
	{
		if(PRANA_CMS == "joomla")
		{	
			$db = \JFactory::getDbo();
			$table = isset($args["table"]) ? '#__' . $args["table"] : "";
			$query = 'SELECT DISTINCT ' . $args['column'] . ' FROM ' . $table;
			$query .= ' ORDER BY ' . $args['column'];
			$db->setQuery($query);
			$rows = $db->loadObjectList();
		}
		if(PRANA_CMS == "wordpress")
		{	
			global $wpdb;
			$wptable = $wpdb->prefix . $args["table"];
			$query = 'SELECT DISTINCT ' . $args['column'] . ' FROM ' . $wptable;
			$query .= ' ORDER BY ' . $args['column'];
			$rows = $wpdb->get_results($query,OBJECT_K);
		}
		return($rows);
    }
		/**
	 * Columntype($args) 
	 * $args["columns'] - column info (not required) e.g.  [["id","nr","int"],["name","naam","string"]]
	 * $args['column'] - column nam
	 */
	public function Columntype($args)
	{
		foreach ($args['columns'] as $c)
		{
			if($c[0] == $args["column"]) { return($c[2]); }
		}
    }
	public function quoteName($key)
	{
		return('`' . $key . '`');
	}
	public function quote($value)
	{
		return("'" . $value . "'");
	}

}
?>