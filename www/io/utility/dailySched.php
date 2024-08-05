<?php
//    A file which runs daily 
//    Includes jobs like automatic email notifications,
//    updating the database, etc.

namespace Mediaio;

// Include the necessary files
require_once '../ItemManager.php';
require_once '../ProjectMailer.php';
require_once '../Mailer.php';
require_once '../Accounting.php';

// Set the time zone to Budapest
date_default_timezone_set('Europe/Budapest');

class DailySchedule
{

    /*
        The following functions are for the projectManaging system
        ---------------------------------------------------
    */

    static function projectDeadlineReminder()
    {
        // Get the projects which are due today
        // Get all the projects
        $projects = projectManager::listProjects();
        $projects = json_decode($projects, true);

        // Current date
        $currentDate = new \DateTime();
        $currentDate = $currentDate->format('Y-m-d');

    }

    static function projectTaskDeadlineReminder()
    {
        // Get all the projects
        $projects = projectManager::listProjects();
        $projects = json_decode($projects, true);

        // Current date
        $currentDate = new \DateTime();
        $currentDate = $currentDate->format('Y-m-d');

        // Loop through the projects
        foreach ($projects as $project) {
            // Get the tasks of the project
            $tasks = projectManager::getProjectTask();
            $tasks = json_decode($tasks, true);

            // Loop through the tasks
            foreach ($tasks as $task) {
                // Check if the task is due today
                $taskDeadline = new \DateTime($task['Deadline']);
                $taskDeadlineDate = $taskDeadline->format('Y-m-d');

                if ($taskDeadlineDate == $currentDate) {
                    // TODO: Send an email to the members of the project
                }
            }

        }

    }

    /*
        The following functions are for the admin statistics system
        TODO: Implement the functions
    */

}

// Testing purposes
/*if (isset($_GET['mode'])) {
    switch ($_GET['mode']) {
        case 'plannedTakeoutReminder':
            DailySchedule::plannedTakeoutReminder();
            break;
        case 'notInitiatedTakeoutDisable':
            DailySchedule::notInitiatedTakeoutDisable();
            break;
    }
    exit();
}
*/

// Run the planned takeout reminder
//DailySchedule::projectDeadlineReminder();
//DailySchedule::projectTaskDeadlineReminder();
