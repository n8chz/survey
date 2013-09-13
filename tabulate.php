<!DOCTYPE HTML>
<head>

<?php


require_once('secrets.php');
require_once('recaptchalib.php'); # reCAPTCHA server-side code from http://code.google.com/apis/recaptcha/docs/php.html
$resp = recaptcha_check_answer ($privatekey,
                                $_SERVER["REMOTE_ADDR"],
                                $_POST["recaptcha_challenge_field"],
                                $_POST["recaptcha_response_field"]);
echo "<!-- response obtained from reCAPTCHA -->\n";

if (!$resp->is_valid) {
 // What happens when the CAPTCHA was entered incorrectly
 //die ("The reCAPTCHA wasn't entered correctly. Go back and try it again." .
 //     "(reCAPTCHA said: " . $resp->error . ")");
 echo "</head><body><form>
       <h3>reCAPTCHA phrase not correct!</h3>
       <input type=\"button\" value=\"Back to Previous Page\"
       onClick=\"javascript: history.go(-1)\">
       </form></body>"; #h/t http://www.web-source.net/javascript_back.htm
} else {
    // Your code here to handle a successful verification

$db = new MySQLi($sqlhost, $sqluser, $sqlpass, $sqldb);
echo "<!-- Database on board -->\n";
require_once("ais.php");
$survey = new survey($db);
echo "<!-- Survey created -->\n";
$survey -> tabulate($_POST);
echo "<!-- Survey tabulated -->\n";
$survey -> spool_data_file();


shell_exec('chmod 0755 mkidmap.py');
shell_exec('./mkidmap.py');
shell_exec('chmod 0311 mkidmap.py');

echo "</head>\n";

}

?>

</html>
