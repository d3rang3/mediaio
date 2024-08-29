<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
if (!isset($_SESSION["userId"])) {
  echo "<script>window.location.href = '../index.php?error=AccessViolation';</script>";
  exit();
}
include ("header.php");
?>

<html>
<nav class="navbar sticky-top navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand" href="../index.php">
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

<body>
  <h1 class="rainbow">Opciók</h1>
  <table class="help-logintable">
    <tr>
      <td>
        <form action="notifications.php"><button class="btn btn-secondary w-100">Értesítések <i
              class="fas fa-mail-bulk"></i></button></form>
      </td>
    </tr>
    <tr>
      <td>
        <form action="userlist.php"><button class="btn btn-dark w-100">Elérhetőségek <i
              class="fas fa-address-book"></i></button></form>
      </td>
    </tr>
    <tr>
      <td>
        <form action="rules.php"><button class="btn btn-dark w-100">Dokumentumok <i
              class="fas fa-folder-open"></i></button></form>
      </td>
    </tr>
    <tr>
      <td>
        <form action="../utility/damage_report/announce_Damage.php"><button class="btn btn-danger w-100">Sérülés
            bejelentése
            <i class="fas fa-file-alt"></i></button></form>
      </td>
    </tr>
    <tr>
      <td>
        <form action="chPwd.php"><button class="btn btn-warning w-100">Jelszócsere <i class="fas fa-key"></i></button>
        </form>
      </td>
    </tr>
    <?php
    if (in_array("admin", $_SESSION["groups"])): ?>
      <tr>
        <td>
          <form action="../pathfinder"><button class="btn btn-secondary w-100">Tárgy története <i
                class="fas fa-project-diagram"></i></button></form>
        </td>
      </tr>
      <tr>
        <td>
          <form action="../utility/damage_report/service.php"><button
              class="btn btn-warning position-relative w-100">Szerviz <i class="fas fa-wrench"></i></i> <span
                id="serviceItemCount"
                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                0<span class="visually-hidden">unread messages</span></button></form>
        </td>
      </tr>
      <tr>
        <td>
          <form action="./transConfirm"><button class="btn btn-success position-relative w-100">Jóváhagyás <i
                class="fas fa-user-check"></i> <span id="usercheckItemCount"
                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                0
                <span class="visually-hidden">unread messages</span></button></form>
        </td>
      </tr>
      <tr>
        <td>
          <form action="./statistics"><button class="btn btn-dark w-100">Statisztika <i
                class="fas fa-chart-pie"></i></i></button></form>
        </td>
      </tr>

    <?php endif;
    if (in_array("system", $_SESSION["groups"]) or in_array("teacher", $_SESSION["groups"])): ?>
      <!-- <tr>
        <td>
          <form action="../budget/">
            <button class="btn btn-info w-100" disabled>Költségvetés <i class="fas fa-coins"></i></button>
          </form>
        </td>
      </tr>
      <tr>
        <td>
          <form action="points.php">
            <button class="btn btn-success w-100" disabled>Pontszámok <i class="fas fa-calculator"></i></button>
          </form>
        </td>
      </tr> -->
    <?php endif;
    if (in_array("system", $_SESSION["groups"])): ?>
      <tr>
        <td>
          <form action="./roles/index.php">
            <button class="btn btn-danger w-100">Engedélyek módosítása <i class="fas fa-radiation"></i></button>
          </form>
        </td>
      </tr>
      <tr>
        <td>
          <form action="./loginPageSettings.php">
            <button class="btn btn-danger w-100">Motd/Belépések korlátozása <i class="fas fa-user-shield"></i></button>
          </form>
        </td>
      </tr>
    <?php endif; ?>
  </table>

</html>

<?php
if (in_array("admin", $_SESSION["groups"])): ?>
  <script>
    //Make a Jquery call to the ItemManager.php to get the number of items in the service

    $.ajax({
      url: "../ItemManager.php",
      type: "POST",
      data: {
        mode: "getProfileItemCounts",
      },
      success: function (data) {
        var dataArray = data.split(",");
        //Set the service item count
        document.getElementById("serviceItemCount").innerHTML = dataArray[0];
        //Set the user check item count
        document.getElementById("usercheckItemCount").innerHTML = dataArray[1];

      }

    });

  </script>

<?php endif; ?>