<?php

namespace Mediaio;


use Mediaio\Database;


require_once('./Database.php');
session_start();

if (!isset($_SESSION['userId'])) {
  header("Location: ./index.php?error=AccessViolation");
  exit();
}

include "header.php";
$SESSuserName = $_SESSION['UserUserName'];


function PhparrayCookie()
{
  array_push($selItems, $_COOKIE['id_itemNameAdd']);
  foreach ($selItems as $x) {
    echo $x . " ";
  }
}
?>
<script>
  var goStatus = 0;

</script>
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<html>
<?php if (isset($_SESSION["userId"])) { ?>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="index.php">
      <img src="./utility/logo2.png" height="50">
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
      aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav mr-auto navbarUl">
        <script>
          $(document).ready(function () {
            menuItems = importItem("./utility/menuitems.json");
            drawMenuItemsLeft('retrieve', menuItems);
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
      <form method='post' class="form-inline my-2 my-lg-0" action=utility/userLogging.php>
        <button id="logoutBtn" class="btn btn-danger my-2 my-sm-0 logout-button" name='logout-submit'
          type="submit">Kijelentkezés</button>
        <script type="text/javascript">
          window.onload = function () {
            display = document.querySelector('#time');
            var timeUpLoc = "utility/userLogging.php?logout-submit=y"
            startTimer(display, timeUpLoc);
          };
        </script>
      </form>
    </div>
  </nav>
<?php } ?>

<!-- Scanner Modal -->
<div class="modal fade" id="scanner_Modal" tabindex="-1" role="dialog" aria-labelledby="scanner_ModalLabel"
  aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Szkenner</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="stopCamera()"
          aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="reader" width="600px"></div>
        <!-- Toasts -->
        <div class="toast align-items-center" id="scan_toast" role="alert" aria-live="assertive" aria-atomic="true"
          style="z-index: 99; display:none;">
          <div class="d-flex">
            <div class="toast-body" id="scan_result">
            </div>
            <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <!--         <input type="checkbox" class="btn-check btn-light" id="btncheck1" autocomplete="off" wfd-id="id0"
          onclick="startTorch()">
        <label class="btn btn-outline-primary" for="btncheck1"><i class="fas fa-lightbulb"></i></label> -->
        <button type="button" class="btn btn-success" onclick="stopCamera()" data-bs-dismiss="modal">Kész</button>
      </div>
    </div>
  </div>
</div>
<!-- End of Scanner Modal -->

<body>
  <div class="container" id="retrieve-container">
    <h2 class="rainbow" id="doTitle">Visszahozás</h2>
    <br>
    <div class="row">
      <!-- Displays a tickable when items are selected, to approve tekaout process -->
      <div class="col " id="submit-retrieve">

        <div class="form-check intactForm" id="if_intact">
          <input class="form-check-input" type="checkbox" value="" id="intactItems">
          <label class="form-check-label" for="intactItems">
            <h6 class="statement">Igazolom, hogy minden, amit visszahoztam sérülésmentes és kifogástalanul működik.
              Sérülés esetén azonnal jelezd azt a vezetőségnek.</h6>
          </label>
          <!-- Send button holder -->
          <button class="send btn btn-success">
            <i class="fas fa-check-square fa-4x"></i>
          </button>
        </div>
        <!-- Announce Damage button -->
        <div class="row">
          <div class="col">
            <form action="../utility/damage_report/announce_Damage.php"><button class="btn btn-warning">Sérülés
                bejelentése
                <i class="fas fa-file-alt"></i></button></form>
          </div>
          <div class="col">
            <button type="button" class="btn btn-secondary" onclick="showScannerModal()"
              style='margin-bottom:6px'>Szkenner <i class="fas fa-qrcode"></i></button>
          </div>
        </div>
      </div>
    </div>


    <!-- THIS TABLE HOLDS THE TWO CHILDS - selectable and selected-->
    <!-- Selectable items -->
    <?php
    echo '<div class="row" id="retrieve-row">
      
      <div class="col-6" id="items-to-retrieve">
        <table class="table table-bordered table-dark dynamic-table" id="retrieve_items">';

    //Get the items that are currently by the user
    //Todo: Moves this function to the itemManager.php
    $TKI = $_SESSION['UserUserName'];
    $conn = Database::runQuery_mysqli();
    $sql = ("SELECT * FROM `leltar` WHERE `RentBy` = '$TKI' AND Status=0");
    $result = mysqli_query($conn, $sql);
    $conn->close();
    $n = 0;
    while ($row = $result->fetch_assoc()) {
      //var_dump($row);
      $n++;
      echo '<tr id="' . $row['UID'] . '"><td class="result dynamic-field"><button id="' . $row['UID'] . '" class="btn btn-dark" onclick="' . "prepare(this.id,'" . $row['UID'] . "'" . ",'" . $row['Nev'] . "');" . '"' . '>' . $row['Nev'] . ' [' . $row['UID'] . ']' . ' <i class="fas fa-angle-double-right"></i></button></td></tr>';
      //echo '<div class="result dynamic-field"><button id="' . $row['UID'] . '" class="btn btn-dark" onclick="' . "prepare(this.id,'" . $row['Nev'] . "'" . ');' . '"' . '>' . $row['Nev'] . ' [' . $row['UID'] . ']' . ' <i class="fas fa-angle-double-right"></i></button></div>';
    }
    echo '</table>';
    echo '</div>';
    if ($n == 0) {
      echo '<h3 class="nothing_here">Jelenleg nincs nálad egy tárgy sem!</h3>';
    }
    ?>
    <div class="col-6">
      <table class="table table-bordered dynamic-table " style="line-height: 10px;" id="dynamic_field">
        <tr>
          <div style="text-align:center;" class="text-primary"><strong></hr></strong></div>
        </tr>
      </table>
    </div>
  </div>

  </div>
  </div>
</body>

</html>

<script>

  //Preventing double click zoom
  document.addEventListener('dblclick', function (event) {
    event.preventDefault();
  }, { passive: false });


  function prepare(id, uid, name) {
    let useritems = <?php echo $n ?>;
    let itemFound = false
    for (j = 0; j < useritems; j++) {
      let InthelistId = $('#dynamic_field').find('tr').eq(j).attr('id');
      if (InthelistId && uid == $('#dynamic_field').find('tr').eq(j).attr('id').slice(5)) {
        itemFound = true;
        console.log("Item already in the list!");
        return 'alreadyInList';
      }
    }
    if (itemFound == false) {
      $('#dynamic_field').append('<tr class="bg-success" id="prep-' + id + '"><td class="dynamic-field"><button id="prep-' + id + '" class="btn btn-succes" onclick="unstage(this.id);"><i class="fas fa-angle-double-left"></i> ' + name + ' [' + uid + ']' + '</button></td></tr>');
      $('#' + id).hide();
      $('.intactForm').css('display', 'flex');
    }
  }

  function unstage(id) {
    $('#' + id).remove();
    id = id.replace("prep-", "");
    $('#' + id).show();
    if ($('#dynamic_field tr').length == 1) {
      $('.intactForm').css('display', 'none');
      $('.send').hide();
      $("#intactItems").prop("checked", false);
    }
  }
  $(document).ready(function () {
    $('.intactForm').css('display', 'none');
    // Csak akkor jelenjen meg a checkbox, ha már van Go gomb is.
    $('.send').hide();

    function startTimer(duration, display) {
      var timer = duration,
        minutes, seconds;
      setInterval(function () {
        minutes = parseInt(timer / 60, 10)
        seconds = parseInt(timer % 60, 10);

        minutes = minutes < 10 ? "0" + minutes : minutes;
        seconds = seconds < 10 ? "0" + seconds : seconds;

        display.textContent = minutes + ":" + seconds;

        if (--timer < 0) {
          timer = duration;
          window.location.href = "./utility/logout.ut.php"
        }
      }, 1000);
    }



    /*     $(document).on('click', '.result', function () {
          $('.intactForm').css('display', 'flex');
        }); */

    function allowGO() {
      if ($('#intactItems').is(":checked")) {
        $('.send').show();

      }
    }
    $(document).on('click', '#intactItems', function () {
      allowGO();
    });
    //Initiate Takeout process
    $(document).on('click', '.send', function () {
      if ($("#intactItems").prop("checked")) { // ha a felhasználó elfogadta, hogy a tárgyak rendben vannak.
        var items = []; //Items that will be retreievd.
        $('#dynamic_field > tbody  > tr > td > button ').each(function (index, tr) {
          console.log(this.innerText);
          items.push(
            {
              'uid': this.innerText.split('[')[1].slice(0, -1),
              'name': this.innerText.split('[')[0].trim()
            }
          );

        });
        //console.log(items);
        retrieveJSON = JSON.stringify(items);
        //console.log(retrieveJSON);
        $.ajax({
          method: 'POST',
          url: './ItemManager.php',
          data: {
            data: retrieveJSON,
            mode: "retrieveStaging"
          },
          success: function (response) {
            //alert(response);
            if (response == '200') {
              $('#doTitle').animate({
                'opacity': 0
              }, 400, function () {
                $(this).html('<h2 class="text text-info" role="success">Sikeresen visszakerültek a tárgyak ! Az oldal újratölt.</h2>').animate({
                  'opacity': 1
                }, 400);
                $(this).html('<h2 class="text text-info" role="success">Sikeresen visszakerültek a tárgyak ! Az oldal újratölt.</h2>').animate({
                  'opacity': 1
                }, 3000);
                $(this).html('<h2 class="text text-info" role="success">Sikeresen visszakerültek a tárgyak ! Az oldal újratölt.</h2>').animate({
                  'opacity': 0
                }, 400);
                setTimeout(function () {
                  $("#doTitle").text(applicationTitleShort).animate({
                    'opacity': 1
                  }, 400);
                }, 3800);;
              });
              setTimeout(function () {
                location.reload();
              }, 2000);
            } else {
              $('#doTitle').animate({
                'opacity': 0
              }, 400, function () {
                $(this).html('<h2 class="text text-danger" role="danger">A vissszahozás során szerveroldali hiba történt.</h2>').animate({
                  'opacity': 1
                }, 400);
                $(this).html('<h2 class="text text-danger" role="danger">A vissszahozás során szerveroldali hiba történt.</h2>').animate({
                  'opacity': 1
                }, 3000);
                $(this).html('<h2 class="text text-danger" role="danger">A vissszahozás során szerveroldali hiba történt.</h2>').animate({
                  'opacity': 0
                }, 400);
                setTimeout(function () {
                  $("#doTitle").text(applicationTitleShort).animate({
                    'opacity': 1
                  }, 400);
                  location.reload();
                }, 3800);;
              });
            }

          },
          error: function (XMLHttpRequest, textStatus, errorThrown) {
            alert("Status: " + textStatus);
            alert("Error: " + errorThrown);
          }
        });
      } else {
        alert("Ha a tárggyal gond van, jelezd a vezetőségnek!");
        return;
      }
    });



    $('#submit').click(function () {
      $.ajax({
        url: "name.php",
        method: "POST",
        data: $('#add_name').serialize(),
        success: function (data) {
          alert(data);
          $('#add_name')[0].reset();
        }
      });
    });

  });


  //Scanner

  //Toast
  const toastLiveExample = document.getElementById('scan_toast');
  const toastBootstrap = bootstrap.Toast.getOrCreateInstance(toastLiveExample);

  let toastOverwriteAllowed = true;

  function showToast(message, color) {
    if (toastOverwriteAllowed) {
      toastOverwriteAllowed = false;
      document.getElementById("scan_result").innerHTML = "<b style='color: " + color + ";'>" + message + "</b>";
      toastLiveExample.style.display = "block";
      toastBootstrap.show();

      // Allow toast to be overwritten after 5 seconds
      setTimeout(() => {
        toastOverwriteAllowed = true;
      }, 2000);
    }
  }


  //Creating Qr reader
  const QrReader = new Html5Qrcode("reader");
  let QrReaderStarted = false;

  //Qr reader settings
  const qrconstraints = {
    facingMode: "environment"
  };
  const qrConfig = {
    fps: 10,
    qrbox: {
      width: 150,
      height: 150
    },
    showTorchButtonIfSupported: true
  };
  const qrOnSuccess = (decodedText, decodedResult) => {
    console.log(`Code matched = ${decodedText}`, decodedResult);

    //Check if the scanned item is in the list
    let useritems = <?php echo $n ?>;
    let itemFound = false
    for (j = 0; j < useritems; j++) {
      if (decodedText == $('#retrieve_items').find('tr').eq(j).attr('id')) {

        if (prepare(decodedText, decodedText, $('#retrieve_items').find('tr').eq(j).find('td').text().slice(0, -10)) == 'alreadyInList') {
          showToast(decodedText + " - már visszaadtad!", "red");
          console.log("Not available!");
          itemFound = true;
          return;
        }
        showToast(decodedText, "green");
        itemFound = true;
        return;
      }

    }

    if (itemFound == false) {
      document.getElementById("scan_result").innerHTML = "<b style='color: red;'>Ez az eszköz nincs nálad!</b>";
      console.log("Not available!");
      toastLiveExample.style.display = "block";
      toastBootstrap.show();
    }
  };
  // Methods: start / stop
  const startScanner = () => {
    if (!QrReaderStarted) {
      QrReader.start(
        qrconstraints,
        qrConfig,
        qrOnSuccess,
      ).catch(console.error);
      QrReaderStarted = true;
      console.log("Reader started!");
    }
    else {
      QrReader.resume();
      console.log("Unpaused!");
    }
  };

  const stopScanner = () => {
    QrReader.pause();
  };

  // Start scanner on button click

  function showScannerModal() {
    $('#scanner_Modal').modal('show');
    startScanner();
  }


  function stopCamera() {
    console.log("Pausing camera");
    stopScanner();
  }

  function isTorchSupported() {
    let settings = QrReader.getRunningTrackSettings();
    console.log(settings);
    console.log("torch" in settings);
  }

  function startTorch() {
    if (document.getElementById("btncheck1").checked == true) {
      let constraints = {
        "torch": true,
        "advanced": [{ "torch": true }]
      };
      QrReader.applyVideoConstraints(constraints);
      let settings = QrReader.getRunningTrackSettings();

      if (settings.torch === true) {
        console.log("Torch enabled");
        // Torch was indeed enabled, succeess.
      } else {
        console.log("Torch not enabled");
        // Failure.
        // Failed to set torch, why?
      }
    } else {
    }
  }
</script>

<?php //Message handler
if (isset($_GET['state'])) {
  if ($_GET['state'] == "Success") {
    echo '<table align=center width=200px class=successtable><tr><td><div class="alert alert-success"><strong>Retrieve - </strong>Sikeresen bekerültek a tárgyak a raktárba.</div></tr></td></table>';
  }
}
?>