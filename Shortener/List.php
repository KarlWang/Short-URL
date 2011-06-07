<?php require_once('../Connections/conn_shortener.php'); ?>
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

$colname_rsMaster = "-1";
if (isset($_GET['FURL'])) {
  $colname_rsMaster = $_GET['FURL'];
}
mysql_select_db($database_conn_shortener, $conn_shortener);
$query_rsMaster = sprintf("SELECT * FROM tblshorturls WHERE FURL LIKE %s", GetSQLValueString("%" . $colname_rsMaster . "%", "text")." ORDER BY FEnabled DESC");
$rsMaster = mysql_query($query_rsMaster, $conn_shortener) or die(mysql_error());
$row_rsMaster = mysql_fetch_assoc($rsMaster);
$totalRows_rsMaster = mysql_num_rows($rsMaster);


function highlightWords($string, $word)
{
	$string = str_ireplace($word, '<span class="highlight_word">'.$word.'</span>', $string);
	return $string;
}
?>
<style type="text/css">
.highlight_word{
	background-color: yellow;
}
</style>
<script type="text/javascript">
$(document).ready(function() {

});
</script>
<table border="1" cellpadding="2" cellspacing="2" width="60%">
  <thead>
    <tr><th width="1%">ID</th><th>URL</th><th width="1%" nowrap="nowrap">Shortened URL</th><th width="1%">Enabled</th><th width="1%" nowrap="nowrap">Modify</th></tr>
  </thead>
  <tbody>  
<?php do { ?>
    <tr>
      <td><?php echo $row_rsMaster['FKeyID']; ?></td>
      <td><a href="http://<?php echo $row_rsMaster['FURL']; ?>" target="_blank">http://<?php echo highlightWords($row_rsMaster['FURL'], $_GET["FURL"]); ?></a></td>
      <td nowrap="nowrap"><a href="http://karlwang.com/<?php echo $row_rsMaster['FKeyID']; ?>" target="_blank">http://karlwang.com/<?php echo $row_rsMaster['FKeyID']; ?></a>|COPY</td>      
      <td><?php echo $row_rsMaster['FEnabled']; ?></td>
      <td>
      	<a href="Edit.php?FKeyID=<?php echo $row_rsMaster['FKeyID']; ?>">Modify</a>            
      </td></tr>
<?php } while ($row_rsMaster = mysql_fetch_assoc($rsMaster)); ?>
  </tbody>
</table>
<?php
mysql_free_result($rsMaster);
?>