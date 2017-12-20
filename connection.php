<?php
# MYSQLI CONNECTION
$con = new mysqli("localhost","root","","throne");
if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }
?>
