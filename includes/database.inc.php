<?php

	/* -- THIS FILE CONTAINS ALL MYSQL DATABASE FUNCTIONS ------------------------------- */
	
	function MySqlConnection()
	{
	
		$DBServer = DB_SERVER;
		$DBUserName = DB_USERNAME;
		$DBPassword = DB_PASSWORD;
		$DatabaseName = DB_DATABASE;
	
		$db = mysql_connect($DBServer,$DBUserName,$DBPassword,66536) or die(mysql_error());	
		mysql_select_db($DatabaseName) or die(mysql_error());
		return $db;
	}
	
 
	
	
	function MySqlExecute($sql,$arrayType=0)
	{
		$conn = MySqlConnection();
		$cmd = mysql_query($sql) or die($sql . "<BR>" . mysql_error());
		$count = mysql_affected_rows();	
		$returnArray = array();
		if($count > 0)
		{
			if($arrayType == 0)
			{
			//$returnArray = mysql_fetch_array($cmd, MYSQL_ASSOC);
				while ($row = mysql_fetch_array($cmd, MYSQL_ASSOC)) 
				{
					$returnArray[] = $row; 
				}
			}
			else
			{
				while ($row = mysql_fetch_array($cmd, MYSQL_NUM)) 
				{
					$returnArray[] = $row; 
				}
			}
		}		
		mysql_close($conn);
		return $returnArray ;		
	}
	
	function MySqlUpdate($sql)
	{
		$conn = MySqlConnection();
		$cmd = mysql_query($sql) or die($sql . "<BR>" . mysql_error());
		mysql_close($conn);
	}
	
	function MySqlIdentity($sql,$table)
	{
		$conn = MySqlConnection();
		$cmd = mysql_query($sql) or die($sql . "<BR>" . mysql_error());
		
		$sql = "select LAST_INSERT_ID() from " . $table;
		$cmd = mysql_query($sql) or die($sql . "<BR>" . mysql_error());
		$row = mysql_fetch_array($cmd, MYSQL_NUM);
		$rVal = $row[0];
		mysql_close($conn);	
		return $rVal;
	}
	
	function CleanSqlText($value)
	{
		$conn = MySqlConnection();
		if(isset($value))
		{
			if(is_numeric(str_replace(",","",$value)))
			{
				return str_replace(",","",$value);
			}
			if($value != "" && $value != "null")
			{
			   // Stripslashes
			   if (get_magic_quotes_gpc()) {
				   $value = stripslashes($value);
			   }
			   // Quote if not a number or a numeric string
			   if (!is_numeric($value)) {
				   $value = "'" . mysql_real_escape_string($value) . "'";
			   }
			   	mysql_close($conn);	
		   		return $value;
			}
			else
			{
				mysql_close($conn);	
				return 'null';
			}
	   }
	   else
	   {
	   		mysql_close($conn);	
	   		return 'null';
	   }
	}
	
	function CleanSqlDateText($value)
	{
		if(isset($value))
		{
			
			if($value != "" && $value != "null")
			{
			   
		   		return "'" . date('Y-m-d', strtotime($value)) . "'";
			}else
			   {
					return 'null';
			   }
			
	   }
	   else
	   {
	   		return 'null';
	   }	
	
		
	}
	
 
function exportMysqlToCsv($table,$filename = 'export.csv')
{
    $csv_terminated = "\n";
    $csv_separator = ",";
    $csv_enclosed = '"';
    $csv_escaped = "\\";
    $sql_query = $table;
 
    // Gets the data from the database
    
    $result = mysql_query($sql_query);
    $fields_cnt = mysql_num_fields($result);
 
 
    $schema_insert = '';
 
    for ($i = 0; $i < $fields_cnt; $i++)
    {
        $l = $csv_enclosed . str_replace($csv_enclosed, $csv_escaped . $csv_enclosed,
            stripslashes(mysql_field_name($result, $i))) . $csv_enclosed;
        $schema_insert .= $l;
        $schema_insert .= $csv_separator;
    } // end for
 
    $out = trim(substr($schema_insert, 0, -1));
    $out .= $csv_terminated;
 
    // Format the data
    while ($row = mysql_fetch_array($result))
    {
        $schema_insert = '';
        for ($j = 0; $j < $fields_cnt; $j++)
        {
            if ($row[$j] == '0' || $row[$j] != '')
            {
 
                if ($csv_enclosed == '')
                {
                    $schema_insert .= $row[$j];
                } else
                {
                    $schema_insert .= $csv_enclosed . 
					str_replace($csv_enclosed, $csv_escaped . $csv_enclosed, $row[$j]) . $csv_enclosed;
                }
            } else
            {
                $schema_insert .= '';
            }
 
            if ($j < $fields_cnt - 1)
            {
                $schema_insert .= $csv_separator;
            }
        } // end for
 
        $out .= $schema_insert;
        $out .= $csv_terminated;
    } // end while
 
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Content-Length: " . strlen($out));
    // Output to browser with appropriate mime type, you choose ;)
    header("Content-type: text/x-csv");
    //header("Content-type: text/csv");
    //header("Content-type: application/csv");
    header("Content-Disposition: attachment; filename=$filename");
    echo $out;
    exit;
 
}
	
?>