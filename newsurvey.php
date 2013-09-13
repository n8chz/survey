<!DOCTYPE HTML>
<head>
<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
<title>Construct a Likert-scale survey</title>
<link rel="stylesheet" type="text/css" href="ais3.css" />
</head>
<body>
<?php

echo "<!-- \n\n";
print_r($_POST);
echo "\n-->\n";


# post variables passed to this file:
# spiel, delsection[], delquestion[][], question[][], section[]

include_once("secrets.php");
$db = new MySQLi($sqlhost, $sqluser, $sqlpass, $sqldb);
include_once("ais.php");
$survey = new survey($db);

if (isset($_POST["spiel"])) {
 $charcount = file_put_contents("spiel.txt", $_POST["spiel"]);
 echo "<!-- $charcount characters in new spiel! -->\n";
 chmod("spiel.txt", 0600);
}

if (isset($_POST["delsection"])) {
 $survey->del_sections($_POST["delsection"]);
}

if (isset($_POST["question"])) {
 $survey->add_questions($_POST["question"]);
}

if (isset($_POST["delquestion"])) {
 $survey->del_questions($_POST["delquestion"]);
}

if (isset($_POST["section"])) {
 $survey->add_sections($_POST["section"]);
}

echo "<p>Changes made to database.<br /><a href=\".\">HOME</a></p>\n";

?>
</body>
</html>

