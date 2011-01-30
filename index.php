<?php  
require_once("includes/kudo.class.php");
$errmsg = "";

if (!isset($_REQUEST["logout"])) {
  if(isset( $_COOKIE["ecuser"])) {
    if(GetFormValue("rekudo") == "")
    {
        header("Location: kudos.php");
    }
    else
    {
        header("Location: kudos.php?rekudo=" . GetFormValue("rekudo"));
    }
    exit;
  }
}

	if($_POST)
	{
		$k = new kudo();
		try
		{
			$k->login($_POST["username"], $_POST["password"]);
			if(isset($_COOKIE["return_to"]))
			{
				$goto = $_COOKIE["return_to"];
				setcookie("return_to", "", time()-3600);
				header("Location: " . $goto);
				
				exit;
			}
                        if(GetFormValue("rekudo") == "")
                        {
                            header("Location: kudos.php");
                        }
                        else
                        {
                            header("Location: kudos.php?rekudo=" . GetFormValue("rekudo"));
                        }
                        exit;
			
		} catch(Exception $er)
		{
			$errmsg = "<span class='error'>" . $er->getMessage() . "</span><br/>";
		}
	}

?>

<?php require("includes/header.inc.php") ?>

	<br/><br/> 
	<?
		if (isset($_REQUEST["logout"]))
		{
			setcookie("user", NULL, time()-3600);
			setcookie("ecuser", NULL, time()-3600);
			$errmsg = "<span class='error'>You have been logged out.</span><br/>";
		}
		
	?>
		<form method="post" action="<?= $_SERVER["PHP_SELF"] ?>">
                    <input type="hidden" name="rekudo" value="<?= GetFormValue("rekudo") ?>"/>
				<?= $errmsg ?>
			<table>
				<tr>
					<td>Username:</td>
					<td><input type="text" name="username" style="width:200px;"></td>
					</tr>
					<tr>
						<td>Password:</td>
						<td><input type="password" name="password" style="width:200px;"></td>
						</tr>
					<tr>
						<td></td>
						<td><input type="image" vspace="2" src="images/go.png" onmouseover="this.src='images/go1.png'" onmouseout="this.src='images/go.png'"/></td>
					</tr>
			</table>
		</form>
<?php require("includes/footer.inc.php") ?>