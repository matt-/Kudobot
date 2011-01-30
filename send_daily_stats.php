<?php

// $RunasProcess = "Y";
// 
// if ($RunasProcess =="Y") 
// {
//  declare(ticks=1);
//  $pid = pcntl_fork();
//  if ($pid == -1) 
//  {
//      die("could not fork");
//  } else if ($pid) 
//  {
//      exit(); // we are the parent
//  } 
//  else
//   {
//  // we are the child
//  }
//  // detatch from the controlling terminal
//  if (posix_setsid() == -1) {
//      die("could not detach from terminal");
//  }
//  $posid=posix_getpid();
// 
// 
//  $fp = fopen("/var/run/process-kudos.pid", "w");
//  fwrite($fp, $posid);
//  fclose($fp);
//  // setup signal handlers
//   pcntl_signal(SIGTERM, "sig_handler");
//   pcntl_signal(SIGHUP, "sig_handler");
//  // loop forever performing tasks
// }
// 
// function sig_handler($signo) {
// global $fpLog;
//     switch ($signo) {
//      case SIGTERM:
//          // handle shutdown tasks
//         $stardt = "---------- End Time: ".date("m.d.y H:i:s")." --------------- \n";
//         fwrite($fpLog, $stardt); 
//         fclose($fpLog);
//         exit(0);
//          #exit;
//          break;
//      case SIGHUP:
//          // handle restart tasks
//          break;
//      default:
//          // handle all other signals
//      }
// }
// 
// while(1)
// {
// 
// 
//  if(date("H") == "9" || date("H") == "09")
//  {
		require_once("/var/www/vhosts/kudobots.com/httpdocs/includes/kudo.class.php");
	
		$k = new kudo();
		$k->send_daily_stats();
    // }
    // 
    // sleep(3600*2); // -go to sleep till hour has passed.
///}
?>
