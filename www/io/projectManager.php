<?php
namespace Mediaio;

require_once 'Database.php';
require_once 'projectMailer.php';
require_once __DIR__ . '/projectManager/upload-handler.php';

use Mediaio\Database;
use Mediaio\projectPictureManager;

//error_reporting(E_ERROR | E_PARSE);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class projectManager
{
    private static $schema = "am_projects";

    static function createNewProject()
    {
        if (in_array("admin", $_SESSION['groups'])) { //Auto accept 
            $sql = "INSERT INTO `projects`(`ID`, `Name`, `Description`, `Deadline`, `managerUID`) VALUES (NULL,'Névtelen','Leírás...',NULL," . $_SESSION['userId'] . ");";
            $connection = Database::runQuery_mysqli(self::$schema);
            $connection->query($sql);
            $id = $connection->insert_id;

            // Add the creator to the project
            $sql = "INSERT INTO `project_members`(`ProjectID`, `UserID`) VALUES (" . $id . "," . $_SESSION['userId'] . ");";
            $connection->query($sql);

            $connection->close();

            echo $id;
        } else {
            echo 403;
        }
    }

    static function archiveProject()
    {
        if (in_array("admin", $_SESSION['groups'])) {
            $sql = "UPDATE projects SET Archived=1 WHERE ID=" . $_POST['projectId'] . ";";
            $connection = Database::runQuery_mysqli(self::$schema);
            $connection->query($sql);
            $connection->close();
            echo 200;
            exit();
        } else {
            echo 403;
        }
    }

    static function restoreProject($projectID)
    {
        if (in_array("admin", $_SESSION['groups'])) {
            $sql = "UPDATE projects SET Archived=0 WHERE ID=" . $projectID . ";";
            $connection = Database::runQuery_mysqli(self::$schema);
            $connection->query($sql);
            $connection->close();
            return 200;
        } else {
            return 403;
        }
    }

    static function deleteProject()
    {
        if (in_array("admin", $_SESSION['groups'])) {

            // Get picture tasks of the project
            $sql = "SELECT * FROM project_components WHERE ProjectId=" . $_POST['id'] . " AND Task_type='image';";
            $connection = Database::runQuery_mysqli(self::$schema);
            $result = $connection->query($sql);
            while ($row = $result->fetch_assoc()) {
                // Delete the picture of the task
                try {
                    projectPictureManager::deleteImage($row['ID']);
                } catch (\Exception $e) {
                    // Do nothing
                }
            }


            $sql = "DELETE FROM projects WHERE ID=" . $_POST['id'] . ";";
            $connection->query($sql);
            $connection->close();
            echo 1;
            exit();
        } else {
            echo 403;
        }
    }

    static function checkforUpdates()
    {
        $lastUpdate = $_POST['lastUpdate'];
        // Set timezone to +02:00
        date_default_timezone_set('Europe/Budapest');

        $sql = "SELECT * FROM projects WHERE Last_edited > '" . $lastUpdate . "';";
        $connection = Database::runQuery_mysqli(self::$schema);
        $result = $connection->query($sql);
        if ($result->num_rows > 0) {
            return 'true';
        }

        $sql = "SELECT * FROM project_components WHERE Last_edit > '" . $lastUpdate . "';";
        $result = $connection->query($sql);
        if ($result->num_rows > 0) {
            return 'true';
        }
        return 'false';
    }

    static function listProjects($archived = 0)
    {
        $sql = "SELECT DISTINCT * FROM (";

        if (in_array("admin", $_SESSION['groups'])) {
            $sql .= "SELECT * FROM `projects` WHERE Archived=" . $archived;
        } else if (in_array("média", $_SESSION['groups'])) {
            $sql .= "SELECT * FROM `projects` WHERE Visibility_group IN (0,1) AND Archived=" . $archived;
        } else if (in_array("studio", $_SESSION['groups'])) {
            $sql .= "SELECT * FROM `projects` WHERE Visibility_group IN (0,2) AND Archived=" . $archived;
        } else {
            $sql .= "SELECT * FROM `projects` WHERE Visibility_group=0 AND Archived=" . $archived;
        }

        $sql .= " UNION ALL SELECT * FROM `projects` WHERE ID IN (SELECT ProjectID FROM project_members WHERE UserID=" . $_SESSION['userId'] . ") AND Visibility_group=4 AND Archived=0";

        $sql .= ") AS combined ORDER BY Deadline IS NULL, Deadline";

        $connection = Database::runQuery_mysqli(self::$schema);
        $result = $connection->query($sql);
        $resultItems = array();
        while ($row = $result->fetch_assoc()) {
            $resultItems[] = $row;
        }

        // Get project members for each project
        foreach ($resultItems as $key => $item) {
            $sql = "SELECT * FROM `project_members` WHERE ProjectID=" . $item['ID'] . ";";
            $result = $connection->query($sql);
            $members = array();
            while ($row = $result->fetch_assoc()) {
                $members[] = $row['UserID'];
            }
            // If the user is an admin or is part of the project, set "canEdit" to true
            if (in_array("admin", $_SESSION['groups']) || in_array($_SESSION['userId'], $members)) {
                $resultItems[$key]['canEdit'] = 1;
            } else {
                $resultItems[$key]['canEdit'] = 0;
            }
        }
        return json_encode($resultItems);
    }

    static function getProject()
    {
        $sql = "SELECT * FROM projects WHERE ID=" . $_POST['id'] . ";";
        $connection = Database::runQuery_mysqli(self::$schema);
        $result = $connection->query($sql);
        $project = $result->fetch_assoc();
        if ($project == null) {
            echo 404;
            exit();
        }

        // Get project members
        $sql = "SELECT * FROM `project_members` WHERE ProjectID=" . $_POST['id'] . ";";
        $result = $connection->query($sql);
        $members = array();
        while ($row = $result->fetch_assoc()) {
            $members[] = $row['UserID'];
        }
        // If the user is an admin or is part of the project, set "canEdit" to true
        if (in_array("admin", $_SESSION['groups']) || in_array($_SESSION['userId'], $members)) {
            $project['canEdit'] = 1;
        } else {
            $project['canEdit'] = 0;
        }


        echo (json_encode($project));
        exit();
    }

    static function getProjectRoot()
    {
        $sql = "SELECT `NAS_path` FROM `projects` WHERE `ID`=" . $_POST['id'] . ";";
        $connection = Database::runQuery_mysqli(self::$schema);
        $result = $connection->query($sql);
        $path = $result->fetch_assoc()['NAS_path'];
        $connection->close();
        echo $path;
    }

    // TASKS

    static function getProjectTask($taskID = null)
    {
        if ($taskID == null) {
            // Get all tasks of the project
            $sql = "SELECT pc.*, p.Deadline as ProjectDeadline, p.managerUID, p.NAS_path as NASPath,
                    u1.firstName as CreatorFirstName, u1.lastName as CreatorLastName, u1.usernameUsers as CreatorUsername, 
                    u2.firstName as EditorFirstName, u2.lastName as EditorLastName, u2.usernameUsers as EditorUsername
                    FROM am_projects.project_components pc
                    LEFT JOIN am_projects.projects p ON pc.ProjectId = p.ID
                    LEFT JOIN arpadmedia.users u1 ON pc.AddedByUID = u1.idUsers
                    LEFT JOIN arpadmedia.users u2 ON pc.EditedByUID = u2.idUsers
                    WHERE pc.ProjectId=" . $_POST['proj_id'] . " 
                    ORDER BY COALESCE(pc.Position, pc.Deadline);";
        } else {
            // Get the task with the specified ID
            $sql = "SELECT pc.*, p.Deadline as ProjectDeadline, p.managerUID, p.NAS_path as NASPath,
                    u1.firstName as CreatorFirstName, u1.lastName as CreatorLastName, u1.usernameUsers as CreatorUsername, 
                    u2.firstName as EditorFirstName, u2.lastName as EditorLastName, u2.usernameUsers as EditorUsername
                    FROM am_projects.project_components pc
                    LEFT JOIN am_projects.projects p ON pc.ProjectId = p.ID
                    LEFT JOIN arpadmedia.users u1 ON pc.AddedByUID = u1.idUsers
                    LEFT JOIN arpadmedia.users u2 ON pc.EditedByUID = u2.idUsers
                    WHERE pc.ID=" . $taskID . ";";
        }

        $connection = Database::runQuery_mysqli(self::$schema);
        $result = $connection->query($sql);

        if ($taskID != null) {
            $row = $result->fetch_assoc();
            if ($row == null) {
                $connection->close();
                return 404;
            }
            $connection->close();

            // Get the creator UID and editor UID of the task
            $creatorUID = $row['AddedByUID'];

            // Get the project deadline and manager UID
            $projectManagerUID = $row['managerUID'];

            //Check if user is allowed to delete the task
            if (in_array("admin", $_SESSION['groups'])) {
                $row['canDelete'] = 1;
            } else {
                if ($creatorUID == $_SESSION['userId'] || $projectManagerUID == $_SESSION['userId']) {
                    $row['canDelete'] = 1;
                } else {
                    $row['canDelete'] = 0;
                }
            }


            // Check if the task is a single answer task and the user is trying to edit it
            if ($row['SingleAnswer'] == 1 && $_POST['fillOut'] == 'false' && !in_array("admin", $_SESSION['groups'])) {
                // If the task is a checklist or radio task, only the creator can edit it (unless the user is an admin)
                if ($row['Task_type'] == "checklist" || $row['Task_type'] == "radio") {
                    if ($creatorUID == $_SESSION['userId'] || $projectManagerUID == $_SESSION['userId']) {
                        return json_encode($row);
                    } else {
                        return 403;
                    }
                } else {
                    return json_encode($row);
                }
            } else {
                return json_encode($row);
            }

        } else {
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            if ($rows == null) {
                return 404;
            }
            $connection->close();
            return json_encode($rows);
        }
        
    }

    static function saveTask()
    {
        $settings = json_decode($_POST['task'], true);

        // Prevent XSS attacks
        $settings['Task_title'] = htmlspecialchars($settings['Task_title']);
        $settings['fillOutText'] = htmlspecialchars($settings['fillOutText']);

        array_walk_recursive($settings['Task_data'], function (&$item) {
            $item = htmlspecialchars($item);
        });
        $settings['Task_data'] = json_encode($settings['Task_data']);


        $connection = Database::runQuery_mysqli(self::$schema);

        if ($settings['Deadline'] != "NULL") {
            $sql = "INSERT INTO `project_components` (`ID`, `ProjectId`, `Task_type`, `Task_title`, `Task_data`, `isInteractable`, `fillOutText`, `SingleAnswer`, `AddedByUID`, `Deadline`, `EditedByUID`) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) 
                    ON DUPLICATE KEY UPDATE `Task_title`=?, `Task_data`=?, `Deadline`=?, `isInteractable`=?, `fillOutText`=?, `SingleAnswer`=?, `EditedByUID`=?;";
            $stmt = $connection->prepare($sql);
            $stmt->bind_param("iisssisiisisssisii", $_POST['ID'], $settings['ProjectId'], $settings['Task_type'], $settings['Task_title'], $settings['Task_data'], $settings['isInteractable'], $settings['fillOutText'], $settings['singleAnswer'], $_SESSION['userId'], $settings['Deadline'], $_SESSION['userId'], $settings['Task_title'], $settings['Task_data'], $settings['Deadline'], $settings['isInteractable'], $settings['fillOutText'], $settings['singleAnswer'], $_SESSION['userId']);
        } else {
            $sql = "INSERT INTO `project_components` (`ID`, `ProjectId`, `Task_type`, `Task_title`, `Task_data`, `isInteractable`, `fillOutText`, `SingleAnswer`, `AddedByUID`, `Deadline`, `EditedByUID`) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NULL, ?) 
                    ON DUPLICATE KEY UPDATE `Task_title`=?, `Task_data`=?, `Deadline`=NULL, `isInteractable`=?, `fillOutText`=?, `SingleAnswer`=?, `EditedByUID`=?;";
            $stmt = $connection->prepare($sql);
            $stmt->bind_param("iisssisiiissisii", $_POST['ID'], $settings['ProjectId'], $settings['Task_type'], $settings['Task_title'], $settings['Task_data'], $settings['isInteractable'], $settings['fillOutText'], $settings['singleAnswer'], $_SESSION['userId'], $_SESSION['userId'], $settings['Task_title'], $settings['Task_data'], $settings['isInteractable'], $settings['fillOutText'], $settings['singleAnswer'], $_SESSION['userId']);
        }

        $stmt->execute();
        // Check if a new record was inserted
        if ($stmt->affected_rows === 1) {
            // Get the ID of the newly inserted record
            $taskID = $connection->insert_id;
        } else {
            $taskID = $_POST['ID'];
        }

        try {
            // Get current task members
            $sql = "SELECT * FROM `project_task_members` WHERE TaskId=" . $taskID . ";";
            $result = $connection->query($sql);
            $currentMembers = array();
            while ($row = $result->fetch_assoc()) {
                $currentMembers[] = $row['UserId'];
            }

            $_POST['taskMembers'] = json_decode($_POST['taskMembers'], true);

            foreach ($_POST['taskMembers'] as $member) {
                // Check if the record already exists
                if (in_array($member, $currentMembers)) {
                    continue;
                }

                // If the record doesn't exist, insert it
                $sql = "INSERT INTO `project_task_members` (`ProjectId`,`TaskId`, `UserId`) VALUES (" . $settings['ProjectId'] . "," . $taskID . "," . $member . ")";
                $connection->query($sql);

                // Send an email to the new member
                // You would need to implement the ProjectMailer::sendNewTaskMail method
                //try {
                //    ProjectMailer::sendNewTaskMail($taskID, $member);
                //} catch (\Exception $e) {
                //    // Do nothing
                //}
            }

            // Delete members that were removed
            $deletedMembers = array_diff($currentMembers, $_POST['taskMembers']);
            foreach ($deletedMembers as $member) {
                $sql = "DELETE FROM `project_task_members` WHERE `TaskId`=" . $taskID . " AND `UserId`=" . $member . ";";
                $connection->query($sql);
            }
        } catch (\Exception $e) {
            // Do nothing
        }

        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {

            // If the task is an image task, upload the image
            $uploadResult = projectPictureManager::uploadImage($taskID, $_FILES['image']);
            if ($uploadResult != 200) {
                echo $uploadResult;
                exit();
            }

            if (strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION)) == "heic" || strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION) == "heif")) {
                $extension = "jpg";
            } else {
                $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            }

            $settings['Task_data'] = json_decode($settings['Task_data'], true);
            $settings['Task_data']['image'] = "./pictures/" . $taskID . "." . $extension;
            $settings['Task_data'] = json_encode($settings['Task_data']);

            $sql = "UPDATE `project_components` SET `Task_data`=? WHERE `ID`=?;";
            $stmt = $connection->prepare($sql);
            $stmt->bind_param("si", $settings['Task_data'], $taskID);
            $stmt->execute();
        }

        $connection->close();
        echo 200;
    }

    static function saveCheckOrRadio()
    {
        $connection = Database::runQuery_mysqli(self::$schema);

        $sql = "UPDATE `project_components` SET `Task_data`=? WHERE `ID`=?;";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("si", $_POST['Task_data'], $_POST['taskId']);
        $stmt->execute();

        $connection->close();
        echo 200;
    }

    static function saveTaskOrder($tasks)
    {
        $tasks = json_decode($tasks, true);
        $connection = Database::runQuery_mysqli(self::$schema);

        $sql = "UPDATE `project_components` SET `Position`=? WHERE `ID`=?;";
        $stmt = $connection->prepare($sql);

        foreach ($tasks as $item) {
            $stmt->bind_param("ii", $item['order'], $item['id']);
            $stmt->execute();
        }

        $connection->close();
        echo 200;
    }

    static function submitTask()
    {
        $connection = Database::runQuery_mysqli(self::$schema);

        // Getting project id for task
        $sql = "SELECT `ProjectId` FROM `project_components` WHERE `ID`=" . $_POST['ID'] . ";";
        $result = $connection->query($sql);
        $projectID = $result->fetch_assoc()['ProjectId'];

        $sql = "INSERT INTO `project_task_userdata` (`ProjectId`, `TaskId`, `UserId`, `Data`) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE `UserId`=?, `Data`=?;";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("iiisis", $projectID, $_POST['ID'], $_SESSION['userId'], $_POST['task'], $_SESSION['userId'], $_POST['task']);

        $stmt->execute();

        $connection->close();
        echo 200;
    }

    static function getUIs()
    {
        $sql = "SELECT * FROM `project_task_userdata` WHERE `TaskId`=" . $_POST['id'] . ";";
        $connection = Database::runQuery_mysqli(self::$schema);
        $result = $connection->query($sql);
        $connection->close();
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        if ($rows == null) {
            echo 404;
            exit();
        }
        echo (json_encode($rows));
    }

    static function getUI()
    {
        $sql = "SELECT * FROM `project_task_userdata` WHERE `TaskId`=" . $_POST['ID'] . " AND `UserId`=" . $_SESSION['userId'] . ";";
        $connection = Database::runQuery_mysqli(self::$schema);
        $result = $connection->query($sql);
        $connection->close();
        $row = $result->fetch_assoc();
        if ($row == null) {
            echo 404;
            exit();
        }
        echo (json_encode($row));

    }

    static function deleteTask()
    {
        $connection = Database::runQuery_mysqli(self::$schema);

        // If task was picture task, delete the picture
        $sql = "SELECT `Task_data` FROM `project_components` WHERE `ID`=" . $_POST['ID'] . ";";
        $result = $connection->query($sql);
        $taskData = json_decode($result->fetch_assoc()['Task_data'], true);

        if (in_array("admin", $_SESSION['groups'])) {
            $sql = "DELETE FROM project_components WHERE ID=" . $_POST['ID'] . ";";
            $connection->query($sql);
        } else {
            $sql = "SELECT `AddedByUID` FROM `project_components` WHERE `ID`=" . $_POST['ID'] . ";";
            $result = $connection->query($sql);
            $creatorUID = $result->fetch_assoc()['AddedByUID'];
            if ($creatorUID == $_SESSION['userId']) {
                $sql = "DELETE FROM project_components WHERE ID=" . $_POST['ID'] . ";";
                $connection->query($sql);
            } else {
                $connection->close();
                echo 403;
                exit();
            }
        }

        if ($taskData['image'] != '') {
            try {
                projectPictureManager::deleteImage($_POST['ID']);
            } catch (\Exception $e) {
                // Do nothing
            }
        }

        $connection->close();
        echo 200;
    }

    // PROJECT SETTINGS
    static function saveProjectSettings()
    {
        if (in_array("admin", $_SESSION['groups'])) {

            $settings = json_decode($_POST['settings'], true);

            // Prevent XSS attacks
            $settings['Name'] = htmlspecialchars($settings['Name']);
            $settings['Description'] = htmlspecialchars($settings['Description']);

            $connection = Database::runQuery_mysqli(self::$schema);

            if ($settings['Deadline'] == "NULL") {
                $sql = "UPDATE projects SET Name=?, Description=?, Deadline=NULL, Visibility_group=? WHERE ID=?";
            } else {
                $sql = "UPDATE projects SET Name=?, Description=?, Deadline=?, Visibility_group=? WHERE ID=?";
            }

            $stmt = $connection->prepare($sql);
            if ($settings['Deadline'] == "NULL") {
                $stmt->bind_param("sssi", $settings['Name'], $settings['Description'], $settings['Visibility_group'], $_POST['id']);
            } else {
                $stmt->bind_param("ssssi", $settings['Name'], $settings['Description'], $settings['Deadline'], $settings['Visibility_group'], $_POST['id']);
            }

            $stmt->execute();
            $stmt->close();
            $connection->close();
            return 200;
        } else {
            return 403;
        }
    }

    // Functions for users

    static function getUsers($UID = null)
    {
        if ($UID != null) {
            $sql = "SELECT `idUsers`, `firstName`, `lastName`, `usernameUsers` FROM `users` WHERE `idUsers`=" . $UID . ";";
        } else {
            $sql = "SELECT `idUsers`, `firstName`, `lastName` FROM `users` ORDER BY `lastName`, `firstName`;";
        }
        $connection = Database::runQuery_mysqli();
        $result = $connection->query($sql);
        $connection->close();
        $resultItems = array();
        while ($row = $result->fetch_assoc()) {
            $resultItems[] = $row;
        }

        if ($_POST['mode'] == "getUsers") {
            echo (json_encode($resultItems));
            exit();
        } else {
            return $resultItems;
        }
    }

    static function getTaskMembers()
    {

        $projectId = $_POST['proj_id'];
        $taskId = $_POST['task_id'];

        $sql = "SELECT pm.UserID, u.firstName, u.lastName, 
        IF(ptm.UserId IS NULL, 0, 1) as assignedToTask
        FROM am_projects.project_members pm
        LEFT JOIN arpadmedia.users u ON pm.UserID = u.idUsers
        LEFT JOIN am_projects.project_task_members ptm ON pm.UserID = ptm.UserId AND ptm.TaskId = $taskId
        WHERE pm.ProjectId = $projectId";

        $connection = Database::runQuery_mysqli(self::$schema);
        $result = $connection->query($sql);
        $resultItems = $result->fetch_all(MYSQLI_ASSOC);
        $connection->close();

        // Sort the array by lastname and firstname
        usort($resultItems, function ($a, $b) {
            return $a['lastName'] <=> $b['lastName'] ?: $a['firstName'] <=> $b['firstName'];
        });

        if ($_POST['mode'] == "getTaskMembers") {
            return json_encode($resultItems);
        }
        return $resultItems;
    }

    static function getUserTaskData()
    {
        $responseJSON = array();
        $responseJSON['isAdmin'] = in_array("admin", $_SESSION['groups']);


        if ($_POST['type'] == "card") {
            $sql = "SELECT * FROM `project_task_userdata` WHERE TaskId=" . $_POST['task_id'] . " AND UserId=" . $_SESSION['userId'] . ";";
            $connection = Database::runQuery_mysqli(self::$schema);
            $result = $connection->query($sql);
            $row = $result->fetch_assoc();
            if ($row == null) {
                $responseJSON['filled'] = false;
                $taskMembers = self::getTaskMembers();
                // If the task has members
                if ($taskMembers != null) {

                    // Check if the user is a member of the task
                    $taskMembers = array_map(function ($item) {
                        if ($item['assignedToTask'] == 1) {
                            return intval($item['UserID']);
                        }
                    }, $taskMembers);

                    if (in_array($_SESSION['userId'], $taskMembers)) {
                        $responseJSON['isTaskMember'] = true;
                    } else {
                        $responseJSON['isTaskMember'] = false;
                    }
                } else {
                    // If the task has no members
                    $responseJSON['isTaskMember'] = false;
                }
            } else {
                $responseJSON['isTaskMember'] = true;
                $responseJSON['data'] = $row;
                $responseJSON['filled'] = true;
            }

        } else if ($_POST['type'] == "get") {
            $sql = "SELECT * FROM `project_task_userdata` WHERE TaskId=" . $_POST['task_id'] . ";";
            $connection = Database::runQuery_mysqli(self::$schema);
            $result = $connection->query($sql);
            $connection->close();
            $rows = array();
            foreach ($result as $row) {
                $rows[] = $row;
            }
            $responseJSON['data'] = $rows == null ? null : $rows;
        }
        return json_encode($responseJSON);
    }

    static function getProjectMembers($projectID)
    {
        $sql = "SELECT * FROM `project_members` WHERE ProjectID=" . $projectID . ";";
        $connection = Database::runQuery_mysqli(self::$schema);
        $result = $connection->query($sql);
        $connection->close();
        $resultItems = array();
        while ($row = $result->fetch_assoc()) {
            $resultItems[] = $row;
        }
        if ($_POST['mode'] != "getProjectMembers") {
            return $resultItems;
        }

        // Get project manager
        $sql = "SELECT `managerUID` FROM `projects` WHERE `ID`=" . $projectID . ";";
        $connection = Database::runQuery_mysqli(self::$schema);
        $managerUID = $connection->query($sql)->fetch_assoc()['managerUID'];
        $connection->close();

        // Getting the names of the users
        $connection = Database::runQuery_mysqli();
        foreach ($resultItems as $key => $item) {
            $sql = "SELECT `firstName`, `lastName` FROM `users` WHERE `idUsers`=" . $item['UserID'] . ";";
            $result = $connection->query($sql);
            $user = $result->fetch_assoc();
            $resultItems[$key]['firstName'] = $user['firstName'];
            $resultItems[$key]['lastName'] = $user['lastName'];
            $resultItems[$key]['isManager'] = $managerUID == $item['UserID'] ? 1 : 0;
        }

        // Sort the array by lastname and firstname
        usort($resultItems, function ($a, $b) {
            return $a['lastName'] <=> $b['lastName'] ?: $a['firstName'] <=> $b['firstName'];
        });

        echo (json_encode($resultItems));
    }

    static function saveProjectMembers()
    {
        // For every member in array add to database
        $members = json_decode($_POST['Members'], true);

        $connection = Database::runQuery_mysqli(self::$schema);
        // Get current members
        $sql = "SELECT * FROM `project_members` WHERE ProjectID=" . $_POST['id'] . ";";
        $result = $connection->query($sql);
        $currentMembers = array();
        while ($row = $result->fetch_assoc()) {
            $currentMembers[] = $row['UserID'];
        }

        $deletedMembers = array_diff($currentMembers, $members);
        foreach ($deletedMembers as $member) {
            self::removeMemberFromProject($member, $_POST['id']);
        }

        foreach ($members as $member) {
            // Check if the record already exists
            if (in_array($member, $currentMembers)) {
                continue;
            }

            // If the record doesn't exist, insert it
            $sql = "INSERT INTO `project_members` (`ProjectID`, `UserID`) VALUES (" . $_POST['id'] . "," . $member . ")";
            $connection->query($sql);

            // Send an email to the new member
            try {
                ProjectMailer::sendNewProjectMail($_POST['id'], $member);
            } catch (\Exception $e) {
                // Print to log
                print_r($e);
            }
        }

        $connection->close();

        echo 200;
    }


    static function changeManager($projectId, $newManagerId)
    {
        if (in_array("admin", $_SESSION['groups'])) {
            $connection = Database::runQuery_mysqli(self::$schema);

            $sql = "UPDATE projects SET managerUID=" . $newManagerId . " WHERE ID=" . $projectId . ";";
            $connection->query($sql);
            $connection->close();
            return 200;
        } else {
            return 403;
        }
    }

    static function removeMemberFromProject($userId, $projectId)
    {
        if (in_array("admin", $_SESSION['groups'])) {
            $connection = Database::runQuery_mysqli(self::$schema);

            // Check if the user is not the manager of the project
            $sql = "SELECT `managerUID` FROM `projects` WHERE `ID`=" . $projectId . ";";
            $result = $connection->query($sql);
            $managerUID = $result->fetch_assoc()['managerUID'];
            if ($managerUID == $userId) {
                return 403;
            }

            $sql = "DELETE FROM project_members WHERE ProjectID=" . $projectId . " AND UserID=" . $userId . ";";
            $connection->query($sql);
            $connection->close();
            return 200;
        } else {
            return 403;
        }
    }

    // NAS THINGS

    static function saveNASPath($path, $projectID)
    {
        if (in_array("admin", $_SESSION['groups'])) {
            $connection = Database::runQuery_mysqli(self::$schema);

            $sql = "UPDATE projects SET NAS_path='" . $path . "' WHERE ID=" . $projectID . ";";
            $connection->query($sql);
            $connection->close();
            return 200;
        } else {
            return 403;
        }
    }
}

if (isset($_POST['mode'])) {

    switch ($_POST['mode']) {
        case 'createNewProject':
            echo projectManager::createNewProject();
            break;
        case 'deleteProject':
            echo projectManager::deleteProject();
            break;
        case 'archiveProject':
            echo projectManager::archiveProject();
            break;
        case 'restoreProject':
            echo projectManager::restoreProject($_POST['projectID']);
            break;

        case 'checkForUpdates':
            echo projectManager::checkForUpdates();
            break;
        case 'listProjects':
            echo projectManager::listProjects($_POST['archived']);
            break;
        case 'getProject':
            echo projectManager::getProject();
            break;

        case 'getProjectTask':
            echo projectManager::getProjectTask($_POST['task_id']);
            break;

        case 'getProjectRoot':
            echo projectManager::getProjectRoot();
            break;

        case 'saveTask':
            echo projectManager::saveTask();
            break;
        case 'saveCheckOrRadio':
            echo projectManager::saveCheckOrRadio();
            break;
        case 'saveTaskOrder':
            echo projectManager::saveTaskOrder($_POST['tasks']);
            break;
        case 'submitTask':
            echo projectManager::submitTask();
            break;

        case 'getUI':
            echo projectManager::getUI();
            break;
        case 'getUIs':
            echo projectManager::getUIs();
            break;
        case 'deleteTask':
            echo projectManager::deleteTask();
            break;

        case 'saveProjectSettings':
            echo projectManager::saveProjectSettings();
            break;

        case 'getUsers':
            echo projectManager::getUsers($_POST['ID']);
            break;
        case 'getTaskMembers':
            echo projectManager::getTaskMembers();
            break;
        case 'getUserTaskData':
            echo projectManager::getUserTaskData();
            break;
        case 'getProjectMembers':
            echo projectManager::getProjectMembers($_POST['id']);
            break;
        case 'saveProjectMembers':
            echo projectManager::saveProjectMembers();
            break;
        case 'changeManager':
            echo projectManager::changeManager($_POST['projectId'], $_POST['newManagerId']);
            break;
        case 'removeMemberFromProject':
            echo projectManager::removeMemberFromProject($_POST['userId'], $_POST['projectId']);
            break;

        case 'saveNASPath':
            echo projectManager::saveNASPath($_POST['path'], $_POST['projectID']);
            break;
    }
    exit();
}