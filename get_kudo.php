<?php
	require_once("includes/kudo.class.php"); 
	$k = new kudo();
	$k->check_login($_SERVER["PHP_SELF"] . "?id=" . $_REQUEST["id"]);
	
	if(isset($_REQUEST["id"]))
	{
		$kudos = $k->get_kudo($_REQUEST["id"]);
		if(count($kudos) == 0)
		{
			echo "Page Not Found";
			exit;
		}
		$kudo = $kudos[0];
	}
	
	
?>

<?php require("includes/header.inc.php") ?>
<!-- <embed src="mr_roboto.mid" height=4 width=4 hidden=true> </embed> -->

<div align="center">
	<div style="width:340px;height:350px;overflow:auto;">
<h1>Hi. I am KudoBot.</h1>
<table cellpadding="4">
	<tr valign="top">
		<td>
			<?php
			$img = $kudo["sender_avatar"];
			if($img == "")
			{
				$img = "icnProfileSm.png";
			}
			
			?>
			<img src="images/users/<?= $img ?>" align="absmiddle"/>
		</td>
		<td align="left"><span style="font-size:18px;">
			<?= $kudo["sender_first_name"]?> <?= $kudo["sender_last_name"]?> asked that I deliver kudos for:
			</span>

		</td>
	</tr>
</table>
<br/><br/><center>
<?= nl2br($kudo["reason"]) ?><br/>
<br/>
<h1 style="color:#cc250d;">Nice Work!</h1>
<br/><a href="kudos.php"><img src="images/send_kudos.png" border="0"/></a>
</center>
</div>
</div>
<?php require("includes/footer.inc.php") ?>