


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

	}