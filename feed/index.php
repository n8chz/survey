<?php
# header_remove();
header("Content-Type: application/rss+xml");
?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
<channel>
<title>AIS Ideology Sorter</title>
<description>A public opinion survey</description>

<?php


$url = "http://{$_SERVER['HTTP_HOST']}".preg_replace("/\/feed.*$/", "/", $_SERVER["REQUEST_URI"]);
echo "<atom:link href=\"{$url}feed/\" rel=\"self\" type=\"application/rss+xml\" />\n";
echo "<link>$url</link>\n";

include_once("../secrets.php");
$db = new MySQLi($sqlhost, $sqluser, $sqlpass, $sqldb);
include_once("../ais.php");
$survey = new survey($db);
$survey->feed_items(25);

?>


</channel>
</rss>

