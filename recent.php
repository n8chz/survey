<!DOCTYPE HTML>
<head>
<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
<title>Recently added ideologies</title>
<link rel="stylesheet" type="text/css" href="ais3.css" />
</head>
<body>
<h3><a href=".">home</a></h3>
<h2>Recently added ideologies:</h2>
<?php

include_once("secrets.php");
$db = new MySQLi($sqlhost, $sqluser, $sqlpass, $sqldb);
include_once("ais.php");
$survey = new survey($db);
$survey->recent(35);

?>
</body>
</html>

