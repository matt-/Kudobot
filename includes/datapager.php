<?



	$offset = 0;
	$rowsPerPage_ = 0;
	function BuildPager($rowsPerPage,$pageNum, $numrows)
	{
		$existingVars = "?x=x";
		if(isset($_GET))
		{
			
			foreach($_REQUEST as $key => $value)
			{
				if($key != "page" && $key != "rowsPerPage" && $key != "mode" && $key != "x" && $key != "stindex")
				{
					if(!isset($_COOKIE[$key])){	
					$existingVars .= "&" . $key . "=" . $value;
					}
				}
			}
		}
		
		
			if(isset($_REQUEST["status"]) )
			{
				$existingVars .= "&";
					$statuses = $_REQUEST["status"];
							for($i=0; $i < count($statuses); $i++)
							{						
								$existingVars .= "status[]=" . $statuses[$i] . "&";
							}
				$existingVars = substr($existingVars,0,strlen($existingVars)-1) ;
				
			}		
		
		
		global $offset,$rowsPerPage_ ,$pageMode;
		$rowsPerPage_  = $rowsPerPage;
		// counting the offset
		$offset = ($pageNum - 1) * $rowsPerPage;	
		
		// how many pages we have when using paging?
		$maxPage = ceil($numrows/$rowsPerPage);		
		$self = $_SERVER['PHP_SELF'];
		

		// creating 'previous' and 'next' link
		// plus 'first page' and 'last page' link
		
		// print 'previous' link only if we're not
		// on page one
		if ($pageNum > 1)
		{
			$page = $pageNum - 1;
			$prev = " <a href=\"$self". $existingVars . "&page=$page&rowsPerPage=$rowsPerPage&mode=$pageMode\"  style='float:left;'>&lt; Previous</a> ";
			$first = "";
			
			//$first = " <a href=\"$self?page=1\">[First Page]</a> ";
		} 
		else
		{
			$prev  = '<div  style="float:left;">&lt; Previous</div>';       // we're on page one, don't enable 'previous' link
			//$first = ' [First Page] '; // nor 'first page' link
			$first = "";
		}
		
		// print 'next' link only if we're not
		// on the last page
		if ($pageNum < $maxPage)
		{
			$page = $pageNum + 1;
			
			$next = " <a href=\"$self". $existingVars . "&page=$page&rowsPerPage=$rowsPerPage&mode=$pageMode\"     style='float:right;'>Next &gt;</a> ";
			
			//$last = " <a href=\"$self?page=$maxPage\">[Last Page]</a> ";
			$last = "";
		} 
		else
		{
			$next = '<div style="float:right;">Next &gt; </div>';      // we're on the last page, don't enable 'next' link
			//$last = ' [Last Page] '; // nor 'last page' link
			$last = "";
		}
		return $first . $prev .  $next . $last;	
		//return $first . $prev . " <strong>Page: $pageNum </strong> of <strong>$maxPage</strong>  " . $next . $last;	
	}
?>