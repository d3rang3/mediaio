<?php
namespace Mediaio;

//require 'vendor/mk-j/php_xlsxwriter/xlsxwriter.class.php';
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Core.php';
use Mediaio\Core;
use Mediaio\Database;

error_reporting(E_ERROR | E_PARSE);

session_start();


class formManager
{
  private static function generateRandomString($length = 10)
  {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
  }

  private static function getIdFromHash($formHash)
  {
    $sql = "SELECT ID FROM forms WHERE LinkHash='" . $formHash . "';";
    $connection = Database::runQuery_mysqli();
    $result = $connection->query($sql);
    $connection->close();
    $row = $result->fetch_assoc();
    return $row['ID'];
  }
  static function createNewForm()
  {
    if (in_array("admin", $_SESSION['groups'])) { //Auto accept 
      $formHash = formManager::generateRandomString(12);
      $sql = "INSERT INTO `forms`(`ID`, `LinkHash`, `Name`, `Header`, `Status`, `Anonim`, `AccessRestrict`, `Data`) VALUES(NULL,'" . $formHash . "' ,'Névtelen','Leírás','0','0','1',NULL);";
      $connection = Database::runQuery_mysqli();
      $connection->query($sql);
      $id = $connection->insert_id;
      $connection->close();
      echo $id;
      exit();
    }
  }

  static function listForms()
  {
    $sql = "SELECT * FROM forms WHERE AccessRestrict='0';";
    if (isset($_SESSION['userId'])) {
      $sql = "SELECT * FROM forms WHERE AccessRestrict IN ('0', '2');";
      if (in_array("admin", $_SESSION['groups'])) {
        $sql = "SELECT * FROM forms";
      }
    }
    $connection = Database::runQuery_mysqli();
    $result = $connection->query($sql);
    $connection->close();
    $forms = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode($forms);
  }

  static function saveFormSettings($form, $id, $formHash)
  {
    if (in_array("admin", $_SESSION['groups'])) {
      $form = json_decode($form, true);
      $form['elements'] = json_encode($form['elements'], JSON_UNESCAPED_UNICODE);

      if ($id == null) {
        $id = formManager::getIdFromHash($formHash);
      }

      $sql = "UPDATE forms SET Name='" . $form['name'] . "',Header='" . $form['header'] . "',Status='" . $form['state'] . "',Anonim='" . $form['anonim'] . "',Data='" . $form['elements'] . "',Accessrestrict='" . $form['access'] . "' WHERE ID=" . $id . ";";
      $connection = Database::runQuery_mysqli();
      $connection->query($sql);
      $connection->close();
      echo 200;
    }
  }

  static function saveFormElements($formElements, $formHeader, $id, $formHash)
  {
    try {
      if (in_array("admin", $_SESSION['groups'])) {
        $formElements = json_decode($formElements, true);
        $formElements = json_encode($formElements, JSON_UNESCAPED_UNICODE);

        if ($id == null) {
          $id = formManager::getIdFromHash($formHash);
        }

        // Prepare an SQL statement to prevent SQL injection
        $sql = "UPDATE forms SET Header=?, Data=? WHERE ID=?;";
        $connection = Database::runQuery_mysqli();
        $stmt = $connection->prepare($sql);

        // Bind parameters to the prepared statement
        $stmt->bind_param("ssi", $formHeader, $formElements, $id);

        // Execute the statement and check for success
        if (!$stmt->execute()) {
          throw new \Exception("Failed to update form.");
        }

        $stmt->close();
        $connection->close();
        echo 200;
      } else {
        throw new \Exception("User not authorized.");
      }
    } catch (\Exception $e) {
      // Log the error or handle it as per your error handling policy
      error_log($e->getMessage());
      // Return an error code or message to the user
      http_response_code(500); // Internal Server Error
      echo 403;
    }
  }

  static function getFormLinkHash($id)
  {
    if (in_array("admin", $_SESSION['groups'])) {
      $sql = "SELECT LinkHash FROM forms WHERE ID=" . $id . ";";
      $connection = Database::runQuery_mysqli();
      $result = $connection->query($sql);
      $connection->close();
      $row = $result->fetch_assoc();
      echo json_encode($row);
    }
  }

  static function generateNewLinkHash($id)
  {
    if (in_array("admin", $_SESSION['groups'])) {
      $formHash = formManager::generateRandomString(12);
      $sql = "UPDATE forms SET LinkHash='" . $formHash . "' WHERE ID=" . $id . ";";
      $connection = Database::runQuery_mysqli();
      $connection->query($sql);
      $connection->close();
      echo 200;
    }
  }

  static function changeBackground($name, $id)
  {
    if (in_array("admin", $_SESSION['groups'])) {
      $sql = "UPDATE forms SET Background='" . $name . "' WHERE ID=" . $id . ";";
      $connection = Database::runQuery_mysqli();
      $connection->query($sql);
      $connection->close();
      echo 200;
    }
  }

  static function getForm($id, $formHash)
  {
    if ($id != null) {
      if (isset($_SESSION['userId']) && in_array("admin", $_SESSION['groups'])) {
        $sql = "SELECT * FROM forms WHERE ID=" . $id . ";";
      } else if (isset($_SESSION['userId'])) {
        $sql = "SELECT * FROM forms WHERE ID=" . $id . " AND AccessRestrict IN ('0','2') AND Status='1';";
      } else {
        $sql = "SELECT * FROM forms WHERE ID=" . $id . " AND AccessRestrict='0' AND Status='1';";
      }
    } else {
      $sql = "SELECT * FROM forms WHERE LinkHash='" . $formHash . "' AND AccessRestrict='3' AND Status='1';";
    }
    $connection = Database::runQuery_mysqli();
    $result = $connection->query($sql);
    $connection->close();
    $row = $result->fetch_assoc();
    //If no rows are returned, return 404
    if ($row == null) {
      echo 404;
      return;
    }
    echo json_encode($row);
  }


  static function deleteForm($id)
  {
    if (in_array("admin", $_SESSION['groups'])) {
      $sql = "DELETE FROM forms WHERE ID=" . $id . ";";
      $connection = Database::runQuery_mysqli();
      $connection->query($sql);
      $connection->close();
      echo 200;
    }
  }

  static function submitAnswer($uid, $id, $formHash, $ip, $answers, $form)
  {
    // Prevent injection
    $answers = json_decode($answers, true);
    array_walk_recursive($answers, function (&$value) {
      $value = htmlspecialchars($value);
    });
    $answers = json_encode($answers, JSON_UNESCAPED_UNICODE);
    if ($id == -1) {
      $id = formManager::getIdFromHash($formHash);
    }

    $connection = Database::runQuery_mysqli();

    $sql = "INSERT INTO `formanswers` (`ID`, `FormID`, `userID`, `userIp`, `UserAnswers`, `FormState`) VALUES (NULL, ?, ?, ?, ?, ?);";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("sssss", $id, $uid, $ip, $answers, $form);

    $stmt->execute();

    $connection->close();
    echo 200;
  }

  static function deleteAnswer($id)
  {
    if (in_array("admin", $_SESSION['groups'])) {
      $sql = "DELETE FROM formanswers WHERE ID=" . $id . ";";
      $connection = Database::runQuery_mysqli();
      $connection->query($sql);
      $connection->close();
      echo 200;
    }
  }

  static function getFormAnswers($id, $formHash)
  {
    if (in_array("admin", $_SESSION['groups'])) {
      if ($id == -1) {
        $id = formManager::getIdFromHash($formHash);
      }
      $sql = "SELECT * FROM formanswers WHERE FormID=" . $id . ";";
      $connection = Database::runQuery_mysqli();
      $result = $connection->query($sql);
      $connection->close();
      $answers = array();
      while ($row = $result->fetch_assoc()) {
        $answers[] = $row;
      }
      echo json_encode($answers);
    }
  }

  static function generateXlsx($id)
  {
    if (in_array("admin", $_SESSION['groups'])) {
      $sql = "SELECT * FROM formanswers WHERE FormID=" . $id . ";";
      $connection = Database::runQuery_mysqli();
      $result = $connection->query($sql);
      $connection->close();
      $answers = array();
      while ($row = $result->fetch_assoc()) {
        $answers[] = $row;
      }
      $data = array();
      $data[] = array('ID', 'FormID', 'userID', 'userIp', 'UserAnswers');
      foreach ($answers as $answer) {
        $data[] = array($answer['ID'], $answer['FormID'], $answer['userID'], $answer['userIp'], $answer['UserAnswers']);
      }
      $writer = new \XLSXWriter();
      $writer->writeSheet($data);
      $writer->writeToFile('output.xlsx');
      echo 200;
    }
  }
}



if (isset($_POST['mode'])) {

  //Set timezone to the computer's timezone.
  date_default_timezone_set('Europe/Budapest');

  switch ($_POST['mode']) {
    case 'createNewForm':
      echo formManager::createNewForm();
      break;

    case 'listForms':
      echo formManager::listForms();
      break;

    case 'save':
      echo formManager::saveFormSettings($_POST['form'], $_POST['id'], $_POST['formHash']);
      break;

    case 'saveFormElements':
      echo formManager::saveFormElements($_POST['formElements'], $_POST['formHeader'], $_POST['formId'], $_POST['formHash']);
      break;

    case 'changeBackground':
      echo formManager::changeBackground($_POST['name'], $_POST['id']);
      break;

    case 'getForm':
      echo formManager::getForm($_POST['id'], $_POST['formHash']);
      break;

    case 'getLinkHash':
      echo formManager::getFormLinkHash($_POST['id']);
      break;

    case 'newLinkHash':
      echo formManager::generateNewLinkHash($_POST['id']);
      break;

    case 'deleteForm':
      echo "deleteForm";
      echo formManager::deleteForm($_POST['id']);
      break;

    case 'submitAnswer':
      echo formManager::submitAnswer($_POST['uid'], $_POST['id'], $_POST['formHash'], $_POST['userIp'], $_POST['answers'], $_POST['form']);
      break;

    case 'deleteAnswer':
      echo formManager::deleteAnswer($_POST['id']);
      break;

    case 'getFormAnswers':
      echo formManager::getFormAnswers($_POST['id'], $_POST['formHash']);
      break;

    case 'generateXlsx':
      echo 'ASD';
      echo formManager::generateXlsx($_POST['id']);
      echo $_POST['value'];
      break;

    default:
      // Optionally handle unknown mode
      echo "Unknown mode";
      break;
  }
  exit();
}

?>