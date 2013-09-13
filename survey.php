<!DOCTYPE HTML>
 <head>
  <meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
  <title>AIS survey form</title>
  <link rel="stylesheet" type="text/css" href="ais3.css" />
 </head>
 <body>
  <form action="tabulate.php" method="post" name="survey">
   <fieldset>
    <legend><span id="heading">Take the survey</span><br />
     <span id="guide">Choose a name for your school of thought, or ideology.
     Please do not include any personal identifying information in this name.
     <b>Understand that your responses to this survey, while anonymous, will be released into the public domain!</b>
     If this is not OK with you, please do not continue.</span>
    </legend>
    <label for="ideology">I call my system of thought</label>
    <input type="text" name="ideology" id="ideology" autofocus required />
   </fieldset>
   <fieldset id="questions">
    <?php 

     require_once("secrets.php");
     $db = new MySQLi($sqlhost, $sqluser, $sqlpass, $sqldb);
     require_once("ais.php");
     $survey = new survey($db);
     echo "<!-- new survey created -->\n";
     $survey -> generate_questionnaire();

    ?>
   </fieldset>
   <fieldset id="enter">
    <script type="text/javascript" src="http://www.google.com/recaptcha/api/challenge?k=6LelGMgSAAAAALGrw3q6biIBsXpLO4lDRIxqC1by"></script>
    <noscript>
     <iframe src="http://www.google.com/recaptcha/api/noscript?k=6LelGMgSAAAAALGrw3q6biIBsXpLO4lDRIxqC1by" height="300" width="500">
     </iframe><br/>
     <textarea name="recaptcha_challenge_field" rows="3" cols="40"></textarea>
     <input type="hidden" name="recaptcha_response_field" value="manual_challenge"/>
    </noscript>
    <input type="submit" value="Enter responses" />
   </fieldset>
  </form>
 </body>
</html>

