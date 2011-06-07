<?php require_once('../Connections/conn_shortener.php'); ?>
<?php
//initialize the session
if (!isset($_SESSION)) {
  session_start();
}

// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
  $logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
  //to fully log out a visitor we need to clear the session varialbles
  $_SESSION['MM_Username'] = NULL;
  $_SESSION['MM_UserGroup'] = NULL;
  $_SESSION['PrevUrl'] = NULL;
  unset($_SESSION['MM_Username']);
  unset($_SESSION['MM_UserGroup']);
  unset($_SESSION['PrevUrl']);
	
  $logoutGoTo = "index.php";
  if ($logoutGoTo) {
    header("Location: $logoutGoTo");
    exit;
  }
}
?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "";
$MM_donotCheckaccess = "true";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($UserGroup, $arrGroups)) { 
      $isValid = true; 
    } 
    if (($strUsers == "") && true) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "index.php";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0) 
  $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
}
?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "frmEdit")) {
  $insertSQL = sprintf("INSERT INTO tblshorturls (FURL, FEnabled) VALUES (%s, %s)",
                       GetSQLValueString("http://".$_POST['FURL'], "text"),
                       GetSQLValueString(isset($_POST['FEnabled']) ? "true" : "", "defined","1","0"));

  mysql_select_db($database_conn_shortener, $conn_shortener);
  $Result1 = mysql_query($insertSQL, $conn_shortener) or die(mysql_error());
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>URL Shortener</title>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	$("#divForm").hide();
	$("#btnCancel").attr("disabled", "disabled");
	
	$("#btnAdd").click(function() {
		$("#divForm").slideDown();
			$("#btnCancel").attr("disabled", "");
			$("#btnAdd").attr("disabled", "disabled");
	});
	
	$("#btnCancel").click(function() {
		$("#divForm").slideUp();
			$("#btnCancel").attr("disabled", "disabled");
			$("#btnAdd").attr("disabled", "");			
	});	
	
	$.ajax({
		url:	"List.php",
		data:	"FURL=%",
		success: function(msg) {
			$("#divResult").html(msg);
		}
	});	
	
	$("#Search").bind("change keyup", function() {
		$.ajax({
			url:	"List.php",
			data:	"FURL=" + $(this).val(),
			success: function(msg) {
				$("#divResult").html(msg);
			}
		});
	});
});
</script>
</head>

<body>
<div>
Search: <input type="text" id="Search" name="Search" />
</div>
<div id="divResult">

</div>
<div>
<input type="button" id="btnAdd" name="btnAdd" value="Add New" />&nbsp; <input type="button" id="btnCancel" name="btnCancel" value="Cancel" />
</div>
<div id="divForm">
<form method="POST" action="<?php echo $editFormAction; ?>" id="frmEdit" name="frmEdit">
<p><label>ID: <input type="text" id="ID" name="ID" disabled="disabled" size="10" /></label></p>
<p><label>URL: http://<input type="text" id="FURL" name="FURL" size="100" /></label></p>
<p><label>Enabled: <input type="checkbox" id="FEnabled" name="FEnabled" checked="checked" /></label></p>
<p><input type="submit" id="submit" name="submit" value="Submit" /></p>
<input type="hidden" name="MM_insert" value="frmEdit" />
</form>
</div>
<p><a href="<?php echo $logoutAction ?>">Log out</a></p>
</body>
</html>