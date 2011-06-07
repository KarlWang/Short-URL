<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_conn_shortener = "localhost";
$database_conn_shortener = "test";
$username_conn_shortener = "root";
$password_conn_shortener = "";
$conn_shortener = mysql_pconnect($hostname_conn_shortener, $username_conn_shortener, $password_conn_shortener) or trigger_error(mysql_error(),E_USER_ERROR); 
?>