<?php
namespace Mediaio;


session_start();


error_reporting(E_ALL ^ E_NOTICE);

if (!isset($_SESSION["userId"])) {
  echo "<script>window.location.href = '../../index.php?error=AccessViolation';</script>";
  exit();
}

// Prevent unauthorized access
if (!in_array("admin", $_SESSION["groups"])) {
  echo "Nincs jogosultságod az oldal megtekintéséhez!";
  exit();
}
include "header.php";
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
  <?php include "modals.php"; ?>

  <h1 class="rainbow">Jóváhagyás</h1>

  <div class="container">
    <div class="row justify-content-center">
      <div id="confirmEvents">
        
      </div>
    </div>
  </div>

</body>


<script>
  function acceptEvent(n) {
    //alert('elfogadas');
    var items = JSON.parse($('#event' + n)[0].cells[4].innerHTML);
    var itemsJSON = JSON.stringify(items);
    var eventType = $('#event' + n)[0].cells[3].innerHTML;
    var date = $('#event' + n)[0].cells[0].innerHTML;
    var user = $('#event' + n)[0].cells[1].innerHTML;
    var mode;
    if (eventType == 'IN') {
      mode = 'retrieveApproval';
    } else {
      mode = 'takeOutApproval';
    }
    console.log(items);
    $.ajax({
      method: 'POST',
      url: '../ItemManager.php',
      data: {
        data: itemsJSON,
        mode: mode,
        value: true,
        date: date,
        user: user
      }, //value true means event is approved.
      success: function (response) {
        if (response == 200) {
          //Remove event from the table.
          $('#event' + n).fadeOut();
          setTimeout(function () {
            location.reload();
          }, 500);
        } else {
          console.log("Backend error.");
          alert(response);
        }
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        alert("Status: " + textStatus);
        alert("Error: " + errorThrown);
      }
    });
  }

  function declineEvent(n) {
    var items = JSON.parse($('#event' + n)[0].cells[4].innerHTML);
    // items.forEach(element => {
    //   console.log(element);
    //   element=element.split('(');
    // });
    var itemsJSON = JSON.stringify(items);
    var eventType = $('#event' + n)[0].cells[3].innerHTML;
    var date = $('#event' + n)[0].cells[0].innerHTML;
    var mode;
    var user = $('#event' + n)[0].cells[1].innerHTML;
    if (eventType == 'IN') {
      mode = 'retrieveApproval';
    } else {
      mode = 'takeOutApproval';
    }
    //alert(items.stringify());
    $.ajax({
      method: 'POST',
      url: '../ItemManager.php',
      data: {
        data: itemsJSON,
        mode: mode,
        value: false,
        date: date,
        user: user
      }, //event declined.
      success: function (response) {
        //alert(response)
        if (response == 200) {
          //Remove event from the table.
          $('#event' + n).fadeOut();
          location.reload();
        } else {
          console.log("Backend error.");
          alert(response);
        }
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        alert("Status: " + textStatus);
        alert("Error: " + errorThrown);
      }
    });
  }

  function openEventDocument(n) {
    window.location.replace("../utility/damage_report/announce_Damage.php");
  }
</script>