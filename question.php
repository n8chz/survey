<!DOCTYPE HTML>
<head>
<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
<?php

$q = $_GET["q"];
echo "<title>Analysis of question #$q</title>\n";

?>
<link rel="stylesheet" type="text/css" href="ais3.css" />
</head>
<body>
<h3><a href=".">home</a></h3>
<?php
include_once("secrets.php");
$db = new MySQLi($sqlhost, $sqluser, $sqlpass, $sqldb);
include_once("ais.php");
$survey = new survey($db);
$question = $survey->question_text($q);
echo "<p>Proposition:</p>\n";
echo "<h2>$question</h2>\n";
$survey->bar_graph($q);
$survey->correlate_question($q);

?>
</body>
</html>

