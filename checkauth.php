<?php

function get_http_response_code($domain) {
  $headers = get_headers($domain);
  return substr($headers[0], 9, 3);
}
require 'PHPMailer/PHPMailerAutoload.php';
$dir = "/etc/nginx/sites-available";
$vhost_path = "";
if (is_dir($dir)){
  if ($dh = opendir($dir)){
    while (($file = readdir($dh)) !== false){
      $file_content = file_get_contents($dir."/".$file);
      if(strpos('server_name',$file_content)===false) {
        $str_pos1 = stripos($file_content, 'root');
        $root_location = substr($file_content, $str_pos1, 100);
        $exp = explode(";", $root_location);
        $server_root = $exp[0];
        $exp_root = explode(" ", $server_root);
        $root_path = trim($exp_root[1]) . "/";

        $str_pos = stripos($file_content, 'server_name');
        $getstr = substr($file_content, $str_pos, 300);
        $server_domainexp = explode(" ",$getstr);
        $serverdomain = explode(";", $server_domainexp[1]);
        $domain = $serverdomain[0];
        if($domain) {
          $domain1 = "http://".$domain."/";
          $get_http_response_code = get_http_response_code($domain1);
          if ( $get_http_response_code == 401 ) {
            echo "has http auth!.";
          } elseif($get_http_response_code == 200) {
            $vhost_path .= $root_path. "<br />";
          }
        }

      }
    }
    closedir($dh);
  }
}

if($vhost_path) {
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
  $mail->Subject = 'Remainder about Vhost authentication';
  $mail->Body = '<b>Http authentication is disable in </b>'.$vhost_path;
  $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
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