<?php 
namespace Mediaio;
require "./Mediaio_autoload.php"; // Loads Database, Core and Mailer.


class takeOutManager{
  /*
  User takeout process. Stages the takeout as it still needs to be approved on userCheck panel.
  sets the item status to 2 (needs approvement.)
  */
  static function stageTakeout(){
    //Accesses post and Session Data.
  }

  //Usercheck approved takeout process. Acknowledges the takeout process and sets the item status to 1 (taken out)
  static function approveTakeout($value){
    /*  If not approved (value=false) - RETRIEVE CANNOT BE declined!  */
    /*  If approved (value=true) - RETRIEVE CANNOT BE declined!  */
    $userName = $_SESSION['UserUserName'];
    if(empty($userName)){
      return 400; // Session data is empty (e.g User is not loggged in.)
    }else{
      $data = json_decode(stripslashes($_POST['data']));
      $dataArray=array();
      foreach($data as $d){
      array_push($dataArray,$d);
    }
      //For Use in the SQL query.
      $dataString = "'" . implode ( "', '", $data ) . "'";

      //Restore Items allowing others to take it out.
      $sql="START TRANSACTION; UPDATE leltar SET leltar.Status=0 AND RentBy='".$userName."' WHERE leltar.Nev IN (".$dataString.");";
      //Acknowledge events in log.
      $sql.="UPDATE takelog SET Acknowledged=1 WHERE User='".$userName."' AND Date='".$_POST['date']."' AND EVENT='OUT' AND Item IN (".$dataString."); COMMIT;";

      $connection=Database::runQuery_mysqli();
      if(!$connection->multi_query($sql)){
        printf("Error message: %s\n", $connection->error);
      }else{
        //All good, return OK message
        //echo $sql;
        echo 200;
        return;
      }
    }

    /*  If approved (value=true)  */
  }

}

class retrieveManager{
  /*
  User takeout process. Stages the takeout as it still needs to be approved on userCheck panel.
  sets the item status to 2 (needs approvement.)
  */
  static function stageRetrieve(){
    //Accesses post and Session Data.
    //CHECK if sesison data is empty!
    $userName = $_SESSION['UserUserName'];
    if(empty($userName)){
      return 400; // Session data is empty (e.g User is not loggged in.)
    }

    $currDate= date("Y/m/d H:i:s");
    $data = json_decode(stripslashes($_POST['data']));
    $dataArray=array();
    $countOfRec=0;
    //New query - reduced to single query containing all items using the implode function;
    $countOfRec+=1;
    foreach($data as $d){
      array_push($dataArray,$d);
    }
    //For Use in SQL query.
    $dataString = "'" . implode ( "', '", $data ) . "'";
    // Database init  - create a mysqli obejct
      
      $connection=Database::runQuery_mysqli();
      $sql=" 
      START TRANSACTION; UPDATE leltar SET leltar.Status=2, leltar.RentBy=NULL WHERE leltar.Nev IN (".$dataString.");";
      $sql.="INSERT INTO takelog VALUES";
    foreach($data as $d){
      $sql.="(NULL, '1', '$currDate', '$userName', '$d', 'IN',0),";
    }
      //Removes last comma from sql command.
      $sql=substr_replace($sql, "", -1);
      $sql.="; COMMIT;";
      if(!$connection->multi_query($sql)){
        printf("Error message: %s\n", $connection->error);
      }else{
        //All good, return OK message
        echo 200;
        return;
      }

  }

  //Usercheck approved retrieve process. Acknowledges the takeout process and sets the item status to 1 (taken out)
  static function approveRetrieve($value){
    /*  If not approved (value=false) - RETRIEVE CANNOT BE declined!  */
    /*  If approved (value=true) - RETRIEVE CANNOT BE declined!  */
    $userName = $_SESSION['UserUserName'];
    if(empty($userName)){
      return 400; // Session data is empty (e.g User is not loggged in.)
    }else{
      $data = json_decode(stripslashes($_POST['data']));
      $dataArray=array();
      foreach($data as $d){
      array_push($dataArray,$d);
    }
      //For Use in the SQL query.
      $dataString = "'" . implode ( "', '", $data ) . "'";

      //Restore Items allowing others to take it out.
      $sql="START TRANSACTION; UPDATE leltar SET leltar.Status=1 WHERE leltar.Nev IN (".$dataString.");";
      //Acknowledge events in log.
      $sql.="UPDATE takelog SET Acknowledged=1 WHERE User='".$userName."' AND Date='".$_POST['date']."' AND EVENT='IN' AND Item IN (".$dataString."); COMMIT;";

      $connection=Database::runQuery_mysqli();
      if(!$connection->multi_query($sql)){
        printf("Error message: %s\n", $connection->error);
      }else{
        //All good, return OK message
        //echo $sql;
        echo 200;
        return;
      }
    }

    /*  If approved (value=true)  */
  }
}

class itemDataManager{
    static function getNumberOfTotalItems(){}
    static function getNumberOfTakenItems(){}
    static function getItemData($itemTypes){
        $displayed="";
        if ($itemTypes['toDisplay1']!=1 & $itemTypes['toDisplay2']!=2 & $itemTypes['toDisplay3']!=3 ){
            return NULL;
        }
        $sql= 'SELECT * FROM leltar WHERE';
        //Kölcsönözhető
        if ($itemTypes['toDisplay1']==1){
          $sql = $sql.' TakeRestrict=""';
          $displayed=$displayed." Kölcsönözhető";
        }
        //Stúdiós
        if ($itemTypes['toDisplay2']==2){
          if (isset($_GET['toDisplay1'])){
            $sql = $sql.' OR TakeRestrict="s"';
            $displayed=$displayed.", Stúdiós";
          }else{
            $sql = $sql.' TakeRestrict="s"';
            $displayed=$displayed." Stúdiós";
          }
          
        }
        //Nem kölcsönözhető
        if ($itemTypes['toDisplay3']==3){
          if (isset($_GET['toDisplay1']) || isset($_GET['toDisplay2'])){
            $sql = $sql.' OR TakeRestrict="*"';
            $displayed=$displayed.", Nem kölcsönözhető";
          }else{
            $sql = $sql.' TakeRestrict="*"';
            $displayed=$displayed."Nem kölcsönözhető";
          }
        }
        $sql= $sql." ORDER BY Nev ASC";
        return Database::runQuery($sql);
    }

}

class itemHistoryManager{

}

if(isset($_POST['mode'])){
  if($_POST['mode']=='takeOutStaging'){
    echo takeOutManager::stageTakeout();
    //Header set.
    exit();
  }
  if($_POST['mode']=='takeOutApproval'){
    echo takeOutManager::approveTakeout($_POST['value']);
    //Header set.
    exit();
  }
  if($_POST['mode']=='retrieveStaging'){
    echo retrieveManager::stageRetrieve();
    //Header set.
    exit();
  }
  if($_POST['mode']=='retrieveApproval'){
    echo retrieveManager::approveRetrieve($_POST['value']);
    //Header set.
    exit();
  }
  
}

?>