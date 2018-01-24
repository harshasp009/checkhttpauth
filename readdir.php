<?php
require 'PHPMailer/PHPMailerAutoload.php';
$dir = "/etc/nginx/sites-available";
$vhost = "";
if (is_dir($dir)){
  if ($dh = opendir($dir)){
    while (($file = readdir($dh)) !== false){
      $file_content = file_get_contents($dir."/".$file);
      if(strpos('root',$file_content)===false) {
        $str_pos = stripos($file_content, 'root');
         $getstr = substr($file_content, $str_pos, 500);
         $exp = explode(";", $getstr);
         $server_root = $exp[0];
         $exp_root = explode(" ", $server_root);
         $root_path = trim($exp_root[1]) . "/";
        $content = 'User-agent: *' . "\n";
        $content .= 'Disallow: /';
        if(file_exists($root_path.'robots.txt')){
           $root_content = file_get_contents($root_path . 'robots.txt');
           $file_path = $root_path.'robots.txt';
          if(exec('grep '.escapeshellarg('User-agent: *').' '.$file_path)) {
                break;
          }else {
            $vhost .= $root_path.",";
            file_put_contents($root_path . 'robots.txt', "\n".$content.PHP_EOL , FILE_APPEND);
          }
        } else {
          $handle = fopen($root_path . 'robots.txt', 'w');
          fwrite($handle, $content);
          fclose($handle);
        }
      }
    }
    closedir($dh);
  }
}

if($vhost) {
  $mail = new PHPMailer;
  $mail->isSMTP();                                      // Set mailer to use SMTP
  $mail->SMTPDebug = 0;
  $mail->Host = 'smtp.gmail.com';                       // Specify main and backup server
  $mail->SMTPAuth = TRUE;                               // Enable SMTP authentication
  $mail->Username = 'sp.sriharsha@redcrackle.com';                   // SMTP username
  $mail->Password = 'Harsha#123';               // SMTP password
  $mail->SMTPSecure = 'ssl';                            // Enable encryption, 'ssl' also accepted
  $mail->Port = 465;                                    //Set the SMTP port number - 587 for authenticated TLS
  $mail->setFrom('sp.sriharsha@redcrackle.com', 's p sriharsha');     //Set who the message is to be sent from
  $mail->addAddress('vishal.khialani@redcrackle.com');  // Add a recipient
  $mail->WordWrap = 50;                                 // Set word wrap to 50 characters
  $mail->isHTML(TRUE);                                  // Set email format to HTML
  $mail->Subject = 'Remainder about Vhost Robots.txt File';
  $mail->Body = '<b>Robots.txt file was not present in  </b>'.$vhost.'and has been updated';
  $mail->AltBody = 'Robots.txt file was not present in'.$vhost;
  if (!$mail->send()) {
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
    return FALSE;
    //exit;
  }
  else {
    return TRUE;

  }
}
?>