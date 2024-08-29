<?php
require_once __DIR__.'/../../ItemManager.php';
require_once __DIR__.'/../../Core.php';
use Mediaio\ItemDataManager;
use Mediaio\Core;
use Mediaio\retrieveManager;
//Usage: TODO
//Returns 200 with a token if the credentials are correct
$request_method=$_SERVER["REQUEST_METHOD"];
switch($request_method)
  {
    case 'GET':
      // Retrive Products
      if(!empty($_GET["apikey"]))
      {
        //Check API key
        $c=new Core();
        $loginResponse=$c->loginWithApikey($_GET["apikey"]);
        if($loginResponse['code']!=200){
          header("HTTP/1.0 ".$loginResponse['code']);
          header('Content-Type: application/json');
          echo json_encode(array('type'=>'error', 'text' => 'Invalid api key'));
          exit();
        }
        //Code was OK

        //List by critria
        if(isset($_GET["type"]) && $_GET["orderby"]){
          $itemManager=new itemDataManager();
          $itemsJSON=json_decode($itemManager->listByCriteria($_GET["type"],$_GET["orderby"]));
          header("HTTP/1.0 200 OK");
          header('Content-Type: application/json');
          echo json_encode(array('items'=>$itemsJSON, 'text' => 'OK.'));
          exit();
        }
        
        //list user items
        if(isset($_GET["mode"]) & $_GET["mode"]=="user"){
          $itemManager=new retrieveManager();
          $itemsJSON=json_decode($itemManager->listUserItems());
          header("HTTP/1.0 200 OK");
          header('Content-Type: application/json');
          echo json_encode(array('items'=>$itemsJSON, 'text' => 'OK.'));
          exit();
        }

        else{
        //list  every item
        $itemManager=new itemDataManager();
        $itemsJSON=json_decode($itemManager->getItems());
		    header("HTTP/1.0 200 OK");
		    header('Content-Type: application/json');
		    echo json_encode(array('items'=>$itemsJSON, 'text' => 'OK.'));
        }

      }
      else
      {
        //header("HTTP/1.0 500 Internal Server Error");
      }
      break;
    default:
      // Invalid Request Method
      header("HTTP/1.0 405 Method Not Allowed");
      break;
  }


?>