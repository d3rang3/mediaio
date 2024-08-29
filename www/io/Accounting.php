<?php
/** Logs user activity and tracks u
 * ser interactions throughout the webapp */
namespace Mediaio;

require_once 'Database.php';
use Mediaio\Database;

class Accounting
{
    static function logEvent($userID, $event, $optionalData = NULL)
    {

        //Encode JSON data to make it SQL compatible
        $optionalJSONData = json_encode($optionalData);

        //Write to log file
        $logFile = fopen("./log.txt", "a");
        fwrite($logFile, "User: " . $userID . " Event: " . $event . " Data: " . json_encode($optionalJSONData) . "\n");

        $connection = Database::runQuery_mysqli();
        $stmt = mysqli_stmt_init($connection);

        $sql = "INSERT INTO log (UserID, Action,Data) VALUES
        (?,?,?)";
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            return array('code' => '500');
        }
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("sss", $userID, $event, $optionalJSONData);
        $stmt->execute();

        if ($stmt->affected_rows == 1) {
            $connection->close();
            return array('code' => '200');
        } else {
            $connection->close();
            return array('code' => '401');
        }

    }

    static function getLastLogin($userID)
    {
        $connection = Database::runQuery_mysqli();
        $sql = "SELECT * FROM log WHERE UserID = ? AND Action = 'login_Success' ORDER BY ID DESC LIMIT 1";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("s", $userID);
        $stmt->execute();
        $result = $stmt->get_result();
        $connection->close();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return json_encode($row);
        } else {
            return json_encode(array('code' => '404'));
        }
    }

    static function getLogHistory()
    {
        // Select only the last week's data
        $sql = "SELECT * FROM `log` WHERE `Date` > DATE_SUB(NOW(), INTERVAL 1 WEEK) ORDER BY `Date` DESC";
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

    static function getPublicUserInfo($userID = NULL, $notificationSettings = false)
    {
        if ($userID != NULL) {
            $sql = "SELECT `usernameUsers`, `firstName`, `lastName`, `teleNum`, `emailUsers`";
            $notificationSettings ? $sql .= ", `AdditionalData`" : null; // Get the notification settings if requested
            $sql .= " FROM `users` WHERE `idUsers` = " . $userID . ";";
        } else {
            $sql = "SELECT `idUsers`, `usernameUsers`, `firstName`, `lastName`, `teleNum`, `emailUsers`";
            $notificationSettings ? $sql .= ", `AdditionalData`" : null; // Get the notification settings if requested
            $sql .= " FROM `users`;";
        }
        $connection = Database::runQuery_mysqli();
        $result = $connection->query($sql);
        $connection->close();

        if ($result->num_rows > 0) {
            $users = $result->fetch_all(MYSQLI_ASSOC);
            return json_encode($users);
        } else {
            return 500;
        }

    }


    /* 
        Functions for the user notification system
        These functions will be used to setup the settings
    */

    static function getNotificationSettings()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $connection = Database::runQuery_mysqli();
        $sql = "SELECT `AdditionalData` FROM `users` WHERE `idUsers` = ?;";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("s", $_SESSION['userId']);
        $stmt->execute();
        $result = $stmt->get_result();
        $connection->close();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            //Decode the JSON data and only return the notification settings
            $settings = json_decode($row['AdditionalData'], true);
            $settings = isset($settings['notificationSettings']) ? $settings['notificationSettings'] : "";

            return json_encode($settings);
        } else {
            return json_encode(array('code' => '404'));
        }
    }


    static function setNotificationSettings($settings)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $connection = Database::runQuery_mysqli();
        // Get the current Data
        $sql = "SELECT `AdditionalData` FROM `users` WHERE `idUsers` = ?;";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("s", $_SESSION['userId']);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        $currentData = json_decode($row['AdditionalData'], true);

        // Update the notification settings
        $currentData['notificationSettings'] = $settings; // Update the notification settings
        // Encode the data back to JSON
        $settings = json_encode($currentData, JSON_UNESCAPED_UNICODE);

        $sql = "UPDATE `users` SET `AdditionalData` = ? WHERE `idUsers` = ?;";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("ss", $settings, $_SESSION['userId']);
        $stmt->execute();
        $connection->close();

        if ($stmt->affected_rows == 1) {
            return 200;
        } else {
            return 500;
        }
    }
}


if (isset($_POST['mode'])) {
    switch ($_POST['mode']) {
        case 'getPublicUserInfo':
            echo Accounting::getPublicUserInfo();
            break;


        // Get the last login event for a user
        case 'getLastLogin':
            echo Accounting::getLastLogin($_POST['userID']);
            break;

        // Get the log history for the last week
        case 'getLogHistory':
            echo Accounting::getLogHistory();
            break;


        // Get the notification settings for a user
        case 'getNotificationSettings':
            echo Accounting::getNotificationSettings();
            break;
        // Set the notification settings for a user
        case 'setNotificationSettings':
            echo Accounting::setNotificationSettings($_POST['settings']);
            break;
    }
}