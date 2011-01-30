<?php
	require_once("includes/kudo.class.php"); 
	$k = new kudo();
	$k->check_login($_SERVER["PHP_SELF"]);
	$msg = "";

	
	if($_POST)
	{
		try
		{
			//$k->change_user_name($_POST["user_name"]);
			$k->change_password($_POST["password"]);

			$msg = "<span style='color:green;'><strong>KudoBot says: </strong>Your account has been changed</span>";
		}
		catch(Exception $er)
		{
			$msg = "<span class='error'><strong>KudoBot says: </strong>" . $er->getMessage() . "</span>";
		}
		

	}
	
	$user = $k->get_user();
	$u = $user[0];

?>
<?php require("includes/header.inc.php") ?>
<h1>My Account</h1>

<?= $msg ?>
<form method="post" action="<?= $_SERVER["PHP_SELF"] ?>">
<table>
	<!-- ><tr>
		<td>Change Username:</td>
		<td><input type="text" name="user_name" value="<?= GetFormValueWithDefault($u["user_name"],"user_name") ?>"/></td>
	</tr> -->
	<tr>
		<td>Change Password:</td>
		<td><input type="password" name="password" value="<?= GetFormValueWithDefault($u["password"],"password") ?>"/></td>
	</tr>	

	
	<tr>	<td>

		</td>
			<td>

			</td></tr>
	<tr>
		<td>
			
		</td>
		<td><input type="image" src="images/save.png"/></td>
		</tr>
</table>
</form>

<?php require("includes/footer.inc.php") ?>