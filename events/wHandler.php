<?php
//insert.php
session_start();
$connect = new PDO("mysql:host=localhost;dbname=mediaio", "root", "umvHVAZ%");

if(isset($_POST["wEvent"]))
{
 if(!isset($_POST['wComment'])){$wComment="";}else{$wComment=$_POST['wComment'];}
 $query = "
 INSERT INTO worksheet 
 (FullName, EventID, Worktype, Location, Comment) 
 VALUES (:fullname, :eventid, :worktype, :location, :comment)
 ";
 $statement = $connect->prepare($query);
 $result = $statement->execute(
  array(
   ':fullname' => $_SESSION['lastName']." ".$_SESSION['firstName'],
   ':eventid'  => $_POST['wEvent'],
   ':worktype' => $_POST['wType'],
   ':location' => $_POST['wLoc'],
   ':comment' => $wComment
  )
 );

 if($result){
     echo "1";
 }
 else{
     echo "2";
 }
}


?>