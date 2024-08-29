<?php

use Mediaio\Database;

//Suppresses error messages
error_reporting(E_ERROR | E_PARSE);

session_start();
if (!isset($_SESSION["userId"])) {
  echo "<script>window.location.href = '../index.php?error=AccessViolation';</script>";
  exit();
}

include ("header.php");
?>


<body>
  <nav class="navbar sticky-top navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="index.php">
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

  <h2 class="rainbow">Elérhetőségek</h2>

  <div class="container">
    <div class="statsTable" id="tableContainer">

    </div>
  </div>

</body>

<style>
  .statsTable {
    max-height: 80dvh;
    overflow: auto;

    &::-webkit-scrollbar-track {
      background: rgb(255, 255, 255);
      /* color of the tracking area */
    }

    &::-webkit-scrollbar {
      width: 8px;
      height: 8px;
      /* width of the entire scrollbar */
    }

    &::-webkit-scrollbar-thumb {
      background-color: rgb(179, 179, 179);
      /* color of the scroll thumb */
      border-radius: 20px;
    }
  }
</style>


<script>
  $(document).ready(function () {
    loadTable();
  });


  async function loadTable() {
    const users = JSON.parse(await $.ajax({
      url: "../Accounting.php",
      type: "POST",
      data: {
        mode: "getPublicUserInfo",
      },
    }));


    console.log(users);

    const table = document.createElement("table");
    table.id = "itemTable";
    table.className = "table table-striped table-bordered table-hover";

    const header = table.createTHead();
    const headerRow = header.insertRow(0);
    const headers = ["Vezetéknév", "Keresztnév", "Felhasználónév", "E-mail cím", "Telefonszám"];
    headers.forEach((header, index) => {
      const th = document.createElement("th");
      th.innerHTML = header;
      //th.style.cursor = "pointer";
      th.setAttribute("data-header", header);
      headerRow.appendChild(th);
    });

    const body = table.createTBody();
    users.forEach((item) => {
      const row = body.insertRow(-1);
      const cellValues = [
        item.lastName,
        item.firstName,
        item.usernameUsers,
        `<a href="mailto:${item.emailUsers}">${item.emailUsers}</a>`,
        `<a href="tel:${item.teleNum}">${item.teleNum}</a>`
      ];
      cellValues.forEach((value, index) => {
        const cell = row.insertCell(index);
        cell.innerHTML = value;
      });
    });


    document.getElementById("tableContainer").innerHTML = "";
    document.getElementById("tableContainer").appendChild(table);

  }
</script>