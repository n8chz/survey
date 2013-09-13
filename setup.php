<!DOCTYPE HTML>
<head>
<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
<title>Construct a Likert-scale survey</title>
<link rel="stylesheet" type="text/css" href="ais3.css" />
</head>
<body>
<h1>Construct a <a href="http://en.wikipedia.org/wiki/Likert_scale" target="blank">Likert-scale</a> survey</h1>
<form id="setup" action="edit.php" method="post">

<fieldset>
<legend>Enter MySQL server credentials:</legend>
<label for="username">username</label>
<input type="text" name="username" autofocus />
<label for="password">password</label>
<input type="password" name="password" />
<label for="dbname">database name</label>
<input type="text" name="dbname" />
</fieldset>

<fieldset>
<legend>Enter <a href="http://code.google.com/apis/recaptcha/docs/php.html">ReCAPTCHA keys</a></legend>
<label for="public">public key</label>
<input type="password" name="public" />
<label for="private">private key</label>
<input type="password" name="private" />
</fieldset>

<input type="submit" value="Press here to edit questionnaire" />

</form>

</body>
</html>

