


<?php

//require_once("include/config.php");

define("TBLOP_EXECUTE_SELECT", 1);

define("TBLOP_EXECUTE_INSERT", 2);

define("TBLOP_EXECUTE_UPDATE", 3);

define("TBLOP_EXECUTE_DELETE", 4);

 //Connect to DB using Connection Class

/***************** Table Operations Class ***************/

class tableop{

	private $db   				= DB_DATABASE;

	private $host 				= DB_HOST;

	private $user 				= DB_USER;

	private $pass 				= DB_PASS;

	private $now				= "0000-00-00 00:00:00";

	private $table				= "";

	private $primaryKey			= "";

	private $fields	  			= array();		

	private $insert_id			= 0;

	private $arrFormData		= array();

	private $errorString		= '';

	public $connection;
    function __construct(){

		$this->connection	= $this->connect();


		$this->now 			= date("Y-m-d h:i:s");

		$this->insert_id	= 0;
	
	}

	function connect()

	{

		$connection = mysqli_connect($this->host, $this->user, $this->pass) or die("<span style='font-family:Arial, Helvetica, sans-serif; color:#FF0000; font-size:12px; font-weight:bold;'>An error occured connection :</span>".mysqli_connect_error($connection));

		mysqli_select_db($connection,$this->db)or die("<span style='font-family:Arial, Helvetica, sans-serif; color:#FF0000; font-size:11px; font-weight:bold;'>An error occured dbconnect:</span>".mysqli_errno($connection));

	
				mysqli_query($connection,"SET NAMES utf8;");

				mysqli_query($connection,'SET CHARACTER_SET utf8;');

		return $connection;

	}

	function close($connection)

	{

		mysqli_close($connection);

	}







	function query($sql)

	{	

		//mysqli_query($dbconnection,"SET NAMES utf8;");

		//mysqli_query($dbconnection,'SET CHARACTER_SET utf8;');

		$connection =$this->connect();

		$res = mysqli_query($connection,$sql);

		if(!$res)

		{

			$this->setError('An error occured query :'.mysqli_error($connection));

			return false;

		}

		else

		{

			$this->insert_id = mysqli_insert_id($connection); 

			return $res;

		}

	}

	function query_result($sql){
		$query	= $this->query($sql);
		if($query)

		{
			$res=array();
			if(!empty($query)){
				while($row = mysqli_fetch_assoc($query) ){
				    $res[] = $row;

				   // $res= array_merge($res,$a1);
				}
				
			}else{
				$res=array();
			}
			

			return $res;

		}

		else

		{

			return false;

		}

	}


	function query_result_row($sql){
		$query	= $this->query($sql);

		if($query)

		{

			$res 	= 	mysqli_fetch_assoc($query);
			return $res;

		}

		else

		{

			return false;

		}
	}



	function rowCount($sql){
		$query	= $this->query($sql);

		if($query)
		{
			$res 	= 	MYSQLI_NUM_rows($query);
			return $res;
		}
		else
		{
			return false;
		}
	}



	function insert_query($sql){
		$connection =$this->connect();
		$query	= $this->query($sql);

		if($query)
		{
			
			return $this->insert_id; 
		}
		else
		{
			return false;
		}
	}



















	function optimiseFields($table)

	{

		$connection =$this->connect();

		$result = mysqli_query($connection,"SHOW COLUMNS FROM ".$table);

		if (!$result)

		{

			die ('An error occured ss :'.mysqli_error($connection));

		}

		if (MYSQLI_NUM_rows($result) > 0)

		{ 

			$structure = array();

			while ($row = mysqli_fetch_assoc($result))

			{ 		

				$structure[$row['Field']] = '';

			}

		}

		return $structure;

	} 

	function getPrimaryKey($table)

	{

		$connection =$this->connect();

		$result = mysqli_query($connection,"SHOW COLUMNS FROM ".$table);

		if (!$result)

		{

			die ('An error occured primary :'.mysqli_error($connection));

		}

		else

		{

			if (MYSQLI_NUM_rows($result) > 0)

			{ 

				while ($row = mysqli_fetch_assoc($result))

				{ 

					if ($row['Key'] == 'PRI')

					{ 

						$primaryKey = $row['Field'];

					}

				}

				return $primaryKey;

			}

		}

	}

	function setError($str)

	{

		$this->errorString = $str;

	}

	function getError()

	{

		return $this->errorString;

	}

	
	function execute($values = NULL, $mode = TBLOP_EXECUTE_INSERT, $where = NULL, $order = NULL)

	{

		if (($mode == TBLOP_EXECUTE_INSERT || $mode == TBLOP_EXECUTE_UPDATE) && (!is_array($values) || count($values)==0))

		{

			//echo  "No Data for Insertion / Updation";

			$this->setError('An error occured  arg: Invalid Arguments Supplied');

			return false;

		}

		//$values = $this->ArrRemoveXSS($values);

		$values	= $this->clearArray($values);

		$sql	= $this->buildQuery($values, $mode, $where, $order);

		$res  	= $this->query($sql);



		return true;

	}


	function clearArray($array) //Will Clear the repeated Elements from array

	{

		if (!is_array($array) || count($array)==0)

		{

			$this->setError('An error occured clear : Invalid Arguments Supplied');

			return false;

		}

		foreach ($array as $key => $val){

			if (!array_key_exists($key, $this->fields))

				unset($array[$key]);

		}

		return $array;

	}

	function buildQuery($value = NULL, $mode = TBLOP_EXECUTE_SELECT, $where = FALSE, $order = FALSE)
	{
		$where	= ($where) ? " WHERE $where " 		: "";
		$order	= ($order) ? " ORDER BY $order " 	: " ORDER BY {$this->primaryKey} ";
		$temp	= "";
		$connection =$this->connect();

        switch ($mode) {
            case TBLOP_EXECUTE_SELECT:
				$temp	= implode(",", array_keys($this->fields));
				//echo "SELECT $temp FROM {$this->table} $where $order ";  
				return "SELECT $temp FROM {$this->table} $where $order ";

            case TBLOP_EXECUTE_UPDATE:
				array_walk($value, "alter");
				$temp	= implode(", ", array_values($value));
			//	echo "UPDATE {$this->table} SET $temp $where ";  exit;
				return "UPDATE {$this->table} SET $temp $where ";

            case TBLOP_EXECUTE_INSERT:
			
				//$value=array_map('mysqli_real_escape_string',$value);
				//$value	=	array_map(mysqli_real_escape_string($connection,$value),$value);

				$tempF	= implode(",", array_keys($value));
				array_walk($value, "insertval");
				$tempV	= implode(", ", array_values($value));
				
				//echo "<br> INSERT INTO {$this->table} ($tempF) VALUES ($tempV)";exit;
				return "INSERT INTO {$this->table} ($tempF) VALUES ($tempV)";

            case TBLOP_EXECUTE_DELETE:	
				//echo "DELETE FROM {$this->table} $where ";	exit;
				return "DELETE FROM {$this->table} $where ";
			//default:
				//echo " No operations performed ";
		}
	}



	

	function getOnerecord($sql) 

	{

		//$sql	= $this->buildQuery(NULL, TBLOP_EXECUTE_SELECT, $where, $order);

		$query	= $this->query($sql);

		if(!$query)

		{

			return false;

		}

		else

		{

			$res 	= 	mysqli_fetch_assoc($query,MYSQLI_ASSOC);

			mysqli_free_result($query); 

			return $res['count'];

		}

	}

	function getAll($where = NULL, $order = NULL)

	{	

		$res	= array();

		$sql	= $this->buildQuery(NULL, TBLOP_EXECUTE_SELECT, $where, $order);

		$query	= $this->query($sql);

		if(!$query)

		{

			return false;

		}

		else

		{

			while($array 	= 	mysqli_fetch_array($query,MYSQLI_ASSOC))

			{

				$res[]	= $array;

			}

			mysqli_free_result($query); 

			return $res;

		}

	}

	function insert($array)  //onSuccess Return 'success'  

	{return $this->execute($array);}	

	function insert_video($array)  //onSuccess Return 'success'  

	{return $this->execute_video($array);}	

	function delete($cond)   //onSuccess Return 'success'  

	{return $this->execute($array = "", TBLOP_EXECUTE_DELETE, $cond);}

	function update($array, $cond) //onSuccess Return 'success'  

	{return $this->execute($array, TBLOP_EXECUTE_UPDATE, $cond);}

	function update_video($array, $cond) //onSuccess Return 'success'  

	{return $this->execute_video($array, TBLOP_EXECUTE_UPDATE, $cond);}

	function updateField($array, $cond) //onSuccess Return 'success'  

	{return $this->execute($array, TBLOP_EXECUTE_UPDATE, $cond);}

	function insertId(){return $this->insert_id;} //onSuccess Return 'success'  

	function count($where = NULL){

		$where	= ($where) ? " WHERE $where " 		: "";

		$sql 	= "SELECT $this->primaryKey FROM {$this->table} $where";

		//echo $sql;

		$result = $this->query($sql);

		if($result)

		{

			$res = MYSQLI_NUM_rows($result);

			//mysqli_free_result($query); 

			return $res;

		}

		else

		{

			return false;

		}

	}

	function recordCount($sql){

		$result = $this->query($sql);

		if($result)

		{

			$res = MYSQLI_NUM_rows($result);

			return $res;

		}

		else

		{

			return false;

		}

	}

	function getFields($fields, $where = NULL, $order = NULL)

	{

		$fields	= ($fields) ? " $fields " 			: " * ";

		$where	= ($where) ? " WHERE $where " 		: "";

		$order	= ($order) ? " ORDER BY $order " 	: " ORDER BY {$this->primaryKey} ";

		$sql	= "SELECT $fields FROM {$this->table} $where $order;";

		//echo $sql;

		$query	= $this->query($sql);

		if($query)

		{

			while($array 	= 	mysqli_fetch_array($query,MYSQLI_ASSOC)){

					$res[]	= $array;

			}

			mysqli_free_result($query); 

			return $res;

		}

		else

		{

			return false;

		}

	}

	function getRowByFields($fields, $where = NULL, $order = NULL, $group = NULL)

	{

		$fields	= ($fields) ? " $fields " 			: " * ";

		$where	= ($where) ? " WHERE $where " 		: "";

		$order	= ($order) ? " ORDER BY $order " 	: " ORDER BY {$this->primaryKey} ";

		$group	= ($group) ? " GROUP BY $group " 	: "";

		$sql	= "SELECT $fields FROM {$this->table} $where $group $order;";

		$query	= $this->query($sql);

		if($query)

		{

			$array 	= 	mysqli_fetch_array($query,MYSQLI_ASSOC);

			mysqli_free_result($query); 

			return $array;

		}

		else

		{

			return false;

		}

	}

	function getRow($where = NULL, $order = NULL)

	{

		$sql 	= $this->buildQuery(NULL, TBLOP_EXECUTE_SELECT, $where, $order);

		$query	= $this->query($sql);

		if($query)

		{

			$array 	= 	mysqli_fetch_array($query,MYSQLI_ASSOC);

			mysqli_free_result($query); 

			return $array;

		}

		else

		{

			return false;

		}

	}

	function getOptions($strKey, $strVal , $selected = NULL, $where = NULL, $order = NULL)

	{

		$strOptions = "";

		$where	= ($where) ? " WHERE $where " 		: "";

		$order	= ($order) ? " ORDER BY $order " 	: " ORDER BY {$this->primaryKey} ";

		$sql 	= "SELECT * FROM {$this->table} $where $order;";

		//echo $sql;

		$query	= $this->query($sql);

		if($query)

		{

			while($array 	= 	mysqli_fetch_array($query,MYSQLI_ASSOC)){

					$res[]	= $array;

			}

			mysqli_free_result($query); 

			if($res)

			foreach ($res as $key => $val){

				if(is_array($selected))

					$strOptions .= "<option value='{$val[$strKey]}' ".((in_array($val[$strKey],$selected)) ? "selected" : "").">{$val[$strVal]}</option>\r\n";

				else

					$strOptions .= "<option value='{$val[$strKey]}' ".(($val[$strKey] == $selected) ? "selected" : "").">{$val[$strVal]}</option>\r\n";

			}

			return $strOptions;

		}

		else

		{

			return false;

		}

	}

	function getOptionsSql($strKey, $strVal , $selected = NULL, $sql)

	{

		$strOptions = "";

		//$where	= ($where) ? " WHERE $where " 		: "";

		//$order	= ($order) ? " ORDER BY $order " 	: " ORDER BY {$this->primaryKey} ";

		//$sql 	= "SELECT * FROM {$this->table} $where $order;";

		//echo $sql;

		$query	= $this->query($sql);

		if($query)

		{

			while($array 	= 	mysqli_fetch_array($query,MYSQLI_ASSOC)){

					$res[]	= $array;

			}

			mysqli_free_result($query); 

			if($res)

			foreach ($res as $key => $val){

				if(is_array($selected))

					$strOptions .= "<option value='{$val[$strKey]}' ".((in_array($val[$strKey],$selected)) ? "selected" : "").">{$val[$strVal]}</option>\r\n";

				else

					$strOptions .= "<option value='{$val[$strKey]}' ".(($val[$strKey] == $selected) ? "selected" : "").">{$val[$strVal]}</option>\r\n";

			}

			return $strOptions;

		}

		else

		{

			return false;

		}

	}

	function getselectOptions($strKey,$strkey2, $strVal , $selected = NULL, $where = NULL, $order = NULL)

	{

		$strOptions = "";

		$where	= ($where) ? " WHERE $where " 		: "";

		$order	= ($order) ? " ORDER BY $order " 	: " ORDER BY {$this->primaryKey} ";

		$sql 	= "SELECT * FROM {$this->table} $where $order;";

		//echo $sql;

		$query	= $this->query($sql);

		if($query)

		{

			while($array 	= 	mysqli_fetch_array($query,MYSQLI_ASSOC)){

					$res[]	= $array;

			}

			mysqli_free_result($query); 

			if($res)

			foreach ($res as $key => $val){

				if(is_array($selected))

					$strOptions .= "<option value='{$val[$strKey]}' $strkey2='{$val[$strKey]}' ".((in_array($val[$strKey],$selected)) ? "selected" : "").">{$val[$strVal]}</option>\r\n";

				else

					$strOptions .= "<option value='{$val[$strKey]}' $strkey2='{$val[$strKey]}'  ".(($val[$strKey] == $selected) ? "selected" : "").">{$val[$strVal]}</option>\r\n";

			}

			return $strOptions;

		}

		else

		{

			return false;

		}

	}

	function getLimit($from, $count=5, $where = NULL, $order	= NULL) 

	{

		$sql	= $this->buildQuery(NULL, TBLOP_EXECUTE_SELECT, $where, $order);

		//$to = $from + $count;

		$sql.="LIMIT  $from,$count ";

		//echo $sql."<br>";

		$query	= $this->query($sql);

		if($query)

		{

			while($array 	= 	mysqli_fetch_array($query,MYSQLI_ASSOC)){

					$res[]	= $array;

			}

			mysqli_free_result($query); 

			return $res;

		}

		else

		{

			return false;

		}

	}

	 function getPageLink($pgNo, $totPage, $url, $count = "5")

       {

			   $strReturn		=	'';

               $intPre         = $pgNo - 1;

               $intNex         = $pgNo + 1;

               $intFirst         = $pgNo - $count;

               $intLast         = $pgNo + $count;

               if ($intFirst <= 0){

                       $intFirst        = 1;

               }

               if ($intLast >= $totPage){

                       $intLast        = $totPage;

               }

               if ($intPre <= 0){

                       $intPre                = 1;

                       $strReturn        = str_replace("{pgTxt}", "<", $strReturn);

               }else{

                       $strReturn        = str_replace("{pgNo}", "$intPre", $url);

                       $strReturn        = str_replace("{pgTxt}", "<", $strReturn);

               }

               for ($i = $intFirst; $i <= $intLast; $i++){

                       if ($i != $pgNo) {

                               $strTemp        = str_replace("{pgNo}", "$i", $url);

                               $strReturn        .= str_replace("{pgTxt}", "$i", $strTemp);

                       }else{

                               //$strReturn        .= "$i";

							    $strTemp        = str_replace("{pgNo}", "$i", $url);

                               $strReturn        .= str_replace("{pgTxt}", "$i", $strTemp);

                       }

               }

               if ($intNex > $totPage){

                       $intNex            = $totPage;

                       $strReturn        .= str_replace("{pgTxt}", ">", $strTemp);

               }else{

                       $strTemp        = str_replace("{pgNo}", "$intNex", $url);

                       $strReturn .= str_replace("{pgTxt}", ">", $strTemp);

               }

               return $strReturn;

       }

	function joinQueryRecord($sql){

		$query	= $this->query($sql);

		if($query)

		{

			while($array 	= 	mysqli_fetch_array($query,MYSQLI_ASSOC)){

					$res[]	= $array;

			}

			mysqli_free_result($query); 

			return isset($res[0])?$res[0]:"";

		}

		else

		{

			return false;

		}

	}

	function joinQuery($sql){

		$res = array();

		$query	= $this->query($sql);

		//echo $sql;

	   if($query)

		{

			while($array 	= 	mysqli_fetch_array($query,MYSQLI_ASSOC)){

					$res[]	= $array;

			}

			mysqli_free_result($query); 

			return $res;

		}

		else

		{

			return false;

		}

	}

	function limitQuery($sql, $from, $COUNT){

		$sql.=" LIMIT  $from,$COUNT ";

		//echo $sql."<br>";

		$query	= $this->query($sql);

		if($query)

		{

			while($array 	= 	mysqli_fetch_array($query,MYSQLI_ASSOC)){

					$res[]	= $array;

			}

			mysqli_free_result($query); 

			return $res;

		}

		else

		{

			return false;

		}

	}

	function joinQueryOne($sql){

		//echo $sql."<br>";

		$query	= $this->query($sql);

		if($query)

		{

			$array 	= 	mysqli_fetch_array($query,MYSQLI_NUM);

			return $array[0];

		}

		else

		{

			return false;

		}

	}

	function ArrRemoveXSS($ArrVal)	 // used for array values

	{  

	if($ArrVal)  

	foreach($ArrVal as $key=>$val)

	 {

	   // remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed 

	   // this prevents some character re-spacing such as <java\0script> 

	   // note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs 

	   $val = preg_replace('/([\x00-\x08][\x0b-\x0c][\x0e-\x20])/', '', $val); 

	   // straight replacements, the user should never need these since they're normal characters 

	   // this prevents like <IMG SRC=&#X40&#X61&#X76&#X61&#X73&#X63&#X72&#X69&#X70&#X74&#X3A&#X61&#X6C&#X65&#X72&#X74&#X28&#X27&#X58&#X53&#X53&#X27&#X29> 

	   $search = 'abcdefghijklmnopqrstuvwxyz'; 

	   $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'; 

	   $search .= '1234567890!@#$%^&*()'; 

	   $search .= '~`";:?+/={}[]-_|\'\\'; 

	   for ($i = 0; $i < strlen($search); $i++) { 

	      // ;? matches the ;, which is optional 

	      // 0{0,7} matches any padded zeros, which are optional and go up to 8 chars 

	      // &#x0040 @ search for the hex values 

	      $val = preg_replace('/(&#[xX]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val); // with a ; 

	      // &#00064 @ 0{0,7} matches '0' zero to seven times 

	      $val = preg_replace('/(&#0{0,8}'.ord($search[$i]).';?)/', $search[$i], $val); // with a ; 

	   } 

	   // now the only remaining whitespace attacks are \t, \n, and \r 

	   $ra1 = Array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base'); 

	   $ra2 = Array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload'); 

	   $ra = array_merge($ra1, $ra2); 

	   $found = true; // keep replacing as long as the previous round replaced something 

	   while ($found == true) { 

	      $val_before = $val; 

	      for ($i = 0; $i < sizeof($ra); $i++) { 

	         $pattern = '/'; 

	         for ($j = 0; $j < strlen($ra[$i]); $j++) { 

	            if ($j > 0) { 

	               $pattern .= '('; 

	               $pattern .= '(&#[xX]0{0,8}([9ab]);)'; 

	               $pattern .= '|'; 

	               $pattern .= '|(&#0{0,8}([9|10|13]);)'; 

	               $pattern .= ')*'; 

	            } 

	            $pattern .= $ra[$i][$j]; 

	         } 

	         $pattern .= '/i'; 

	         $replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2); // add in <> to nerf the tag 

	         $val = preg_replace($pattern, $replacement, $val); // filter out the hex tags 

	         if ($val_before == $val) { 

	            // no replacements were made, so exit the loop 

	            $found = false; 

	         } 

	      } 

	   }

	   $ArrVal[$key]	= $val;

	 } 

	   return $ArrVal; 

	} 

	function RemoveXSS($val) { 

	   // remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed 

	   // this prevents some character re-spacing such as <java\0script> 

	   // note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs 

	   $val = preg_replace('/([\x00-\x08][\x0b-\x0c][\x0e-\x20])/', '', $val); 

	   // straight replacements, the user should never need these since they're normal characters 

	   // this prevents like <IMG SRC=&#X40&#X61&#X76&#X61&#X73&#X63&#X72&#X69&#X70&#X74&#X3A&#X61&#X6C&#X65&#X72&#X74&#X28&#X27&#X58&#X53&#X53&#X27&#X29> 

	   $search = 'abcdefghijklmnopqrstuvwxyz'; 

	   $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'; 

	   $search .= '1234567890!@#$%^&*()'; 

	   $search .= '~`";:?+/={}[]-_|\'\\'; 

	   for ($i = 0; $i < strlen($search); $i++) { 

	      // ;? matches the ;, which is optional 

	      // 0{0,7} matches any padded zeros, which are optional and go up to 8 chars 

	      // &#x0040 @ search for the hex values 

	      $val = preg_replace('/(&#[xX]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val); // with a ; 

	      // &#00064 @ 0{0,7} matches '0' zero to seven times 

	      $val = preg_replace('/(&#0{0,8}'.ord($search[$i]).';?)/', $search[$i], $val); // with a ; 

	   } 

	   // now the only remaining whitespace attacks are \t, \n, and \r 

	   $ra1 = Array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base'); 

	   $ra2 = Array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload'); 

	   $ra = array_merge($ra1, $ra2); 

	   $found = true; // keep replacing as long as the previous round replaced something 

	   while ($found == true) { 

	      $val_before = $val; 

	      for ($i = 0; $i < sizeof($ra); $i++) { 

	         $pattern = '/'; 

	         for ($j = 0; $j < strlen($ra[$i]); $j++) { 

	            if ($j > 0) { 

	               $pattern .= '('; 

	               $pattern .= '(&#[xX]0{0,8}([9ab]);)'; 

	               $pattern .= '|'; 

	               $pattern .= '|(&#0{0,8}([9|10|13]);)'; 

	               $pattern .= ')*'; 

	            } 

	            $pattern .= $ra[$i][$j]; 

	         } 

	         $pattern .= '/i'; 

	         $replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2); // add in <> to nerf the tag 

	         $val = preg_replace($pattern, $replacement, $val); // filter out the hex tags 

	         if ($val_before == $val) { 

	            // no replacements were made, so exit the loop 

	            $found = false; 

	         } 

	      } 

	   } 

	   return $val; 

	} 

	function _setFormData($array)

	{

		$this->arrFormData = $array;

	}

	function getFormData()

	{

		if($this->arrFormData)

			return $this->arrFormData;

	}

	function _clearArray($array)

	{

		//if (!is_array($array) || count($array)==0)

		//	return $this->raiseError(TBLOP_ERROR_INVALID_ARGUMENT, array("file" => __FILE__, "line" => __LINE__));

		if($array)	

		foreach ($array as $key => $val){

			if (!array_key_exists($key, $this->fields))

				unset($array[$key]);

		}

		return $array;

	}

	function isExist($where = NULL, $tablename = NULL)

	{

		$where		= ($where) ? " WHERE $where " : "";

		$tablename	= ($tablename) ? $tablename : $this->table;

		$sql 		= "SELECT count(1) count FROM $tablename $where ;";

	//	echo $sql;exit;

		$res  		= $this->getOne($sql);

		//if (DB::isError($res))

			//return $this->raiseError($res);

		if ($res >= 1)

			return true;

		return false;

	}

	function sumOfrows($field,$where = NULL, $tablename = NULL)

	{

		$where		= ($where) ? " WHERE $where " : "";

		$tablename	= ($tablename) ? $tablename : $this->table;

		$sql 		= "SELECT sum($field) sum FROM $tablename $where ;";

	//echo $sql;exit;

		$query	= $this->query($sql);

		if($query)

		{

			$array 	= 	mysqli_fetch_array($query,MYSQLI_NUM);

			return $array[0];

		}

		else

		{

			return false;

		}

	}

	function getOne($sql) 

	{

		$query	= $this->query($sql);

		if(!$query)

		{

			return false;

		}

		else

		{

			$res 	= 	mysqli_fetch_assoc($query);

			mysqli_free_result($query); 

			return $res['count'];

		}

	}

	function getPostStats($id)

	{	

		//global $db;

		$sql = "SELECT MAX(postdate) AS lastpost "

				 . "FROM df_posts AS P, df_topics AS T, df_forums AS F "

				 . "WHERE F.ctg_id = '$id' "

				 . "AND T.forum_id = F.forum_id AND P.topic_id = T.topic_id";

		//$res	= $this->getRow($sql, DB_FETCHMODE_ASSOC);

		$query	= $this->query($sql);

		if($query)

		{

			$array 	= 	mysqli_fetch_array($query,MYSQLI_ASSOC);

			mysqli_free_result($query); 

			return $array;

		}

		else

		{

			return false;

		}

		//if (DB::isError($res))

			//return $this->raiseError($res);

		//return ($res);

	}

	function getSplFields($fields, $where = NULL, $order = NULL){

		$fields	= ($fields) ? " $fields " 			: " * ";

		$where	= ($where) ? " WHERE $where " 		: "";

		$order	= ($order) ? " ORDER BY '$order' " 	: " ORDER BY {$this->primaryKey} ";

		$sql	= "SELECT $fields FROM {$this->table} $where $order ;";

		$query	= $this->query($sql);

		if($query)

		{

			$array 	= 	mysqli_fetch_array($query,MYSQLI_ASSOC);

			mysqli_free_result($query); 

			return $array;

		}

		else

		{

			return false;

		}

	}

	function monthname($n)

	{

		$monthName = date("F", mktime(0, 0, 0, $n, 10));

		return $monthName;

	}	

/*End Class*/	

}

function getAddress() {

    $protocol = $_SERVER['HTTPS'] == 'on' ? 'https' : 'http';

    return $protocol.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

}

function alter(&$val, $key){
	$val= addslashes($val);

	$val = "$key = '$val'";
}
function insertval(&$val, $key){
	$val= addslashes($val);
	$val = "'$val'";
}
function pre($val){

	echo "<pre>";

	print_r($val);

	echo "</pre>";

}

function pree($val){

	echo "<pre>";

	print_r($val);

	echo "</pre>";

	exit;

}

function dateTimetxtDisplay($date)

{

	return(date("M j, Y h:i A",$date));

}

function dateDisplay($date)

{

	return(date("m-d-Y",strtotime($date)));

}

function dateMDisplay($date)

{

	return(date("M-d-Y",strtotime($date)));

}

function dateYMDisplay($date)

{

	return(date("Y-m-d",strtotime($date)));

}

function dateTimeDisplay($date)

{

	return(date("m-d-Y H:i:s",strtotime($date)));

}

function dateTxtDisplay($date)

{

	return(date("M j, Y h:i A",strtotime($date)));

}

function dateonlyTxtDisplay($date)

{

	return(date("M d, Y",strtotime($date)));

}

function getMonthName($monthno)

{

	return date("F", mktime(0, 0, 0, $monthno, 10));

}

function getLimitedText($str,$limit=0,$moreChar="...")

{

		$str	=	trim($str);				

		if($limit>0)

			{

				if(strlen($str)>$limit)	return substr($str,0,$limit).$moreChar;						

				else					return $str;

			}

		else	

		return $str;

}

function getBetween($content,$start,$end)

{

    $r = explode($start, $content);

    if (isset($r[1]))

	{

        $r = explode($end, $r[1]);

		$nos 	= 	preg_replace('/[^0-9]/',"",$r[0]);

		$leter	=	explode($nos, $r[0]);

		$excelval['excelcolum']	= $leter[0];

		$excelval['excelrow'] 	= $nos;

		return $excelval;

    }

    return '';

}

function convert_number($number) 

{ 

    if (($number < 0) || ($number > 999999999)) 

    { 

    throw new Exception("Number is out of range");

    } 

    $Gn = floor($number / 1000000);  /* Millions (giga) */ 

    $number -= $Gn * 1000000; 

    $kn = floor($number / 1000);     /* Thousands (kilo) */ 

    $number -= $kn * 1000; 

    $Hn = floor($number / 100);      /* Hundreds (hecto) */ 

    $number -= $Hn * 100; 

    $Dn = floor($number / 10);       /* Tens (deca) */ 

    $n = $number % 10;               /* Ones */ 

    $res = ""; 

    if ($Gn) 

    { 

        $res .= convert_number($Gn) . " Million"; 

    } 

    if ($kn) 

    { 

        $res .= (empty($res) ? "" : " ") . 

            convert_number($kn) . " Thousand"; 

    } 

    if ($Hn) 

    { 

        $res .= (empty($res) ? "" : " ") . 

            convert_number($Hn) . " Hundred"; 

    } 

    $ones = array("", "One", "Two", "Three", "Four", "Five", "Six", 

        "Seven", "Eight", "Nine", "Ten", "Eleven", "Twelve", "Thirteen", 

        "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eightteen", 

        "Nineteen"); 

    $tens = array("", "", "Twenty", "Thirty", "Fourty", "Fifty", "Sixty", 

        "Seventy", "Eigthy", "Ninety"); 

    if ($Dn || $n) 

    { 

        if (!empty($res)) 

        { 

            $res .= " and "; 

        } 

        if ($Dn < 2) 

        { 

            $res .= $ones[$Dn * 10 + $n]; 

        } 

        else 

        { 

            $res .= $tens[$Dn]; 

            if ($n) 

            { 

                $res .=  $ones[$n]; 

            } 

        } 

    } 

    if (empty($res)) 

    { 

        $res = "zero"; 

    } 

    return $res; 

} 

function urlsafe_b64decode($string) {

    $data = str_replace(array('-','_'),array('+','/'),$string);

    $mod4 = strlen($data) % 4;

    if ($mod4) {

        $data .= substr('====', $mod4);

    }

    return base64_decode($data);

}

function putThemTogether($array1, $array2) 

{

	$output = array();

	foreach($array1 as $key => $value) {

	    if (!isset($output[$key]))

	        $output[$key] = array();

	       $output[$key][] = $value;

	   }

	   foreach($array2 as $key => $value) {

		    if (!isset($output[$key]))

		           $output[$key] = array();

			        $output[$key][] = $value;

			    }

				    return $output;

}

function empty_date($var)

{

    if (empty($var) or $var === '0000-00-00' or $var === '0000-00-00 00:00:00')

        return true;

    else

        return false;

}