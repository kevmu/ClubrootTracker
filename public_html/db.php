<?php
//echo "hello_world\n";
//$con = mysqli_connect("localhost","root","","register");
//$con = pg_connect("host=localhost port=5432 dbname=login user=postgres password=password");

$host = "localhost";
$dbname = "clubroot_tracker_login";
$user = "postgres";
$password = "password";
    
$con = pg_connect("host=$host dbname=$dbname user=$user password=$password");

// Check connection
$result = pg_get_result($con);

//echo $result;

if ($result){
  echo "Failed to connect to postgresql: " . pg_result_error_field($result, PGSQL_DIAG_SQLSTATE);
}else{
  //echo "Connection to postgresql successful!\n$con";
}
?>

