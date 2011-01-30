<?php 
	require_once("includes/kudo.class.php"); 
	$_REQUEST["rekudo"] = isset($_REQUEST["rekudo"]) ? $_REQUEST["rekudo"] : "";
	$k = new kudo();
	$k->check_login();
	$result = "";
        $rekudo_user_id = "";
        $rekudo_user_name = "";
        $rekudo_avatar = "";
        $rekudo_msg = "";
	if(($_REQUEST["rekudo"] != ""))
        {
            $user = $k->get_rekudo($_REQUEST["rekudo"]);
            $rekudo_user_id = $user["recip_user_id"];
            if($rekudo_user_id == kudo::get_loggedin_user())
            {
                echo "Sorry, as much as you'd like to, you can not send yourself a kudo.";
                exit; 
            }
            $rekudo_user_name = $user["recip_first_name"] . " " . $user["recip_last_name"];
            $rekudo_avatar = "<img src='/images/users/" . $user["recip_avatar"] . "'/>";
            $rekudo_msg = "RK  @" . $user["sender_first_name"] . " to @" . $user["recip_first_name"]  . ": For " . $user["reason"];
        }
	if($_POST)
	{
                $thread_id = isset($_REQUEST["rekudo"]) ? $_REQUEST["rekudo"] : "";
		$k->do_kudo($_POST["selected_user"],$_POST["kudos"],$thread_id);
		header("Location: kudos.php?sent=1");
		exit;
	}
?>


<?php require("includes/header.inc.php") ?>
<?php if(!isset($_REQUEST["sent"])) { ?>

	<h1>KudoBot, please send kudos to:</h1>
	<form method="post" action="<?= $_SERVER["PHP_SELF"] ?>">
	<input type="hidden" name="selected_user" id="selected_user" value="<?= $rekudo_user_id ?>">
        <input type="hidden" name="rekudo" value="<?= GetFormValue("rekudo") ?>"/>
	<table cellpadding="4">
		<tr>
			<td>Enter Name:</td>
			<td>
				<input type="text" id="alias" name="alias_parameter" style="width:270px;" value="<?= $rekudo_user_name ?>" onclick="this.value='';"/>
				<div id="alias_choices" class="autocomplete" style="background-color:#000;color:#fff;padding:4px;"></div>	
				<script>

					new Ajax.Autocompleter("alias", "alias_choices", "ajx_get_users.php", {minChars:2});

				</script>
			</td>
		</tr>
		<tr>
			<td colspan="2"><hr style="color:#cc250d;" noshade=1 size="1"/></td>
		</tr>
		<tr>
			<td>
				Kudos for:
			</td>
			<td>
				<textarea name="kudos" cols="30" rows="5" style="width:270px;"><?= $rekudo_msg ?></textarea>
			</td>
		</tr>
		<tr>
			<td></td>
			<td><input type="image" vspace="2" src="images/go.png" onmouseover="this.src='images/go1.png'" onmouseout="this.src='images/go.png'"/></td>
		</tr>
	</table>
	</form><!-- <embed src="mr_roboto.mid" height=4 width=4 hidden=true> </embed> -->
<? } 
	else 
	{
		?>
		<div align="center">
		<h1>KudoBot has deivered your kudos.</h1>
		<h1 style='color:#cc250d;'>Long Live KudoBot!</h1>
		<br/>
		<a href="kudos.php"><img src="images/more.png" border="0"/></a>
		</div>
		<?
		
	}
?>
        <? if($rekudo_avatar != "") { ?>
        <script>
            	$('selected').innerHTML = "<?= $rekudo_avatar ?>";
	$('selected').style.visibility = "visible";
            </script>
        <? } ?>
<?php require("includes/footer.inc.php") ?>