<!DOCTYPE HTML>

<head>
<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
<title><?php

include_once("secrets.php");
$db = new MySQLi($sqlhost, $sqluser, $sqlpass, $sqldb);
$id = $_GET["ideology"];
$result = $db->query("select ideology from ideology where id=$id");
if ($error = $db->error) {
 echo "Oh noez! No ideology corresponds to id=$id!";
}
else {
 $row = $result->fetch_assoc();
 if ($row) {
  $ideology = $row['ideology'];
  echo $ideology;
 }
}

?></title>
<link rel="stylesheet" type="text/css" href="ais3.css" />
</head>

<body>
<h3><a href=".">home</a></h3>
<?php

echo <<<SPIEL
<p class="text">These two lists reflect the level of agreement or disagreement between <b>$ideology</b> and other ideologies.
This level is calculated as follows:
The responses &ldquo;strongly disagree,&rdquo; &ldquo;disagree,&rdquo; &ldquo;neutral or no opinion,&rdquo; &ldquo;agree,&rdquo; and &ldquo;strongly agree&rdquo;
are represented by the numbers -2, -1, 0, 1 and 2, respectively.
When the responses of two respondents to a survey item are multipled,
the result will be positive if the two responses are on the same side of neutral,
negative if on opposite sides of neutral,
and zero if either response is neutral.
The numbers thus obtained for each survey item are added together
into a rough measure of agreement or disagreement,
as the case may be,
between two respondents.
Following the link for one of the compared ideologies
will give a side-by-side comparison of the two responses for each survey item.</p>
SPIEL
;
echo "<!-- ideology #$id -->\n";
include_once("ais.php");
$survey = new survey($db);
echo "<div class=\"friends\">\n";
echo "<h2>Likely allies:</h2>\n";
$survey->friends_and_foes($id, "friend");
echo "</div>\n<div class=\"foes\">\n";
echo "<h2>Likely rivals:</h2>\n";
$survey->friends_and_foes($id, "foe");
echo "</div>\n";
?>


</body>

</html>

