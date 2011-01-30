<?php

require_once("config.php");
require_once("database.inc.php");
require_once("crypt.class.php");
require_once("php_mailer/class.phpmailer.php");

    class kudo
    {
            public function login($username, $password)
            {
                    $sql = "select * from users where user_name = " . CleanSqlText($username) . " and password = " . CleanSqlText($password);
                    $result = MySqlExecute($sql);
                    if(count($result) > 0)
                    {
                            $crypt = new proCrypt;
                            $encoded = $crypt->encrypt($result[0]["user_id"]);
                            setcookie("ecuser", $encoded, time()+(60*60*24*365));
                            return true;
                    }
                    else
                    {
                            throw new Exception("<strong>KudoBot says:</strong> \"Your log in does not compute.\"");
                    }

            }

            public static function get_loggedin_user()
            {
                    if(isset( $_COOKIE["ecuser"]))
                    {
                            $crypt = new proCrypt;
                            return trim($crypt->decrypt( $_COOKIE["ecuser"] ));
                    }
                    else
                    {
                            if (isset($_COOKIE["user"])) {
                              return $_COOKIE["user"];
                            }
                    }

            }

            public static function is_admin()
            {
                    $sql = "select * from users where admin = 1 and user_id = " . kudo::get_loggedin_user();
                    $result = MySqlExecute($sql);
                    if(count($result) > 0)
                    {
                            return true;
                    }
            }

            public function check_login($url="")
            {
                    if(!kudo::get_loggedin_user() != "")
                    {
                            if($url != "")
                            {
                                    setcookie("return_to", $url);
                            }
                            header("Location: index.php");
                            exit;
                    }
            }


            public function add_user($f,$l,$u,$p,$e,$a=0)
            {
                    $sql = "select * from users where user_name  = " . CleanSqlText($u);
                    $result = MySqlExecute($sql);
                    if(count($result) == 0)
                    {
                            $sql = "insert into users (first_name,last_name,user_name,password,admin,email) values (";
                            $sql .= CleanSqlText($f) . ", " . CleanSqlText($l) . "," . CleanSqlText($u) . "," . CleanSqlText($p) . ", " . $a . "," . CleanSqlText($e);
                            $sql .= ")";
                            $newid = MySqlIdentity($sql,"users");


                            return $newid;

                    }
                    else
                    {
                            throw new Exception("User already exists.");
                    }
            }


            public function update_user($user_id,$f,$l,$p,$e,$a=0)
            {
                    $sql = "update users set first_name = " . CleanSqlText($f) . ",last_name = " . CleanSqlText($l) . ", password = " . CleanSqlText($p) . ", email = " . CleanSqlText($e) . ",admin = " . $a
                     . " where user_id = " . $user_id
                    ;
                    MySqlUpdate($sql);
            }


            public function delete_user($user_id)
            {
                    $sql = "delete from users where user_id = " . $user_id;
                    MySqlUpdate($sql);

                    $sql = "delete from kudos where user_id = ". $user_id;
                    MySqlUpdate($sql);

                    $sql = "delete from kudos where from_id = ". $user_id;
                    MySqlUpdate($sql);
            }

            public function delete_kudo($kudo_id)
            {
                    $sql = "delete from kudos where kudo_id = " . $kudo_id;
                    MySqlUpdate($sql);
            }

            public function get_user_recieved_history($user)
            {
                    $sql = "select k.kudo_id,k.created_at,
                                    u1.first_name as sender_first_name,
                                    u1.last_name as sender_last_name,
                                    u2.first_name as recip_first_name,
                                    u2.last_name as recip_last_name,
                                    u1.avatar as sender_avatar ,
                                    k.reason
                     from kudos k
                                            inner join users u1 on u1.user_id = k.from_id
                                            inner join users u2 on u2.user_id = k.user_id

                                            where k.user_id = " . kudo::get_loggedin_user() . " order by k.kudo_id desc" ;
                    return MySqlExecute($sql);

            }


            public function get_user_given_history($user)
            {
                    $sql = "select k.kudo_id,DATE_FORMAT(k.created_at,'%m/%d/%Y') as created_at,
                                    u1.first_name as sender_first_name,
                                    u1.last_name as sender_last_name,
                                    u2.first_name as recip_first_name,
                                    u2.last_name as recip_last_name,
                                    u1.avatar as sender_avatar ,
                                    k.reason
                     from kudos k
                                            inner join users u1 on u1.user_id = k.from_id
                                            inner join users u2 on u2.user_id = k.user_id

                                            where k.from_id = " . kudo::get_loggedin_user() . " order by k.kudo_id desc" ;
                    return MySqlExecute($sql);

            }




            public function get_all_kudos($filter="", $limit="")
            {
                    $sql = "select k.kudo_id,DATE_FORMAT(k.created_at,'%m/%d/%Y') as created_at,
                                    u1.first_name as sender_first_name,
                                    u1.last_name as sender_last_name,
                                    u2.first_name as recip_first_name,
                                    u2.last_name as recip_last_name,
                                    u1.avatar as sender_avatar ,
                                    k.reason,
                                    u1.user_id as sender_user_id,
                                    u2.user_id as recip_user_id ,
                                    u2.avatar as recip_avatar
                     from kudos k
                                            inner join users u1 on u1.user_id = k.from_id
                                            inner join users u2 on u2.user_id = k.user_id ";

                    if($filter != "")
                    {
                            $sql .= " " . $filter;
                    }

                    $sql .= " order by k.kudo_id desc ";

                    if($limit != "")
                    {
                            $sql .= " " . $limit;
                    }
                                    $result = MySqlExecute($sql);
                                    return $result;
            }


            public function get_users()
            {
                    $sql = "select * from users order by user_name";
                    $result = MySqlExecute($sql);
                    return $result;
            }

            public function get_rekudo($thread_id)
            {
                $kudo = $this->get_all_kudos("where thread_id = '" . addslashes($thread_id) . "' ", $limit=" limit 1");
                $k = array();
                if(count($kudo) > 0)
                {
                    $k = $kudo[0];
                }
                return $k;
            }

            public function find_users($user_str)
            {
                    $sql = "select * from users where user_id <> " . kudo::get_loggedin_user() . " and (first_name like " . CleanSqlText("%" . $user_str . "%") . " or last_name like " . CleanSqlText("%" . $user_str . "%")
                                            . " or user_name like " . CleanSqlText("%" . $user_str . "%") . " or concat(first_name, ' ', last_name) like " . CleanSqlText("%" . $user_str . "%") . ");";

                    $result = MySqlExecute($sql);
                    return $result;
            }

            public function do_kudo($user_id,$reason,$thread_id="")
            {
                    $rekudo = 0;
                    if($thread_id != "")
                    {
                        $rekudo = 1;
                    }
                    if($rekudo == 1)
                    {
                        $sql = "insert into kudos (from_id, user_id, reason, created_at,thread_id,rekudo) values
                            (" . kudo::get_loggedin_user() . ", " . $user_id . ", " . CleanSqlText($reason) . ",NOW()," .
                                CleanSqlText($thread_id) . "," . CleanSqlText($rekudo) . ")";
                    }
                    else
                    {
                        $rand = uniqid();
                        $sql = "insert into kudos (from_id, user_id, reason, created_at,thread_id,rekudo) values
                            (" . kudo::get_loggedin_user() . ", " . $user_id . ", " . CleanSqlText($reason) . ",NOW(),'" . $rand . "',0)";
                    }
                    $newid = MySqlIdentity($sql,"kudos");

                    $sql = "select u1.first_name as sender_name,u1.email as sender_email,u2.first_name as recip_name,u2.email as recip_email from users u1 inner join users u2 on u2.user_id = " . $user_id . " where u1.user_id = " . kudo::get_loggedin_user() . ";";
                    $result = MySqlExecute($sql);

                    $mail             = new PHPMailer(); 

                    $mail->Body	= "KudoBot has kudos for you. To see your kudos, go to " . APP_DOMAIN . "/get_kudo.php?id=". $newid . "\n\n";
                    $mail->Body	.= "Long live KudoBot!";
                    $mail->From       = EMAIL_FROM;
                    $mail->FromName   = "KudoBot";
                    $mail->Subject    = "KudoBot has sent you kudos";
                    $mail->AddAddress($result[0]["recip_email"], $result[0]["recip_email"]);
                    if(!$mail->Send()) {
                      echo "Mailer Error: " . $mail->ErrorInfo;exit;
                    } 

            }


            /** FOR CRON **/
            public function send_daily_stats()
            {

                    $sql = "select k.from_id,u.first_name,u.last_name,
                    u.avatar,
                    u2.first_name as from_name,u2.last_name as from_last,u2.avatar as from_avatar,k.reason,DATE_FORMAT(k.created_at,'%M %D, %Y') as dDate,
                    k.thread_id
                     from kudos k
                                    inner join users u on u.user_id = k.user_id
                                    inner join users u2 on u2.user_id = k.from_id

                                    where DATEDIFF(DATE_FORMAT(k.created_at,'%Y-%m-%d') , DATE_FORMAT(NOW(),'%Y-%m-%d')) = -1 and sent = 0 

                                      order by k.rekudo,RAND() desc";
                    $result = MySqlExecute($sql);

                    if(count($result) > 0)
                    {
                            $msg = '
                                    <style>
                                            body {font-family:"Lucida Grande", Tahoma, Verdana, sans-serif;font-size:11px;background-color:#ffffff;}
                                            td {font-size:14px;}
                                    </style>
                            <body>
                            <center><img src="' . APP_DOMAIN . '/images/JibJabLogo.jpg" align="middle"/>
                            <h2>KudoBot daily kudos for ' . $result[0]["dDate"] . "</h2>
                            <a href='" . APP_DOMAIN . "/' style='color:#cc250d;'><strong>Domo Arigato KudoBot!!</strong></a>
                            <br/><br>
                            ";
                            $msg .= "
                                    ";
                            $msg .= "

                            <table border='0' cellspacing='0' cellpadding='3' style='border:1px dotted black;background-color:#ffffff;'>
                                            ";
                            $altmsg = 'KudoBot daily kudos for ' . $result[0]["dDate"] . "\n\n";


                                            foreach($result as $r)
                                            {
                                                    $altmsg .= $r["from_name"] . " " . $r["from_last"] . "\t sent kudos to \t" . $r["first_name"] . " " . $r["last_name"] . "\t";
                                                    $altmsg .= $r["reason"] . "\n";
                                                    $msg .= "<tr valign='middle'>";
                                                            $msg .= "<td style='width:50px;'><img src='" . APP_DOMAIN . "/images/users/" . $r["from_avatar"] . "'/></td>";
                                                            $msg .= "<td style='width:100px;'>Sent kudos to</td>";
                                                            $msg .= "<td style='width:50px;'><img src='" . APP_DOMAIN . "/images/users/" . $r["avatar"] . "'/></td>";
                                                            $msg .= "<td style='width:360px;'>" . nl2br($r["reason"]) . "</td>";
                                                            $msg .= "<td style='width:40px;'><a href='" . APP_DOMAIN . "?rekudo=" . $r["thread_id"] . "' target='_blank'><img src='" . APP_DOMAIN . "/images/re_kudo.gif' border='0' alt='Re-Kudo' title='Re-Kudo'/></a></td></tr>";

                                            }
                            $msg .="</table>

                                    </center>
                            </body>";



                            $sql = "select email from users  order by email;";
                            $emails = MySqlExecute($sql);
                            if(count($emails) > 0 )
                            {
                                    foreach($emails as $e)
                                    {
                                            $mail = new PHPMailer(); 
                                            $mail->MsgHTML($msg);
                                            $mail->AltBody = $altmsg;
                                            $mail->From       = EMAIL_FROM;
                                            $mail->FromName   = "KudoBot";
                                            $mail->Subject    = "KudoBot Daily Kudos";
                                            $mail->AddAddress($e["email"], $e["email"]);
                                            if(!$mail->Send()) {
                                              echo "Mailer Error: " . $mail->ErrorInfo; exit;
                                            } 
                                    }
                            }
                            $sql = "update kudos set sent = 1 where DATEDIFF(DATE_FORMAT(created_at,'%Y-%m-%d') , DATE_FORMAT(NOW(),'%Y-%m-%d')) = -1 and sent  = 0";
                            MySqlUpdate($sql);
                    }


            }


            public function get_kudo($id)
            {
                    $sql = "select
                                    u1.first_name as sender_first_name,
                                    u1.last_name as sender_last_name,
                                    u2.first_name as recip_first_name,
                                    u2.last_name as recip_last_name,
                                    u1.avatar as sender_avatar ,
                                    k.reason
                     from kudos k
                                            inner join users u1 on u1.user_id = k.from_id
                                            inner join users u2 on u2.user_id = k.user_id
                                            where k.kudo_id = " . CleanSqlText($id) . " and k.user_id = " . kudo::get_loggedin_user();
                                    $result = MySqlExecute($sql);


                    $sql = "update kudos set received_on = NOW() where kudo_id = " . CleanSqlText($id);
                    MySqlUpdate($sql);

                            return $result;


            }


            public function get_user()
            {
                    $sql = "select * from users where user_id = " . kudo::get_loggedin_user();
                    $result = MySqlExecute($sql);
                    return $result;

            }


            public function change_user_name($new_user_name)
            {
                    if(strlen($new_user_name) < 3)
                    {
                            throw new Exception("Username too short.");

                    }

                    $sql = "select * from users where user_name = " . CleanSqlText($new_user_name) . " and user_id <> " . kudo::get_loggedin_user();
                    $result = MySqlExecute($sql);
                    if(count($result) == 0)
                    {
                            $sql = "update users set user_name = " . CleanSqlText($new_user_name) . " where user_id = " . kudo::get_loggedin_user();
                            MySqlUpdate($sql);
                    }
                    else
                    {
                            throw new Exception("Username already exists.");
                    }
            }

            public function change_password($new_pass)
            {
                    $sql = "update users set password = " . CleanSqlText($new_pass) . "  where user_id = " . kudo::get_loggedin_user();
                    MySqlUpdate($sql);
            }


            public function change_photo($uid=0,$var="photo")
            {
                    $user_id = kudo::get_loggedin_user();
                    if($uid != 0) {$user_id = $uid;}
                    if((strpos(basename( $_FILES[$var]['name']),".jp") < 1 && strpos(basename( $_FILES[$var]['name']),".JP") < 1) && (strpos(basename( $_FILES[$var]['name']),".png") < 1 && strpos(basename( $_FILES[$var]['name']),".PNG") < 1))
                    {
                            throw new Exception("Please use JPG or PNG files.");
                            exit;
                    }
                    $dir = USER_IMAGES_DIR;
                    $target_path = $dir;
                    $ext =substr(strrchr($_FILES[$var]['name'],'.'),1);
                    $target_path = $target_path . basename( $_FILES[$var]['name']);


                    if(move_uploaded_file($_FILES[$var]['tmp_name'], $target_path)) {
                    $newfile = $dir . $user_id . "." . $ext;
                    try
                    {
                            $this->resize($target_path,$newfile,40,54,$ext);
                    }catch(Exception $er)
                    {
                            throw new Exception($er->getMessage());
                    }
                    unlink($target_path);

                    $sql = "update users set avatar = '" . $user_id . "." . $ext  . "'  where user_id = " . $user_id;
                    MySqlUpdate($sql);

                    } else{
                        throw new Exception("There was an error uploading the file, please try again!");
                    }

            }



            function resize_image($file, $newfile,$w, $h,$type, $crop=FALSE)
            {
                ini_set('display_errors', 1);
                list($width, $height) = getimagesize($file);
                $r = $width / $height;
                if ($crop) {
                    if ($width > $height) {
                        $width = ceil($width-($width*($r-$w/$h)));
                    } else {
                        $height = ceil($height-($height*($r-$w/$h)));
                    }
                    $newwidth = $w;
                    $newheight = $h;
                } else {
                    if ($w/$h > $r) {
                        $newwidth = $h*$r;
                        $newheight = $h;
                    } else {
                        $newheight = $w/$r;
                        $newwidth = $w;
                    }
                }
                    if(strtolower($type) == "jpg" || strtolower($type) == "jpeg")
                    {
                        $src = imagecreatefromjpeg($file);
                        $dst = imagecreatetruecolor($newwidth, $newheight);
                        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
                            imagejpeg($dst,$newfile.".jpg",100);
                    }
                    else
                    {

                            $image1 = imagecreatefrompng($file);
                            $image2=imagecreatefrompng($newfile);
                            imagecopyresampled($image2,$image1,0,0,0,0,$newwidth,$newheight,$width,$height);
                            imagesavealpha( $image2 ,true );
                            imagepng($image2,$newfile,9);
                            imagedestroy($image1);
                            imagedestroy($image2);
                    }

                ini_set('display_errors', 0);
            }

    #
            function resize($img, $newfilename,$w, $h, $type)
            {
              
             if (!extension_loaded('gd') && !extension_loaded('gd2')) {
              trigger_error("GD is not loaded", E_USER_WARNING);
              return false;
             }
             //Get Image size info
             $imgInfo = getimagesize($img);
             switch ($imgInfo[2]) {

              case 1: $im = imagecreatefromgif($img); break;

              case 2: $im = imagecreatefromjpeg($img);  break;

              case 3: $im = imagecreatefrompng($img); break;

              default:  trigger_error('Unsupported filetype!', E_USER_WARNING);  break;
            #
             }
            #

            #
             //If image dimension is smaller, do not resize
            #
             if ($imgInfo[0] <= $w && $imgInfo[1] <= $h) {
            #
              $nHeight = $imgInfo[1];
            #
              $nWidth = $imgInfo[0];
            #
             }else{
            #
                            //yeah, resize it, but keep it proportional
            #
              if ($w/$imgInfo[0] > $h/$imgInfo[1]) {
            #
               $nWidth = $w;
            #
               $nHeight = $imgInfo[1]*($w/$imgInfo[0]);
            #
              }else{
            #
               $nWidth = $imgInfo[0]*($h/$imgInfo[1]);
            #
               $nHeight = $h;
            #
              }
            #
             }
             $nWidth = round($nWidth);
            #
             $nHeight = round($nHeight);
            #

            #
             $newImg = imagecreatetruecolor($nWidth, $nHeight);
            #

            #
             /* Check if this image is PNG or GIF, then set if Transparent*/
            #
             if(($imgInfo[2] == 1) OR ($imgInfo[2]==3)){
            #
              imagealphablending($newImg, false);
            #
              imagesavealpha($newImg,true);
            #
              $transparent = imagecolorallocatealpha($newImg, 255, 255, 255, 127);
            #
              imagefilledrectangle($newImg, 0, 0, $nWidth, $nHeight, $transparent);
            #
             }
            #
             imagecopyresampled($newImg, $im, 0, 0, 0, 0, $nWidth, $nHeight, $imgInfo[0], $imgInfo[1]);
            #

            #
             //Generate the file, and rename it to $newfilename
            #
             switch ($imgInfo[2]) {
            #
              case 1: imagegif($newImg,$newfilename); break;
            #
              case 2: imagejpeg($newImg,$newfilename);  break;
            #
              case 3: imagepng($newImg,$newfilename); break;
            #
              default:  trigger_error('Failed resize image!', E_USER_WARNING);  break;
            #
             }
            #

            #
               return $newfilename;
            #
            }


            public function get_stats_by_month_received()
            {
                    $sql = "select k.from_id,count(kudo_id) as kcount,u.first_name,u.last_name,MONTH(k.created_at) as m,YEAR(k.created_at) as y

                     from kudos k
                                    inner join users u on u.user_id = k.from_id
                                     group by k.from_id,u.first_name,u.last_name,MONTH(k.created_at),YEAR(k.created_at) order by count(kudo_id) desc";
                    $result = MySqlExecute($sql);
                    return $result;
            }


            public function get_stats_by_month_given()
            {
                    $sql = "select count(*) as kcount,from_id,u.first_name,u.last_name,MONTH(k.created_at) as m,YEAR(k.created_at) as y from kudos k
                            inner join users u on u.user_id = k.user_id
                    group by k.user_id,u.first_name,u.last_name,MONTH(k.created_at),YEAR(k.created_at) order by count(*) desc";
                    $result = MySqlExecute($sql);
                    return $result;
            }

            public function get_stats($mode)
            {
                    if($mode == "given")
                    {
                            $sql = "select count(kudo_id) as kcount,u.first_name,u.last_name,(select count(*) from kudos where from_id = " . kudo::get_loggedin_user(). ") as ttl from kudos k
                                            inner join users u on u.user_id = k.user_id
                                            where k.from_id = " . kudo::get_loggedin_user() . " group by u.first_name,u.last_name order by count(kudo_id) desc";
                            $result = MySqlExecute($sql);
                    }


                    if($mode == "received")
                    {
                            $sql = "select count(kudo_id) as kcount,u.first_name,u.last_name,(select count(*) from kudos where user_id = " . kudo::get_loggedin_user(). ") as ttl from kudos k
                                            inner join users u on u.user_id = k.from_id
                                            where k.user_id = " . kudo::get_loggedin_user() . " group by u.first_name,u.last_name order by count(kudo_id) desc";
                            $result = MySqlExecute($sql);
                    }

                    return $result;
            }

    }




    function GetFormValue($name)
    {
            if(isset( $_REQUEST[$name]))
            {
                    return $_REQUEST[$name];
            }
            else
            {
                    return "";
            }
    }

    function GetFormValueWithDefault($default,$name)
    {
            if(isset( $_REQUEST[$name]))
            {
                    return $_REQUEST[$name];
            }
            else
            {
                    return $default;
            }
    }

?>
