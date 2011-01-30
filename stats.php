<?php require("includes/kudo.class.php");

	$k = new kudo();
	$k->check_login($_SERVER["PHP_SELF"]);
	$msg = "";
	
	
?>


<?php require("includes/header.inc.php") ?>

	
	<?php
	if($_REQUEST["mode"] == "given")
	{
		$stats = $k->get_stats('given');
		if(count($stats) > 0)
		{
			?>
			<h1>Kudos Given (<?= $stats[0]["ttl"] ?>)</h1>
			<div style="width:360px;height:300px;overflow:auto;">
			<table border="0" cellspacing="3">
				<tr style='font-weight:bold;'><td>User</td><td>Count</td></tr>
				<? foreach($stats as $s) { ?>
					<tr>
						<td><?= $s["first_name"] ?> <?= $s["last_name"] ?></td>
						<td style='text-align:center'><?= $s["kcount"] ?></td>
				<? } ?>
			
			</table>
			<br/><br/>
			
			<?php
					$hist = $k->get_user_given_history(kudo::get_loggedin_user());
					
				
			?>
			<table cellpadding='3'>
				<tr style='font-size:10px;'>
					<td><strong>To</strong></td><td><strong>Kudo</strong></td><td><strong>Date</strong></td>
				</tr>
				<?
				foreach($hist as $h) {
				?>
				<tr valign='top' style='font-size:9px;'>
					<td><?= $h["recip_first_name"]?> <?= $h["recip_last_name"]?></td>
					<td><?= nl2br($h["reason"]) ?></td>
					<td><?=  $h["created_at"] ?></td>
				</tr>
				<? } ?>
			</table>
			
			</div>
			<?
			
		}
		else
		{
			echo "<h1>You have not given kudos to anyone yet.</h1>
				Send kudos <a href='kudos.php'>here</a>.
			";
		}
	}
	
	
	
	if($_REQUEST["mode"] == "received")
	{
		$stats = $k->get_stats('received');
		if(count($stats) > 0)
		{
			?>
			<h1>Kudos Received (<?= $stats[0]["ttl"] ?>)</h1>
			<div style="width:360px;height:300px;overflow:auto;">
			<table border="0" cellspacing="3">
				<tr style='font-weight:bold;'><td>User</td><td>Count</td></tr>
				<? foreach($stats as $s) { ?>
					<tr>
						<td><?= $s["first_name"] ?> <?= $s["last_name"] ?></td>
						<td style='text-align:center'><?= $s["kcount"] ?></td>
				<? } ?>
			
			</table><br/><br/>
			
			<?php
					$hist = $k->get_user_recieved_history($_COOKIE["user"]);
					
				
			?>
			<table cellpadding='3'>
				<tr style='font-size:10px;'>
					<td><strong>From</strong></td><td><strong>Kudo</strong></td><td><strong>Date</strong></td>
				</tr>
				<?
				foreach($hist as $h) {
				?>
				<tr valign='top' style='font-size:9px;'>
					<td><?= $h["sender_first_name"]?> <?= $h["sender_last_name"]?></td>
					<td><?= nl2br($h["reason"]) ?></td>
					<td><?= $h["created_at"]?></td>
				</tr>
				<? } ?>
			</table>
			
			
			
			
			</div>
			<?
			
		}
		else
		{
			echo "<h1>You have not received kudos yet.</h1>
				Send kudos <a href='kudos.php'>here</a>.
			";
		}
	}
	
	?>
	
<?php require("includes/footer.inc.php") ?>