<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_MyConn = "localhost";
$database_MyConn = "test";
$username_MyConn = "root";
$password_MyConn = "";
$MyConn = mysql_pconnect($hostname_MyConn, $username_MyConn, $password_MyConn) or trigger_error(mysql_error(),E_USER_ERROR);

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
?>
<?php
$colname_rsShortURL = "-1";
if (isset($_GET['ID'])) {
  $colname_rsShortURL = $_GET['ID'];
}
mysql_select_db($database_MyConn, $MyConn);
$query_rsShortURL = sprintf("SELECT * FROM tblShortURLs WHERE FKeyID = %s", GetSQLValueString($colname_rsShortURL, "int"));
$rsShortURL = mysql_query($query_rsShortURL, $MyConn) or die(mysql_error());
$row_rsShortURL = mysql_fetch_assoc($rsShortURL);
$totalRows_rsShortURL = mysql_num_rows($rsShortURL);

if ($totalRows_rsShortURL > 0){
	header("Location:".$row_rsShortURL["FURL"]);
}
else{
	header("Location:http://blog.karlwang.com");
}
?>