<!DOCTYPE HTML>
<head>
<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
<?php

$errmess = "Erroneous information in GET parameters!";
$id1 = $_GET["id1"];
$id2 = $_GET["id2"];
if ($id1 > $id2) {
 $holder = $id1;
 $id1 = $id2;
 $id2 = $holder;
}
echo "<title>";
if ($id1 && $id2 && $id1 != $id2) {
 include_once("secrets.php");
 $db = new MySQLi($sqlhost, $sqluser, $sqlpass, $sqldb);
 include_once("ais.php");
 $survey = new survey($db);
 $ideology1 = $survey->ideology_name($id1);
 $ideology2 = $survey->ideology_name($id2);
 echo ($ideology1 && $ideology2) ? "Comparing \"$ideology1\" and \"$ideology2\"" : $errmess;
}
else {
 echo "$errmess\n";
}
echo "</title>\n";

?>
<link rel="stylesheet" type="text/css" href="ais3.css" />
</head>
<body>
<h3><a href=".">home</a></h3>
<?php

$survey->compare($id1, $id2);

?>
</body>
</html>

