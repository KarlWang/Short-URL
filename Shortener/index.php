<?php require_once('../Connections/conn_shortener.php'); ?>
<?php
// *** Validate request to login to this site.
if (!isset($_SESSION)) {
  session_start();
}

$loginFormAction = $_SERVER['PHP_SELF'];
if (isset($_GET['accesscheck'])) {
  $_SESSION['PrevUrl'] = $_GET['accesscheck'];
}

if (isset($_POST['Username'])) {
  $loginUsername=$_POST['Username'];
  $password=$_POST['Password'];
  $MM_fldUserAuthorization = "";
  $MM_redirectLoginSuccess = "Shortener.php";
  $MM_redirectLoginFailed = "index.php";
  $MM_redirecttoReferrer = false;

  if (("user" == $loginUsername) && ("1234" == $password)) {
     $loginStrGroup = "";
    
	if (PHP_VERSION >= 5.1) {session_regenerate_id(true);} else {session_regenerate_id();}
    //declare two session variables and assign them
    $_SESSION['MM_Username'] = $loginUsername;
    $_SESSION['MM_UserGroup'] = $loginStrGroup;	      

    if (isset($_SESSION['PrevUrl']) && false) {
      $MM_redirectLoginSuccess = $_SESSION['PrevUrl'];	
    }
    header("Location: " . $MM_redirectLoginSuccess );
  }
  else {
    header("Location: ". $MM_redirectLoginFailed );
  }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Log in - URL Shortener</title>
</head>

<body>
<form ACTION="<?php echo $loginFormAction; ?>" METHOD="POST" id="frmLogin" name="frmLogin">
<p><label>Username: <input type="text" id="Username" name="Username" /></label></p>
<p><label>Password: <input type="password" id="Password" name="Password" /></label></p>
<p><input type="submit" id="submit" name="submit" value="Submit" /></p>
</form>
</body>
</html>