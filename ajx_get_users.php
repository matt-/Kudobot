<?php
	require_once("includes/kudo.class.php");
	$k = new kudo();
	$users = $k->find_users($_POST["alias_parameter"]);
	
	if(count($users) > 0)
	{
		echo "<ul class='contacts'>";
			foreach($users as $u)
			{
				$img = $u["avatar"];
				if($img == "")
				{
					$img = "icnProfileSm.png";
				}
				echo "<li class='contact' id='" . $u["user_id"] . "'><img src='images/users/" . $img . "' align='absmiddle' style='padding-right:4px;' id='u_image_" . $u["user_id"]. "'/>" . $u["first_name"] . " " . $u["last_name"] . "</li>";
			}
		echo "</ul>";
	}
?>