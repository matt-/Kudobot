 
<?php
require_once("includes/kudo.class.php");
require_once("includes/datapager.php");
$_REQUEST["mode"] = isset($_REQUEST["mode"]) ? $_REQUEST["mode"] : "";
$k = new kudo();

	if (!kudo::is_admin())
	{
		echo "Access Denied";
		exit;
	} 
	if($_REQUEST["mode"] == "export_kudos")
	{
		$conn = MySqlConnection();
		exportMysqlToCsv("select k.kudo_id,DATE_FORMAT(k.created_at,'%m/%d/%Y') as created_at,
					u1.first_name as sender_first_name,
					u1.last_name as sender_last_name,
					u2.first_name as recip_first_name,
					u2.last_name as recip_last_name,
					u1.avatar as sender_avatar ,
					k.reason 
			 from kudos k
						inner join users u1 on u1.user_id = k.from_id
						inner join users u2 on u2.user_id = k.user_id");
		mysql_close($conn);
		exit;
	}

	
	
?>
<h1>KudoBot-min</h1>


<h3>Manage Kudos</h3>
<?
	if($_REQUEST["mode"] == "delete_kudo")
	{
	 
		$k->delete_kudo($_REQUEST["kudo_id"]);
	}
	

	
	$filter = "";
	if(isset($_POST["kudo_search"]))
	{
		$filter = " where (u1.first_name like '%" . $_POST["kudo_search"] . "%' or u1.last_name like '%" . $_POST["kudo_search"] . "%'
		 	or u2.first_name like '%" . $_POST["kudo_search"] . "%' or u2.last_name like '%" . $_POST["kudo_search"] . "%' or 
			k.reason like '%" . $_POST["kudo_search"] . "%')";
	
	}
	$kudos = $k->get_all_kudos($filter);	
	if(count($kudos) > 0)
	{

		$totalcount = count($kudos);
		$rpp = 25;
		if(isset($_REQUEST["rowsPerPage"])){$rpp = $_REQUEST["rowsPerPage"];}
		
		$page = 1;if(isset($_REQUEST["page"])){$page = $_REQUEST["page"];}
		$pagerStr = BuildPager($rpp, $page, count($kudos));
		$limit = " LIMIT $offset, $rowsPerPage_";
		$kudos = $k->get_all_kudos($filter,$limit);
		
?>
<form method="post" action="<?= $_SERVER["PHP_SELF"] ?>?mode=editkudos">
	Search: <input type="text" name="kudo_search"> <input type="submit" value="Go"/>
	
	&nbsp;<input type="button" value="Export All" onclick="location.href='admin.php?mode=export_kudos'">
	<br/><br/>
	<table cellpadding="4" border="1" cellspacing="0">
		<tr style="font-weight:bold;font-size:12px;">
			<td>Delete?</td><td>From</td><td>To</td><td>Reason</td><td>Created</td>
			<tr>
			<? 
			$i = 0;
			foreach($kudos as $kudo) 
				{
					?>
					<tr style="font-weight:normal;font-size:12px;">
							<td><a href="admin.php?mode=delete_kudo&kudo_id=<?= $kudo["kudo_id"] ?>" onclick="return confirm('Are you sure?')">Delete</a></td>
						<td><?= $kudo["sender_first_name"]?> <?= $kudo["sender_last_name"]?></td>
						<td><?= $kudo["recip_first_name"]?> <?= $kudo["recip_last_name"]?></td>
						<td><?= $kudo["reason"]?></td>
						<td><?= $kudo["created_at"]?></td>
					</tr>
					<?
					$i++;
				}
			?>
		<tr>
			<td colspan="5" style="font-weight:normal;font-size:12px;"><?= $pagerStr ?></td>
		</tr>
	</table>
	<input type="hidden" name="reccount" value="<?= $i ?>"/>
	<br/>
 
	</form>
	
<? 	}?>
<hr/>


<h3>Add User</h3>
<?php
// -- ADD USER -----
if($_REQUEST["mode"] == "adduser")
{
	try
	{
		$newid = $k->add_user($_POST["first_name"],$_POST["last_name"],$_POST["user_name"],$_POST["password"],$_POST["email"],$_POST["admin"]);
		if($_FILES["photo"]["name"] != ""){
		$k->change_photo($newid);
		
		
		}
		echo "USER ADDED<br/>";
	}catch(Exception $er)
	{
		echo "ERROR: " . $er->getMessage();
		exit;
	}
}

?>
<form method="post" action="<?= $_SERVER["PHP_SELF"] ?>?mode=adduser" enctype="multipart/form-data">
<table>
	<tr>
		<td>First Name:</td>
		<td><input type="text" name="first_name"></td>
	</tr>
	<tr>
		<td>Last Name:</td>
		<td><input type="text" name="last_name"></td>
	</tr>
	<tr>
		<td>Username:</td>
		<td><input type="text" name="user_name"></td>
	</tr>
	<tr>
		<td>Password:</td>
		<td><input type="password" name="password"></td>
	</tr>
	<tr>
		<td>Email:</td>
		<td><input type="email" name="email"></td>
	</tr>
	<tr>	<td> Photo:</td>
		<td><input type="file" name="photo" class="file"></td>
	</tr>
	<tr>
		<td>Admin:</td>
		<td><select name="admin"><option value="0">No</option><option value="1">Yes</option></td>
	</tr>
	<tr><td><input type="submit" value="Save"/></td></tr>
</table>
</form>
<hr/>

<h3>Edit Users</h3>
<form method="post" action="<?= $_SERVER["PHP_SELF"] ?>?mode=edituser" enctype="multipart/form-data">
	<?php
		if($_REQUEST["mode"] == "edituser")
		{
			$ct = $_POST["reccount"];
			for($i=0;$i < $ct; $i++)
			{
				$a = isset($_POST["a_" . $i]) ? 1 : 0;
				$delete = isset($_POST["del_" . $i]) ?  true : false;
				if($delete)
				{
					$k->delete_user($_POST["uid_" . $i]);
					
				}else {
			 	$k->update_user($_POST["uid_" . $i],
					$_POST["f_" . $i],
					$_POST["l_" . $i],
					$_POST["p_" . $i],
					$_POST["e_" . $i],
					$a);
					
					if($_FILES["photo_" . $i]["name"] != ""){
						$k->change_photo($_POST["uid_" . $i],"photo_" . $i);
					}
				}
			}
			echo "Users Updated";
		}
	
	
		$users = $k->get_users();
		if(count($users) > 0) 
		{
		
	?>
	<table cellpadding="4">
		<tr style="font-weight:bold;font-size:12px;">
		<td>Delete?</td>	<td></td><td>Username</td><td>First Name</td><td>Last Name</td><td>Password</td><td>Email</td><td>Admin</td><td>Photo</td>
		</tr>
		<? 
		$i = 0;
		foreach($users as $u) {?>
		<tr>
			<td>
				<input type="hidden" name="uid_<?= $i ?>" value="<?= $u["user_id"] ?>">
				<input type="checkbox" name="del_<?= $i ?>"></td>
			<td>
				<?php
				
				$img = $u["avatar"];
				if($img == "")
				{
					$img = "icnProfileSm.png";
				}
				?>
				<img src="images/users/<?= $img ?>"/>
				</td>
			<td><?= $u["user_name"]?></td>
			<td><input type="text" name="f_<?= $i ?>" value="<?= $u["first_name"]?>" style="width:90px;"/></td>
			<td><input type="text" name="l_<?= $i ?>" value="<?= $u["last_name"]?>" style="width:90px;"/></td>
			
			<td><input type="password" name="p_<?= $i ?>" value="<?= $u["password"]?>" style="width:90px;"/></td>
			<td><input type="text" name="e_<?= $i ?>" value="<?= $u["email"]?>" style="width:90px;"/></td>
			<td><input type="checkbox" name="a_<?= $i ?>"<? if($u["admin"]){ echo " checked"; }?>/></td>
			<td><input type="file" name="photo_<?= $i ?>" size="20"></td>
		</tr>
		<?
			$i++;
		 } ?>
	</table><input type="hidden" name="reccount" value="<?= $i ?>"/>
	<input type="submit" value="Save"/>
	<? } ?>
</form>
<hr/>

<h3>Stats</h3>
<table><tr valign=top><td>
<?php
	$stats = $k->get_stats_by_month_received();
	if(count($stats) > 0)
	{
		?>
			<strong>Kudos Given</strong>
			<table border=1>
				<tr>
					<td>User</td><td>Count</td><td>Month</td>
				</tr>
				<? foreach($stats as $s) { ?>
					<tr>
						<td><?= $s["first_name"]?> <?= $s["last_name"]?></td><td><?= $s["kcount"]?></td><td><?= $s["m"]?>/<?= $s["y"]?></td>
					</tr>
				
				<? }?>
			</table>
		<?
	}
	
	
?>
</td>
<td>&nbsp;</td>
<td>
	<?php
		$stats = $k->get_stats_by_month_given();
		if(count($stats) > 0)
		{
			?>
				<strong>Kudos Received</strong>
				<table border=1>
					<tr>
						<td>User</td><td>Count</td><td>Month</td>
					</tr>
					<? foreach($stats as $s) { ?>
						<tr>
							<td><?= $s["first_name"]?> <?= $s["last_name"]?></td><td><?= $s["kcount"]?></td><td><?= $s["m"]?>/<?= $s["y"]?></td>
						</tr>

					<? }?>
				</table>
			<?
		}


	?>
	
	</td></tr></table>









