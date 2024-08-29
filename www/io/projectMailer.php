<?php
namespace Mediaio;

require_once __DIR__ . '/Database.php';
use Mediaio\Database;

error_reporting(E_ERROR | E_PARSE);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


class ProjectMailer
{
    private static $schema = "am_projects";
    public static function sendMail($to, $subject, $message)
    {
        require_once __DIR__ . '/Mailer.php';
        MailService::sendContactMail($to, $subject, $message);
    }

    public static function sendNewProjectMail($project_id, $member)
    {
        $connection = Database::runQuery_mysqli(self::$schema);
        $sql = "SELECT `Name`,`managerUID` FROM `projects` WHERE `ID`='" . $project_id . "';";
        $result = $connection->query($sql);

        $row = $result->fetch_assoc();
        $projectName = $row['Name'];
        $managerUID = $row['managerUID'];
        $connection->close();

        $connection = Database::runQuery_mysqli();
        // Send an email to the new member
        $sql = "SELECT `emailUsers`, `firstName` FROM `users` WHERE `idUsers`='" . $member . "';";
        $result = $connection->query($sql);
        $row = $result->fetch_assoc();
        $email = $row['emailUsers'];
        $name = $row['firstName'];


        $sql = "SELECT `firstName`, `lastName` FROM `users` WHERE `idUsers`=" . $managerUID . ";";
        $result = $connection->query($sql);
        $row = $result->fetch_assoc();
        $managerName = $row['lastName'] . " " . $row['firstName'];
        $connection->close();
    

        //E-mail küldése a felhasználónak
        $message = '
                    <html>
                    <head>
                    <title>Árpad Média IO</title>
                    </head>
                    <body>
                    <h3>Kedves ' . $name . '!</h3>
                    <p>Hozzá lettél adva a(z) "' . $projectName . '" projekthez.</p>
                    
                    <h5>Projekt vezető: <br> ' . $managerName . '</h5>
                    <i>Ez egy tájékoztató üzenet, kérlek ne válaszolj rá!</i>
                    </body>
                    </html>
                    ';
        $subject = "Hozzá lettél adva a(z) " . $projectName . " projekthez.";

        self::sendMail($email, $subject, $message);
    }

    /* public static function sendProjectDeadlineMailToAll($project_id)
    {
        $connection = Database::runQuery_mysqli(self::$schema);

        // Get the project name and deadline
        $sql = "SELECT `Name`, `Deadline` FROM `projects` WHERE `ID`=" . $project_id . ";";
        $result = $connection->query($sql);
        $project = $result->fetch_assoc();
        $projectName = $project['Name'];
        $deadline = $project['Deadline'];
        $connection->close();

        $connection = Database::runQuery_mysqli();

        // Get the members of the project
        $sql = "SELECT `idUsers` FROM `projectmembers` WHERE `ProjectID`=" . $project_id . ";";
        $result = $connection->query($sql);
        $members = $result->fetch_all();

        $subject = "Projekt határidő emlékeztető";

        // Get current time
        $currentTime = date("Y-m-d H:i:s");
        if ($currentTime < $deadline) {
            $message = "A(z) " . $projectName . " projekt a határideje " . $deadline . ".\n\n";
        } else {
            $message = "A(z) " . $projectName . " projekt határideje lejárt.\n\n";
        }

        // Send an email to all members
        foreach ($members as $member) {
            $sql = "SELECT `emailUsers`, `firstName` FROM `users` WHERE `idUsers`=" . $member[0] . ";";
            $result = $connection->query($sql);
            $row = $result->fetch_assoc();
            $email = $row['emailUsers'];
            $name = $row['firstName'];

            $message .= "Kedves " . $name . "! \n" . $message;
            self::sendMail($email, $subject, $message);
        }

        $connection->close();
    } */

}