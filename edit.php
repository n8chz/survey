<!DOCTYPE HTML>
<head>
<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
<title>Construct a Likert-scale survey</title>
<link rel="stylesheet" type="text/css" href="ais3.css" />
</head>
<body>
<h1>Construct a <a href="http://en.wikipedia.org/wiki/Likert_scale" target="blank">Likert-scale</a> survey</h1>
<form id="setup" action="newsurvey.php" method="post">

<fieldset>
<label for="spiel">Type in an introductory description of the survey you are creating.</label><br />
<textarea id="spiel" name="spiel" cols="80" rows="24"></textarea>
</fieldset>

<fieldset id="sections">
<legend>Each <em>section</em> is a group of propositions under a common heading.</legend>
<fieldset class="existing-sections">
<?php

  error_reporting(E_ALL); 
//error_reporting(E_ALL & ~E_NOTICE | E_STRICT); // Warns on good coding standards
  ini_set("display_errors", "1");
// h/t http://www.cs.trincoll.edu/hfoss/wiki/How_to_display_errors_in_PHP

$sqluser = $_POST["username"];
$sqlpass = $_POST["password"];
$sqldb = $_POST["dbname"];
$privatekey = $_POST["private"];
$publickey = $_POST["public"];

/*
echo "<pre>\n";
$f = fopen("/testlib.php", "r");
echo file_get_contents($f);
fclose($f);
echo "</pre>\n";
*/

$secrets = fopen("./secrets.php", "w");
# $foo = preg_replace("/(.*\/).*$/", "$1secrets.php", $_SERVER["SCRIPT_FILENAME"]);
# echo "<!-- $foo -->\n";
# $secrets = fopen(preg_replace("/(.*\/).*$/", "$1secrets.php", $_SERVER["SCRIPT_FILENAME"]), "w");
fwrite($secrets, "<?php\n");
fwrite($secrets, "\$sqlhost=\"localhost\";\n");
fwrite($secrets, "\$sqluser=\"$sqluser\";\n");
fwrite($secrets, "\$sqlpass=\"$sqlpass\";\n");
fwrite($secrets, "\$sqldb=\"$sqldb\";\n");
fwrite($secrets, "\$privatekey=\"$privatekey\";\n");
fwrite($secrets, "\$publickey=\"$publickey\";\n");
fwrite($secrets, "?>\n");
fclose($secrets);
chmod("secrets.php", 0600);

$db = new MySQLi("localhost", $sqluser, $sqlpass);
echo "<!-- database attached -->\n";
$db->query("create database if not exists $sqldb");
echo "<!-- ".$db->error." -->\n";
$db->query("use $sqldb");
echo "<!-- ".$db->error." -->\n";
include_once("ais.php");
echo "<!-- ais.php included -->\n";
$survey = new survey($db);
echo "<!-- survey object created -->\n";
$survey -> disp_existing_sections();

?>
</fieldset>
</fieldset>
<fieldset>
<button id="addsec" type="button" onclick="addSection();">Add section</button>
</fieldset>
<fieldset>
<input type="submit" id="submit" value="Save changes" />
</fieldset>
</form>
<script type="text/JavaScript">

/*

@licstart  The following is the entire license notice for the JavaScript code in this page.

    Copyright (C) 2013  Lorraine Lee

    The JavaScript code in this page is free software: you can
    redistribute it and/or modify it under the terms of the GNU
    General Public License (GNU GPL) as published by the Free Software
    Foundation, either version 3 of the License, or (at your option)
    any later version.  The code is distributed WITHOUT ANY WARRANTY;
    without even the implied warranty of MERCHANTABILITY or FITNESS
    FOR A PARTICULAR PURPOSE.  See the GNU GPL for more details.

    As additional permission under GNU GPL version 3 section 7, you
    may distribute non-source (e.g., minimized or compacted) forms of
    that code without the copy of the GNU GPL normally required by
    section 4, provided you include this license notice and a URL
    through which recipients can access the Corresponding Source.

@licend  The above is the entire license notice for the JavaScript code in this page.


*/

function clickFunctionGenerator(fsId) {
 return function () {
  var qId = (new Date()).getTime(); /* h/t http://stackoverflow.com/ */
  var newLabel = document.createElement("label");
  newLabel.setAttribute("for", qId);
  newLabel.setAttribute("class", "question-label");
  newLabel.textContent = "proposition: ";
  qfs = document.getElementById("qb"+fsId);
  qfs.appendChild(newLabel);
  var newQuestion = document.createElement("input");
  newQuestion.setAttribute("id", qId);
  newQuestion.setAttribute("class", "question-input");
  newQuestion.setAttribute("type", "text");
  newQuestion.setAttribute("name", "question["+fsId+"]["+qId+"]");
  qfs.appendChild(newQuestion);
 };
}

function addSection() {
 /* create fieldset to hold new section */
 var sectionFieldSet = document.createElement("fieldset");
 var newLabel = document.createElement("label");
 var uniq = (new Date()).getTime(); /* h/t http://stackoverflow.com/a/3231532/1269964 */
 newLabel.setAttribute("for", uniq);
 newLabel.setAttribute("class", "section-label");
 newLabel.textContent = "Section description: ";
 sectionFieldSet.appendChild(newLabel);
 var newInput = document.createElement("input");
 newInput.setAttribute("id", uniq);
 newInput.setAttribute("type", "text");
 newInput.setAttribute("name", "section["+uniq+"]");
 newInput.setAttribute("class", "section-input");
 sectionFieldSet.appendChild(newInput);
 var questionFieldSet = document.createElement("fieldset");
 var qfsId = "qb"+uniq;
 questionFieldSet.setAttribute("id", qfsId);
 sectionFieldSet.appendChild(questionFieldSet);
 var addQuestionButton = document.createElement("button");
 addQuestionButton.setAttribute("type", "button");
 addQuestionButton.textContent = "Add new proposition to this section";
 addQuestionButton.onclick = clickFunctionGenerator(uniq);
 sectionFieldSet.appendChild(addQuestionButton);
 document.getElementById("sections").appendChild(sectionFieldSet);
}

questionButtons = document.getElementsByClassName("new-q-old-s");
for (var k=0; k<questionButtons.length; k++) {
 otherClass = questionButtons[k].className;
 otherClass = otherClass.replace("new-q-old-s", "").trim();
 // questionButtons[k].setAttribute("style", "background:pink");
 questionButtons[k].onclick = clickFunctionGenerator(otherClass);
}

</script>
</body>
</html>

