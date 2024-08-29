<?php
namespace Mediaio;
require_once __DIR__.'/../../Mailer.php';
require_once __DIR__.'/../../Database.php';
use Mediaio\MailService;
use Mediaio\Database;

error_reporting(E_ALL ^ E_NOTICE);
session_start();

if (isset($_POST['method'])){
    if($_POST['method']=='get_user_items'){
        $sql = "SELECT Nev, UID FROM `leltar` WHERE RentBy = '".$_POST['userName']."'";
        $result = Database::runQuery($sql);

        //for each result row, put it in a sjon array, and then echo it
        $resultArray = [];
        while($row = $result->fetch_assoc()) {
            array_push($resultArray, $row);
        }
        echo json_encode($resultArray);
        exit();
    }
    if($_POST['method']=='announceDamage'){
        //Encode useritem as json
        $damageItem=array(
        [
            'uid' => $_POST['userItems'],
            'name' => $_POST['itemName']]
        );

        //Encode as JSON string
        $damageItem = json_encode($damageItem);

        //Give item to service user
        $sql = "START TRANSACTION; UPDATE `leltar` SET `RentBy` = 'Service' WHERE `leltar`.`UID` = '".$_POST['userItems']."';";
        $sql.= "INSERT INTO takelog VALUES (NULL, '".date("Y/m/d H:i:s")."','service','".$damageItem."','SERVICE',1,'service'); COMMIT;";
        //Add item to takelog

        $connection=Database::runQuery_mysqli();
        if(!$connection->multi_query($sql)){
            echo "Error message: %s\n".$connection->error;
            exit();
        }

        $subject = 'Sérülés - '.$_POST['userItems'];
       /* Set the mail message body. */
        $content = $_SESSION['fullName'].' sérülést jelentett be a(z) <strong>'.$_POST['userItems'].'</strong> tárgyon! <br>Leírás: '.$_POST['description'];
        //Add link to e-mail
        $content .= '<br><button><a href="https://io.arpadmedia.hu/uploads/images/'.$_POST['zip-file'].'.zip">Képek letöltése</a></button>
                ';
        /* Finally send the mail using MailService */
        MailService::sendContactMail('arpadmedia.io@gmail.com',$subject,$content);
        echo 200;
        exit();
    }

    if($_POST['method']=='getServiceItems'){
        //$workID=$_POST['workID'];
        //$n=$_POST["user"];
        $sql="SELECT UID, Nev FROM leltar WHERE RentBy='Service';";
        $connection=Database::runQuery_mysqli();
        //Store result in array
        $resultItems=array();
        if ($result = $connection->query($sql)) {
          while ($row = $result->fetch_assoc()) {
            $resultItems[] = array('uid'=> $row['UID'],'name'=> $row['Nev']);
          }
          echo(json_encode($resultItems));
        }else{
          echo 404;
        }
        exit();
    }

    if($_POST['method']=='returnServiceItem'){
        $uid=$_POST['uid'];
        $dataArray=array();
        array_push($dataArray,array("uid" => $uid,"name" => $_POST['itemName']));
        $sql="UPDATE leltar SET RentBy=NULL, Status=1 WHERE UID='".$uid."';";
        $sql.="INSERT INTO takelog VALUES (NULL, '".date("Y/m/d H:i:s")."','Service','".json_encode($dataArray)."','IN',1,'Service');";
        $connection=Database::runQuery_mysqli();
        if ($result = $connection->multi_query($sql)) {
            echo 200;
        }else{
            echo 500;
        }

        exit();
    }
}







?>