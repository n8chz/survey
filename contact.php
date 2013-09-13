<html>
<head>

<?php



define("sql_error_reporting",true);

$email=$_POST['email'];
$mensaje=$_POST['mensaje'];
$headers = "Reply to: " . $email. "\r\n";
$headers .= "From: " . $email;
mail("n8chz@yahoo.ca","Feedback on the Agnostic Ideology Sorter",$mensaje,$headers,"-f $email -r $email");




?>

<meta HTTP-EQUIV="REFRESH" content="0; url=index.html" />

</head>
</html>

