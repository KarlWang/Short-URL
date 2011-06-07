<?php require_once('../Connections/conn_shortener.php'); ?>
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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "frmModify")) {
  $updateSQL = sprintf("UPDATE tblshorturls SET FURL=%s, FEnabled=%s WHERE FKeyID=%s",
                       GetSQLValueString($_POST['FURL'], "text"),
                       GetSQLValueString(isset($_POST['FEnabled']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString($_POST['FKeyID'], "int"));

  mysql_select_db($database_conn_shortener, $conn_shortener);
  $Result1 = mysql_query($updateSQL, $conn_shortener) or die(mysql_error());

  $updateGoTo = "Shortener.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$colname_Recordset1 = "-1";
if (isset($_GET['FKeyID'])) {
  $colname_Recordset1 = $_GET['FKeyID'];
}
mysql_select_db($database_conn_shortener, $conn_shortener);
$query_Recordset1 = sprintf("SELECT * FROM tblshorturls WHERE FKeyID = %s", GetSQLValueString($colname_Recordset1, "int"));
$Recordset1 = mysql_query($query_Recordset1, $conn_shortener) or die(mysql_error());
$row_Recordset1 = mysql_fetch_assoc($Recordset1);
$totalRows_Recordset1 = mysql_num_rows($Recordset1);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Edit - URL Shortener</title>
</head>

<body>
<div id="divForm">
<form method="POST" action="<?php echo $editFormAction; ?>" id="frmModify" name="frmModify">
<p><label>ID: <input type="text" id="ID" name="ID" disabled="disabled" size="10" value="<?php echo $row_Recordset1['FKeyID']; ?>" /></label>
<input type="hidden" id="FKeyID" name="FKeyID" value="<?php echo $row_Recordset1['FKeyID']; ?>" />
</p>
<p><label>URL: http://<input name="FURL" type="text" id="FURL" value="<?php echo $row_Recordset1['FURL']; ?>" size="100" />
</label></p>
<p><label>Enabled: <input <?php if (!(strcmp($row_Recordset1['FEnabled'],1))) {echo "checked=\"checked\"";} ?> type="checkbox" id="FEnabled" name="FEnabled" /></label></p>
<p><input type="submit" id="submit" name="submit" value="Submit" />&nbsp;<input type="button" value="Cancel" onclick="history.go(-1)" /></p>
<input type="hidden" name="MM_update" value="frmModify" />
</form>
</div>
</body>
</html>
<?php
mysql_free_result($Recordset1);
?>
