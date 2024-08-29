<?php

namespace Mediaio;

use Mediaio\Database;

session_start();

if (!isset($_SESSION["userId"])) {
  echo "<script>window.location.href = '../index.php?error=AccessViolation';</script>";
  exit();
}

if (!in_array("admin", $_SESSION["groups"])) {
  echo "<script>window.location.href = '../404.html';</script>";
  exit();
}
include "header.php";
?>

<!-- If user is logged in -->
<nav class="navbar sticky-top navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand" href="../index.php">
    <img src="../utility/logo2.png" height="50">
  </a>

  <!-- Mobile Navigation - Additional toggle button -->
  <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
    aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <!-- Main Navigation -->
  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto navbarUl">
      <script>
        $(document).ready(function () {
          menuItems = importItem("../utility/menuitems.json");
          drawMenuItemsLeft('pathfinder', menuItems, 2);
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

<body>
  <h1 class="rainbow">Tárgy története</h1>


  <div class="container">
    <div class="row justify-content-center">
      <div class="searchField">
        <div class="input-group">
          <input type="text" class="form-control" placeholder="Keresés" aria-label="Tárgy keresése" id="search" autocomplete="neautofilleljlégyszi">
          <button class="btn btn-outline-success" type="button" id="submitLog">Keresés</button>
        </div>
        <div class="list-group" id="itemsList">

        </div>
      </div>
    </div>
    <div class="row justify-content-center">
      <div class="itemTableHolder">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h3 class="panel-title" id="itemTitle"></h3>
          </div>
          <div class="panel-body">
            <table class="table table-bordered" id="itemHistoryTable">
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

</body>

</html>