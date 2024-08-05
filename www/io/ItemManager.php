<?php

/**
 * ItemManager.php
 * Manages the takeout and retrieve processes, stats and user Lists.
 */

namespace Mediaio;

require_once __DIR__ . '/Database.php';

use Mediaio\Database;

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}


class takeOutManager
{
  /*
  User takeout process. Stages the takeout as it still needs to be approved on userCheck panel.
  sets the item status to 2 (needs approvement.)
  */
  static function stageTakeout($takeoutItems)
  {
    //Accesses post and Session Data.
    // Set time zone to Budapest
    date_default_timezone_set('Europe/Budapest');
    $currDate = date("Y/m/d H:i:s");
    $connection = Database::runQuery_mysqli();

    $UID = $_SESSION['userId'];

    // Is user an admin?
    $acknowledged = in_array("admin", $_SESSION['groups']) ? 1 : 0; // Stageing happens here
    // Set the ackBy field to the user's name if the user is an admin
    $ackBy = $acknowledged ? $_SESSION['UserUserName'] : NULL;

    // Change every item as taken in the database
    $takeoutItems = json_decode($takeoutItems, true);

    try {
      // Start transaction
      $connection->begin_transaction();
      // Check if planned takeout start time is in the future
      $status = in_array("admin", $_SESSION['groups']) ? 0 : 2;

      $sql = "UPDATE leltar SET Status = $status, RentBy = '" . $_SESSION['userId'] . "' WHERE `UID` = ?";

      // Update leltar
      $stmt = $connection->prepare($sql);
      foreach ($takeoutItems as $item) {
        $stmt->bind_param("s", $item['uid']);
        $stmt->execute();
      }

      // Commit transaction
      $connection->commit();
    } catch (\Exception $e) {
      // Rollback transaction if there is an error
      $connection->rollback();
      printf("Error message: %s\n", $e->getMessage());
      $connection->close();
      return 500;
    }

    try {
      $takeoutItems = json_encode($takeoutItems);
      $connection->begin_transaction();

      // TAKELOG
      $sql = "INSERT INTO takelog (`ID`, `Date`, `UserID`, `Items`, `Event`,`Acknowledged`,`ACKBY`) 
            VALUES (NULL, '$currDate', '$UID', '$takeoutItems', 'OUT', $acknowledged, '$ackBy')";
      $connection->query($sql);

      $connection->commit();
    } catch (\Exception $e) {
      echo "Error: " . $e->getMessage();
      $connection->close();
      return 500;
    }
    $connection->close();
    return 200;
  }

  /*Take out items from the database. Sets the item status to 0 (taken out)

  Input: Item UIDs in an array.
  Privilege validation is done too.
  Bypasses the userCheck process for now.
  Currenty limited behaviour (Only empty takerestrict items work!)*/

  //TODO: update this behaviour.
  //static function REST_takeout($items, $userData)
  //{
  //  $successfulTakeouts = 0;
  //  $successfulItems = array();
  //  foreach ($items as $item) {
  //    # Check if it is taken out or marked as restri
  //    $sql = ("SELECT Status, TakeRestrict, RentBy FROM leltar WHERE UID=?");
  //    //Get a new database connection
  //    $connection = Database::runQuery_mysqli();
  //    $stmt = $connection->prepare($sql);
  //    $stmt->bind_param("s", $item);
  //    $stmt->execute();
  //    $result = $stmt->get_result();
  //    $row = $result->fetch_assoc();
//
  //    if ($row['Status'] == 0 && $row['RentBy'] != NULL && $row['TakeRestrict'] != "") { //TODO: Update this line!
  //      //Item is taken out, or currenty limited by api (Only empty takerestrics items work!)
  //      continue;
  //    } else {
  //      $sql = "UPDATE leltar SET Status = 0, RentBy = ? WHERE UID = ?";
  //      $stmt = $connection->prepare($sql);
  //      $stmt->bind_param("ss", $userData['username'], $item);
  //      $stmt->execute();
//
  //      // Check affected rows on the prepared statement
  //      if ($stmt->affected_rows == 1) {
  //        // All good, return OK message
  //        $successfulItems[] = $item;
  //        $successfulTakeouts++;
  //      }
//
  //      $stmt->close();
  //    }
  //  }
  //  return array('successfulTakeouts' => $successfulTakeouts, 'successfulItems' => $successfulItems);
  //}

  /*Retrieve items to the database. Sets the item status to 1.

  Input: Item UIDs in an array.
  Privilege validation is done too.
  Bypasses the userCheck process for now.*/

  //TODO: update this behaviour.
  //static function REST_retrieve($items, $userData)
  //{
  //  $successfulRetrieves = 0;
  //  $successfulItems = array();
  //  foreach ($items as $item) {
  //    # Check if it is taken out or marked as restri
  //    $sql = ("SELECT Status, TakeRestrict, RentBy FROM leltar WHERE UID=?");
  //    //Get a new database connection
  //    $connection = Database::runQuery_mysqli();
  //    $stmt = $connection->prepare($sql);
  //    $stmt->bind_param("s", $item);
  //    $stmt->execute();
  //    $result = $stmt->get_result();
  //    $row = $result->fetch_assoc();

  //    if ($row['Status'] == 0 && $row['RentBy'] == $userData['username']) {
  //      //Item is taken out by this user.
  //      $sql = "UPDATE leltar SET Status = 1, RentBy = NULL WHERE UID = ?";
  //      $stmt = $connection->prepare($sql);
  //      $stmt->bind_param("s", $item);
  //      $stmt->execute();

  //      // Check affected rows on the prepared statement
  //      if ($stmt->affected_rows == 1) {
  //        // All good, return OK message
  //        $successfulItems[] = $item;
  //        $successfulRetrieves++;
  //      }
  //      $stmt->close();
  //    } else {
  //      continue;
  //    }
  //  }
  //  return array('successfulRetrieves' => $successfulRetrieves, 'successfulItems' => $successfulItems);
  //}
}

class retrieveManager
{
  // Function to list the items that are taken out by the user
  static function listUserItems($COUNT = false)
  {
    //Get the items that are currently by the user
    $connection = Database::runQuery_mysqli();

    $sql = "SELECT * FROM leltar WHERE RentBy = ? AND Status = 0";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("s", $_SESSION['userId']);
    $stmt->execute();
    $result = $stmt->get_result();
    $response = $result->fetch_all(MYSQLI_ASSOC);

    $itemCount = count($response);

    return $COUNT ? $itemCount : json_encode($response);
  }

  /*
  User takeout process. Stages the takeout as it still needs to be approved on userCheck panel.
  sets the item status to 2 (needs approvement.)
  */
  static function stageRetrieve()
  {
    date_default_timezone_set('Europe/Budapest');
    $currDate = date("Y/m/d H:i:s");

    $retrieveItems = json_decode($_POST['data'], true);

    // Database init  - create a mysqli object
    $connection = Database::runQuery_mysqli();

    $status = in_array("admin", $_SESSION['groups']) ? 1 : 2;
    $acknowledged = in_array("admin", $_SESSION['groups']) ? 1 : 0;
    $ackBy = in_array("admin", $_SESSION['groups']) ? $_SESSION['UserUserName'] : NULL;
    $RentBy = in_array("admin", $_SESSION['groups']) ? 'NULL' : $_SESSION['userId'];
    try {
      // Start transaction
      $connection->begin_transaction();

      // Update leltar
      $stmt = $connection->prepare("UPDATE `leltar` SET `Status`=$status, `RentBy`=$RentBy WHERE `UID`=?;");
      foreach ($retrieveItems as $item) {
        // Check if the item is in the planned takeouts
        $stmt->bind_param("s", $item['uid']);
        $stmt->execute();
      }

      //Convert data to JSON
      $dataJSON = json_encode($retrieveItems);

      // Insert into takelog
      $stmt = $connection->prepare("INSERT INTO takelog VALUES (NULL, ?, ?, ?, 'IN', ?, ?);");
      $stmt->bind_param("sssis", $currDate, $_SESSION['userId'], $dataJSON, $acknowledged, $ackBy);
      $stmt->execute();

      // Commit transaction
      $connection->commit();

      // All good, return OK message
      echo $acknowledged ? 200 : 201;
      exit();
    } catch (\Exception $e) {
      // Rollback transaction if there is an error
      $connection->rollback();
      printf("Error message: %s\n", $e->getMessage());
    }
  }
}

class itemDataManager
{

  // Change owner of items
  static function changeOwner($items, $newUserID)
  {
    // Only admins can change the owner of items
    if (!in_array("admin", $_SESSION['groups'])) {
      return 403;
    }

    try {
      $connection = Database::runQuery_mysqli();
      $connection->begin_transaction(); // Real backend developers use transactions XD

      $newUserID = intval($newUserID);

      date_default_timezone_set('Europe/Budapest');
      $currDate = date("Y/m/d H:i:s");

      // Update takelog
      $sql = "INSERT INTO `takelog` (`Date`, `UserID`, `Items`, `Event`,`Acknowledged`,`ACKBY`) 
        VALUES (?, ?, ?, 'CHANGE', 1, ?);";
      $stmt = $connection->prepare($sql);
      $stmt->bind_param("siss", $currDate, $newUserID, $items, $_SESSION['UserUserName']);
      $stmt->execute();

      $items = json_decode($items, true);
      $items = array_map(function ($item) {
        return $item['uid'];
      }, $items);

      $sql = "UPDATE leltar SET RentBy=? WHERE UID=?";
      $stmt = $connection->prepare($sql);
      $stmt->bind_param("ss", $newUserID, $item);
      foreach ($items as $item) {
        $stmt->execute();
      }

      $connection->commit();
    } catch (\Exception $e) {
      echo "Error: " . $e->getMessage();
      $connection->rollback();
      return 500;
    }

    return 200;
  }

  // ______________________________________________________________________________________

  /*

  Confirm items in the database. Sets the item status to 0 (taken out) or 1 (available)

  */
  static function confirmItems($eventID, $items, $direction)
  {
    if (!isset($_SESSION["userId"]))
      return 400; // Session data is empty (e.g User is not loggged in.)

    $items = json_decode($items, true);

    $connection = Database::runQuery_mysqli();

    // Get the user who initiated the transaction and the items
    $sql = "SELECT UserID, Items FROM takelog WHERE ID=" . $eventID;
    $info = $connection->query($sql);
    $info = $info->fetch_assoc();
    $transUser = $info['UserID'];
    $originalItems = json_decode($info['Items'], true);

    $declinedItems = array();

    // For every item check if it was accepted or declined
    foreach ($items as $item) {
      if ($item['declined'] == 'true') {
        $status = ($direction == 'OUT') ? 1 : 0;
        $RentBy = ($direction == 'OUT') ? 'NULL' : $transUser;
        $sql = "UPDATE `leltar` SET `Status` = $status, `RentBy`=$RentBy WHERE `UID` = '" . $item['uid'] . "'";
        $connection->query($sql);
        // Add the declined item to the list
        $declinedItems[] = $item['uid'];
        continue;
      }

      if ($direction == 'OUT') {
        $sql = "UPDATE `leltar` SET `Status` = 0 WHERE `UID` = '" . $item['uid'] . "'";
      } else {
        $sql = "UPDATE `leltar` SET `Status` = 1, `RentBy`=NULL WHERE `UID` = '" . $item['uid'] . "'";
      }
      $connection->query($sql);
    }

    $sql = "UPDATE takelog SET Acknowledged=1, ACKBY='" . $_SESSION['UserUserName'] . "' WHERE ID=" . $eventID;
    $result = $connection->query($sql);


    if ($result == TRUE) {
      // Check if there are any declined items
      if (count($declinedItems) > 0) {

        // Get the id and name from the original items for the declined items
        $declinedItems = array_map(function ($item) use ($originalItems) {
          foreach ($originalItems as $originalItem) {
            if ($originalItem['uid'] == $item) {
              return array('uid' => $item, 'name' => $originalItem['name']);
            }
          }
        }, $declinedItems);


        // Create a new takelog entry for the declined items
        $sql = "INSERT INTO takelog (`ID`, `Date`, `UserID`, `Items`, `Event`,`Acknowledged`,`ACKBY`) 
                VALUES (NULL, '" . date("Y/m/d H:i:s") . "', '" . $transUser . "', '" . json_encode($declinedItems) . "', 'DECLINE', 1, '" . $_SESSION['UserUserName'] . "')";
        $connection->query($sql);

        //Function to compare multidimensional arrays
        function array_diff_multi($array1, $array2)
        {
          foreach ($array1 as $key => $value) {
            if (array_search($value, $array2) !== false) {
              unset($array1[$key]);
            }
          }
          return array_values($array1); // Use array_values to reindex the array
        }

        // Update the takelog entry for the original items
        $sql = "UPDATE takelog SET Items='" . json_encode(array_diff_multi($originalItems, $declinedItems)) . "' WHERE ID=" . $eventID;
        $connection->query($sql);

        // If everything was declined, delete the original takelog entry
        if (count($originalItems) == count($declinedItems)) {
          $sql = "DELETE FROM takelog WHERE ID=" . $eventID;
          $connection->query($sql);
        }
      }
      $connection->close();
      return 200;
    }
    return 500;
  }


  static function getPresets()
  {
    $mysqli = Database::runQuery_mysqli();
    $rows = array();
    $mysqli->set_charset("utf8");
    $query = "SELECT Name, Items FROM takeoutpresets";
    if ($result = $mysqli->query($query)) {
      while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
      }
      $a = json_encode($rows);
      //var_dump($a);
      echo $a;
    }
    return;
  }


  //Obtains items UID and Name from the database
  static function getItemNames()
  {
    $connection = Database::runQuery_mysqli();
    $sql = "SELECT UID, Nev FROM leltar";
    $stmt = $connection->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    $result = json_encode($rows);
    return $result;
  }

  //Obtains every item data from the database
  static function getItems()
  {
    // Get a new database connection
    $connection = Database::runQuery_mysqli();

    // Prepare the SQL query
    $sql = "SELECT leltar.*, COALESCE(leltar.RentBy, tp.UserID) as RentBy, tp.StartTime, tp.ReturnTime 
    FROM leltar
    LEFT JOIN (
        SELECT tp1.Items, tp1.UserID, tp1.StartTime, tp1.ReturnTime
        FROM takeoutPlanner tp1
        JOIN (
            SELECT Items, MIN(StartTime) as MinStartTime
            FROM takeoutPlanner
            WHERE eventState=0
            GROUP BY Items
        ) as tp2
        ON tp1.Items = tp2.Items AND tp1.StartTime = tp2.MinStartTime
    ) as tp
    ON leltar.isPlanned = 1 AND leltar.Status = 1 AND JSON_EXTRACT(tp.Items, '$[*].uid') LIKE CONCAT('%\"', leltar.UID, '\"%')
    ORDER BY leltar.ID";

    // Execute the query
    $stmt = $connection->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = $result->fetch_all(MYSQLI_ASSOC);

    // Encode the result to JSON
    $result = json_encode($rows);

    return $result;
  }

  // Function to list specific items from the database (Used on "leltar" page)
  static function listByCriteria($itemState, $orderCriteria, $orderDirection = 'asc', $takeRestrict = 'none')
  {
    $takeRestrictArray = array(
      'medias' => 'TakeRestrict=""',
      'studios' => 'TakeRestrict="s"',
      'eventes' => 'TakeRestrict="e"',
      'nonRentable' => 'TakeRestrict="*"',
      'mediaAndStudio' => '(TakeRestrict="" OR TakeRestrict="s")',
      'mediaAndEvent' => '(TakeRestrict="" OR TakeRestrict="e")',
      'studioAndEvent' => '(TakeRestrict="s" OR TakeRestrict="e")',
      'mediaAndStudioAndEvent' => '(TakeRestrict="" OR TakeRestrict="s" OR TakeRestrict="e")',
      'none' => '1=1',
    );

    $stateArray = array(
      'in' => 'Status = 1',
      'out' => '(Status = 0 OR Status = 2)',
      'all' => '1=1'
    );

    $orderbyArray = array(
      'name' => 'Nev',
      'uid' => 'UID',
      'status' => 'Status',
      'rentby' => 'RentBy',
      'id' => 'ID',
      'takerestrict' => 'TakeRestrict',
      'type' => 'Tipus',
    );

    $orderDirARR = array(
      'asc' => 'ASC',
      'desc' => 'DESC',
    );

    $sql = "SELECT * FROM leltar WHERE " . $takeRestrictArray[$takeRestrict] . " AND " . $stateArray[$itemState] .
      " ORDER BY " . $orderbyArray[$orderCriteria] . " " . $orderDirARR[$orderDirection];

    // Get a new database connection
    $connection = Database::runQuery_mysqli();
    $stmt = $connection->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = array();
    while ($row = $result->fetch_assoc()) {
      $rows[] = $row;
    }
    $result = json_encode($rows);
    return $result;
  }

  //Update item attributes in the database
  //item: JSON-encoded data
  static function updateItemAttributes($item)
  {
    $item = json_decode($item, true);

    //Check if item data is invalid
    if (($item['UID'] == '') || ($item['Nev']) == '') {
      echo 500;
      return;
    }

    //modify TakeRestrict: if it is null, set it to ''

    if ($item['TakeRestrict'] == NULL) {
      $item['TakeRestrict'] = '';
    }

    $sql = "UPDATE leltar SET UID=?, Nev=?, Tipus=?, Category=?, TakeRestrict=? WHERE ID=?";
    $connection = Database::runQuery_mysqli();
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("ssssss", $item['UID'], $item['Nev'], $item['Tipus'], $item['Category'], $item['TakeRestrict'], $item['ID']);
    $stmt->execute();
    //$result = $stmt->get_result();
    return 200;
  }

  static function createItem($item)
  {
    $item = json_decode($item, true);

    //Check if item data is invalid
    if (($item['UID'] == '') || ($item['Nev']) == '') {
      echo 500;
      return;
    }

    $sql = "INSERT INTO `leltar` (`UID`, `Nev`, `Tipus`, `Category`, `Status`, `RentBy`, `isPlanned`, `TakeRestrict`, `ConnectsToItems`)";
    $sql .= "VALUES (?, ?, ?, ?, '1', NULL, '0', ?, NULL)";
    //echo $sql;
    $connection = Database::runQuery_mysqli();
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("sssss", $item['UID'], $item['Nev'], $item['Tipus'], $item['Category'], $item['TakeRestrict']);
    $stmt->execute();
    //$result = $stmt->get_result();
    return 200;
  }


  static function getItemsForConfirmation()
  {
    $sql = "SELECT * FROM takelog WHERE Acknowledged=0 AND Event != 'SERVICE' ORDER BY DATE DESC, EVENT";
    //Get a new database connection
    $connection = Database::runQuery_mysqli();
    $stmt = $connection->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = array();
    while ($row = $result->fetch_assoc()) {
      $rows[] = $row;
    }
    $result = json_encode($rows);
    return $result;
  }

  static function getToBeUserCheckedCount()
  {
    $sql = "SELECT COUNT(*) FROM takelog WHERE Acknowledged=0";
    //Get a new database connection
    $connection = Database::runQuery_mysqli();
    $stmt = $connection->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['COUNT(*)'];
  }

  static function getServiceItemCount()
  {
    $sql = "SELECT COUNT(*) FROM leltar WHERE Status=-1";
    //Get a new database connection
    $connection = Database::runQuery_mysqli();
    $stmt = $connection->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['COUNT(*)'];
  }
}

class itemHistoryManager
{

  static function getItemHistory($itemUID)
  {
    if (empty($itemUID)) {
      throw new \Exception("Item UID cannot be empty");
    }

    // Construct the JSON string
    $jsonString = json_encode(['uid' => $itemUID]);

    // Use prepared statements
    $sql = "SELECT * FROM `takelog` WHERE JSON_CONTAINS(Items, ?) ORDER BY `Date` DESC";
    //Get a new database connection
    $connection = Database::runQuery_mysqli();
    $stmt = $connection->prepare($sql);
    $stmt->bind_param('s', $jsonString);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = array();
    while ($row = $result->fetch_assoc()) {
      $rows[] = $row;
    }
    $result = json_encode($rows);
    return $result;
  }


  static function getInventoryHistory()
  {
    // Select only the last week's data
    $sql = "SELECT * FROM `takelog` WHERE `Date` > DATE_SUB(NOW(), INTERVAL 1 WEEK) ORDER BY `Date` DESC";
    //Get a new database connection
    $connection = Database::runQuery_mysqli();
    $result = $connection->query($sql);
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    $result = json_encode($rows);
    return $result;
  }

}


/**
 * Handle URL requests
 */
if (isset($_POST['mode'])) {

  //Set timezone to the computer's timezone.
  date_default_timezone_set('Europe/Budapest');

  if ($_POST['mode'] == 'stageTakeout') {
    echo takeOutManager::stageTakeout($_POST['items']);
  }
  if ($_POST['mode'] == 'listUserItems') {
    echo retrieveManager::listUserItems();
  }
  if ($_POST['mode'] == 'retrieveStaging') {
    echo retrieveManager::stageRetrieve();
  }

  //Handles item ownership change
  if ($_POST['mode'] == 'changeOwner') {
    echo itemDataManager::changeOwner($_POST['items'], $_POST['newOwner']);
  }

  if ($_POST['mode'] == 'confirmItems') {
    echo itemDataManager::confirmItems($_POST['eventID'], $_POST['items'], $_POST['direction']);
  }
  if ($_POST['mode'] == 'getItems') {
    echo itemDataManager::getItems();
  }

  if ($_POST['mode'] == 'getItemNames') {
    echo itemDataManager::getItemNames();
  }

  if ($_POST['mode'] == 'listByCriteria') {
    echo itemDataManager::listByCriteria($_POST['itemState'], $_POST['orderCriteria'], $_POST['orderDirection'], $_POST['takeRestrict']);
  }

  if ($_POST['mode'] == 'getItemHistory') {
    echo itemHistoryManager::getItemHistory($_POST['itemUID']);
  }

  if ($_POST['mode'] == 'getInventoryHistory') {
    echo itemHistoryManager::getInventoryHistory();
  }

  if ($_POST['mode'] == 'getPresets') {
    echo itemDataManager::getPresets();
  }

  if ($_POST['mode'] == 'getItemsForConfirmation') {
    echo itemDataManager::getItemsForConfirmation();
  }

  if ($_POST['mode'] == 'updateItemAttributes') {
    echo itemDataManager::updateItemAttributes($_POST['item']);
  }

  if ($_POST['mode'] == 'createItem') {
    echo itemDataManager::createItem($_POST['item']);
  }

  if ($_POST['mode'] == 'getProfileItemCounts') {
    echo itemDataManager::getServiceItemCount();
    echo ",";
    echo itemDataManager::getToBeUserCheckedCount();
    echo ",";
    echo retrieveManager::listUserItems(true);
  }
  exit();
}
