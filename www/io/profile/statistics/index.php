<?php
namespace Mediaio;

error_reporting(E_ERROR | E_PARSE);

session_start();

if (!isset($_SESSION["userId"])) {
  echo "<script>window.location.href = '../../index.php?error=AccessViolation';</script>";
  exit();
}

if (!in_array("admin", $_SESSION["groups"])) {
  echo "<script>window.location.href = '../404.html';</script>";
  exit();
}

include "header.php";
include "../../Accounting.php";
include "../../ItemManager.php";
?>


<nav class="navbar sticky-top navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand" href="../index.php">
    <img src="../../utility/logo2.png" height="50">
  </a>
  <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
    aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto navbarUl">
      <script>
        $(document).ready(function () {
          menuItems = importItem("../../utility/menuitems.json");
          drawMenuItemsLeft('profile', menuItems, 3);
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
    <form method='post' class="form-inline my-2 my-lg-0" action=../../utility/userLogging.php>
      <button id="logoutBtn" class="btn btn-danger my-2 my-sm-0 logout-button" name='logout-submit'
        type="submit">Kijelentkezés</button>
      <script type="text/javascript">
        window.onload = function () {
          display = document.querySelector('#time');
          var timeUpLoc = "../../utility/userLogging.php?logout-submit=y"
          startTimer(display, timeUpLoc);
        };
      </script>
    </form>
  </div>
</nav>

<body>

  <!-- Accept settings modal -->

  <div class="modal fade" id="SettingsModal" tabindex="-1" aria-labelledby="SettingsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="SettingsModalLabel">Részletek</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <ul id="itemsList"></ul>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-success" data-bs-dismiss="modal">Ok</button>
        </div>
      </div>
    </div>
  </div>




  <h1 class="rainbow">Statisztika</h1>
  <div class="container">
    <div class="col-3 text-center mb-2">
      <div class="btn-group mb-2" role="group" aria-label="Basic radio toggle button group">
        <input type="radio" class="btn-check" name="btnradio" id="itemStats" autocomplete="off" checked>
        <label class="btn btn-outline-secondary" for="itemStats">Tárgyak</label>

        <input type="radio" class="btn-check" name="btnradio" id="userStats" autocomplete="off">
        <label class="btn btn-outline-secondary" for="userStats">Felhasználók</label>
      </div>
      <div class="accordion">
        <div class="accordion-item">
          <h2 class="accordion-header">
            <button class="accordion-button collapsed" id="statsButton" type="button" data-bs-toggle="collapse"
              data-bs-target="#statsNum" aria-expanded="true" aria-controls="statsNum">
              Adatok
            </button>
          </h2>
          <div id="statsNum" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
            <div class="accordion-body">

              <div class="card mb-3">
                <div class="card-header" style="font-weight: bold;">
                  Felhasználók
                </div>
                <div class="card-body d-flex justify-content-between">
                  <span class="infoText"><?php echo getUserCount(); ?> fő</span><button class="btn" type="button"
                    onclick="window.location.href = '../userlist.php';"><i class="fas fa-users"></i></button>
                </div>
              </div>
              <div class="card mb-3">
                <div class="card-header" style="font-weight: bold;">
                  Benn levő tárgyak
                </div>
                <div class="card-body">
                  <span class="infoText"><?php echo getItemCount(); ?></span>
                </div>
              </div>
              <!-- <div class="card mb-3">
                <div class="card-body">
                  <h5 class="card-title">Események: <?php //echo getEventCount() ?></h5>
                </div>
              </div> -->

            </div>
          </div>
          <script>
            if (window.innerWidth > 768) {
              document.getElementById("statsButton").click();
            }
          </script>
        </div>
      </div>
    </div>
    <div class="col">
      <div id="eventsContainer">

      </div>
    </div>
    <!-- Navigation back to top -->
    <div id='toTop'><i class="fas fa-chevron-up"></i></div>
  </div>
</body>



<?php

function getUserCount()
{
  $users = Accounting::getPublicUserInfo();
  //Count the number of users
  $userCount = count(json_decode($users));
  return $userCount;
}


function getItemCount()
{
  $items = itemDataManager::getItems();
  //Count the number of items
  $itemCount = count(json_decode($items));

  // Get taken items
  $takenItems = itemDataManager::listByCriteria("out", "id");
  $takenItemCount = count(json_decode($takenItems));
  $itemsIn = $itemCount - $takenItemCount;

  $precent = round(($itemsIn / $itemCount) * 100, 2);

  return "$itemsIn / $itemCount ($precent%)";
}


function getEventCount()
{
  $events = itemHistoryManager::getInventoryHistory();
  //Count the number of events
  $eventCount = count(json_decode($events));
  return $eventCount;
}