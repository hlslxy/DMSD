<?php
// Send an email to the administrator
$to = "xyliu@fhi-berlin.mpg.de";         // 邮件接收者
$subject = "[The diatomic database] User contribution";                // 邮件标题
$contributor_username = "hlslxy";
$table_content = "<table><tr></tr></table>";
$message = "Please check the user contributions from ".$contributor_username.":https://vwebfile.gwdg.de/phpmyadmin/index.php?db=rios&table=molecule_data and confirm via ";  // 邮件正文
$from = "xyliu@fhi-berlin.mpg.de";   // 邮件发送者
$headers = "From:" . $from;         // 头部信息设置
mail($to,$subject,$message,$headers);

// Information to the contributor
echo "Thanks for your contribution! An email has been sent to the website administrator. You will be informed by email after your contribution has been confirmed.";
?>
