<?php
use Mediaio\Accounting;

require_once '../Accounting.php';

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
if (!isset($_SESSION["userId"])) {
  echo "<script>window.location.href = '../index.php?error=AccessViolation';</script>";
  exit();
}
include ("header.php");

getNotificationSettings();
global $notif_settings;
?>

<!-- Info toast -->
<div class="toast-container fixed-bottom start-50 translate-middle-x p-3" style="z-index: 9999;">
  <div class="toast" id="infoToast" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-header">
      <img src="../logo.ico" class="rounded me-2" alt="..." style="height: 20px; filter: invert(1);">
      <strong class="me-auto" id="infoToastTitle">Beállítások</strong>
      <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body">
    </div>
  </div>
</div>

<body>
  <nav class="navbar sticky-top navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="./index.php">
      <img src="../utility/logo2.png" height="50">
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
      aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav mr-auto navbarUl">
        <script>
          $(document).ready(function () {
            menuItems = importItem("../utility/menuitems.json");
            drawMenuItemsLeft('profile', menuItems, 2);
          });
        </script>
      </ul>
      <ul class="navbar-nav ms-auto navbarPhP">
        <li>
          <a class="nav-link disabled timelock" href="#"><span id="time"> 10:00 </span>
            <?php echo ' ' . $_SESSION['UserUserName']; ?>
          </a>
        </li>
      </ul>
      <form method='post' class="form-inline my-2 my-lg-0" action=../utility/userLogging.php>
        <button id="logoutBtn" class="btn btn-danger my-2 my-sm-0 logout-button" name='logout-submit'
          type="submit">Kijelentkezés</button>
        <script type="text/javascript">
          window.onload = function () {
            display = document.querySelector('#time');
            var timeUpLoc = "../utility/userLogging.php?logout-submit=y"
            startTimer(display, timeUpLoc);
          };
        </script>
      </form>
    </div>
  </nav>

  <h1 class="rainbow">Értesítések</h1>

  <div class="container d-flex justify-content-center">
    <div class="d-flex justify-content-center flex-column" style="font-size: 18px;">
      <div class="form-check form-switch mb-1">
        <input class="form-check-input" type="checkbox" role="switch" id="newProject" <?php echo $notif_settings['newProject'] ? "checked" : null; ?>>
        <label class="form-check-label" for="newProject">Új projekt (része vagy)</label>
      </div>
      <div class="form-check form-switch mb-1">
        <input class="form-check-input" type="checkbox" role="switch" id="newTask" <?php echo $notif_settings['newTask'] ? "checked" : null; ?>>
        <label class="form-check-label" for="newTask">Új feladat hozzáadásakor</label>
      </div>
      <div class="form-check form-switch mb-1">
        <input class="form-check-input" type="checkbox" role="switch" id="taskEndReminder" <?php echo $notif_settings['taskEndReminder'] ? "checked" : null; ?>>
        <label class="form-check-label" for="taskEndReminder">Feladat lejárta előtt (1 nappal)</label>
      </div>

    </div>
  </div>

</body>

<script>
  $(document).ready(function () {

    let checks = document.getElementsByClassName('form-check-input');
    Array.from(checks).forEach(element => {
      element.addEventListener('change', () => {
        saveSettings();
      });
    });

  });


  let timeoutId;

  async function saveSettings() {
    // If the function is already scheduled, cancel it
    if (timeoutId) {
      clearTimeout(timeoutId);
    }

    // Schedule the function to run after 1 second (1000 milliseconds)
    timeoutId = setTimeout(async () => {
      const settings = {
        newProject: document.getElementById('newProject').checked,
        newTask: document.getElementById('newTask').checked,
        taskEndReminder: document.getElementById('taskEndReminder').checked
      }

      const response = await $.ajax({
        url: '../Accounting.php',
        type: 'POST',
        data: {
          mode: 'setNotificationSettings',
          settings: JSON.stringify(settings),
        }
      });

      if (response == 200) {
        successToast('Sikeres mentés');
      } else {
        serverErrorToast();
      }
    }, 500);
  }
</script>


<?php

function getNotificationSettings()
{
  global $notif_settings;

  $notif_settings = Accounting::getNotificationSettings();
  $notif_settings = json_decode($notif_settings, true);
  $notif_settings = json_decode($notif_settings, true);

  // Backward compatibility
  if (!isset($notif_settings['newProject'])) {
    $notif_settings['newProject'] = false;
  }
  if (!isset($notif_settings['newTask'])) {
    $notif_settings['newTask'] = false;
  }
  if (!isset($notif_settings['taskEndReminder'])) {
    $notif_settings['taskEndReminder'] = false;
  }
}