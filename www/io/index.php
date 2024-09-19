<?php
session_start();
include "translation.php";
include "header.php";
//Suppresses error messages
error_reporting(E_ERROR | E_PARSE);

?>
<?php if (isset($_SESSION["userId"])) { ?>
  <nav class="navbar sticky-top navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="index.php">
      <img src="./utility/logo2.png" height="50">
    </a>

    <!-- Load Menu and Index table Icons and links -->
    <script type="text/javascript">
      window.onload = async function () {
      window.onload = async function () {

        menuItems = importItem("./utility/menuitems.json");
        drawMenuItemsLeft('index', menuItems);

        drawMenuItemsRight('index', menuItems);
        drawIndexTable(menuItems, 0);

        //Load the badges
        loadBadges();

        //Load the badges
        loadBadges();

        display = document.querySelector('#time');
        var timeUpLoc = "utility/userLogging.php?logout-submit=y"
        startTimer(display, timeUpLoc);
      };
    </script>

    <!-- Mobile Navigation - Additional toggle button -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
      aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Main Navigation -->
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav mr-auto navbarUl">
      </ul>
      <ul class="navbar-nav ms-auto navbarPhP">

        <!-- Timeout timer -->
        <li><a class="nav-link disabled timelock" href="#"><span id="time"> 10:00 </span>
            <?php echo ' ' . $_SESSION['UserUserName']; ?>
          </a></li>
      </ul>

      <!-- User logout button -->
      <form method='post' class="form-inline my-2 my-lg-0" action=utility/userLogging.php>
        <button class="btn btn-danger my-2 my-sm-0" id="logoutBtn" name='logout-submit'
          type="submit">Kijelentkezés</button>
      </form>

    </div>
  </nav>

  <body>

    <h1 class="rainbow">Árpád Média IO</h1>
    <div class="row justify-content-center mainRow1 ab"
      style="text-align: center; width:100%; max-width: 1000px; margin: 0 auto;" id="take-retrieve"></div><br>
      style="text-align: center; width:100%; max-width: 1000px; margin: 0 auto;" id="take-retrieve"></div><br>
    <div class="row justify-content-center mainRow2 ab"
      style="text-align: center; width:100%; max-width: 1000px; margin: 0 auto;"></div><br>
    <div class="row justify-content-center mainRow3 ab"
      style="text-align: center; width:100%; max-width: 1000px; margin: 0 auto;"></div><br>
    <div class="row justify-content-center mainRow4 ab"
      style="text-align: center; width:100%; max-width: 1000px; margin: 0 auto;"></div><br>
    <div class="row justify-content-center mainRow5 ab"
      style="text-align: center; width:100%; max-width: 1000px; margin: 0 auto;"></div>
    <br>

    <div class="toast-container position-absolute p-3 indexToasts">
      <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" id="service_toast">
        <div class="toast-header">
          <img src="./utility/logo.png" height="30">
          <strong class="me-auto"> Üdv újra,
            <?php echo $_SESSION['firstName']; ?>!
          </strong>
          <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
          <p class="toast-text" id="usercheckItemCount">Nincs elfogadásra váró esemény!</p>
          <div class="mt-2 pt-2 border-top" id="service_toast_footer">
          </div>
        </div>
      </div>
    </div>
    <script type="text/javascript">
      async function loadBadges() {
        const response = await $.ajax({
          url: "../ItemManager.php",
          type: "POST",
          data: {
            mode: "getProfileItemCounts",
          },
        });

        console.log(response);
        let dataArray = response.split(",");
        //Set the usercheck count
        <?php if (isset($_GET["login"]) && $_GET["login"] == "success" && in_array("admin", $_SESSION["groups"])) { ?>
          if (dataArray[1] > 0) {
            document.getElementById("usercheckItemCount").innerHTML = dataArray[1] + " esemény vár elfogadásra!";
            let form = document.createElement('form');
            form.action = "../profile/transConfirm";
            form.style = "width: fit-content; display: inline-block;";
            form.innerHTML = '<button type="submit" class="btn btn-primary btn-sm">Vigyél oda!</button>';
            document.getElementById("service_toast_footer").prepend(form);
          }
      async function loadBadges() {
        const response = await $.ajax({
          url: "../ItemManager.php",
          type: "POST",
          data: {
            mode: "getProfileItemCounts",
          },
        });

        console.log(response);
        let dataArray = response.split(",");
        //Set the usercheck count
        <?php if (isset($_GET["login"]) && $_GET["login"] == "success" && in_array("admin", $_SESSION["groups"])) { ?>
          if (dataArray[1] > 0) {
            document.getElementById("usercheckItemCount").innerHTML = dataArray[1] + " esemény vár elfogadásra!";
            let form = document.createElement('form');
            form.action = "../profile/transConfirm";
            form.style = "width: fit-content; display: inline-block;";
            form.innerHTML = '<button type="submit" class="btn btn-primary btn-sm">Vigyél oda!</button>';
            document.getElementById("service_toast_footer").prepend(form);
          }
          const toastLiveExample = document.getElementById('service_toast');
          const toastBootstrap = bootstrap.Toast.getOrCreateInstance(toastLiveExample, { delay: 8000 });
          toastBootstrap.show();
        <?php } ?>
        //Set amount of items the user hold as a badge
        const badge = document.createElement('span');
        badge.className = "position-absolute top-0 start-100 translate-middle badge rounded-pill";
        badge.classList.add(dataArray[2] != 0 ? "bg-primary" : "bg-secondary");
        badge.innerHTML = dataArray[2];

        const menurow = document.getElementById("take-retrieve");
        const divToAppend = menurow.lastChild.firstChild;
        divToAppend.classList.add("position-relative");
        divToAppend.appendChild(badge);
      }
        //Set amount of items the user hold as a badge
        const badge = document.createElement('span');
        badge.className = "position-absolute top-0 start-100 translate-middle badge rounded-pill";
        badge.classList.add(dataArray[2] != 0 ? "bg-primary" : "bg-secondary");
        badge.innerHTML = dataArray[2];

        const menurow = document.getElementById("take-retrieve");
        const divToAppend = menurow.lastChild.firstChild;
        divToAppend.classList.add("position-relative");
        divToAppend.appendChild(badge);
      }
    </script>

  </body>
<?php }

  </body>
<?php }


//If the user is not logged in, display the login form
else { ?>

  <body class="login-body">


    <div class="container d-flex justify-content-center align-items-center" style="height: 100dvh;">
      <!-- Login form -->
      <form class="login" action="utility/userLogging.php" method="post" autocomplete="off">
        <fieldset>
          <h4 id="zsoka" class="text-center">Média<img src="./utility/logo.png" height="50"
              style="position:relative; bottom:5px;"></h4>
          <div class="mb-3">
            <label for="usernameInput" class="form-label">Felhasználónév:</label>
            <input type="text" class="form-control" name="useremail" id="usernameInput" required />
          </div>
          <div class="mb-3">
            <label for="password" class="form-label">Jelszó:</label>
            <input id="password" class="form-control" type="password" name="pwd" required />
          </div>
          <?php
          //If a motd exists, print it
          if (file_exists("./data/loginPageSettings.json")) {
            $file = fopen("./data/loginPageSettings.json", "r");
            $message = fread($file, filesize("./data/loginPageSettings.json"));
            $message = json_decode($message, true);
            echo "<h6 class='text text-success'><p style='color:" . $message["color"] . "'>" . $message["message"] . "</p></h6>";
            fclose($file);
          }
          ?>
          <div class="d-flex justify-content-center mb-3">
            <button class="btn btn-dark" type="submit" name="login-submit">Belépés</button>
          </div>
          <div class="d-flex justify-content-center">
            <a href="./profile/lostPwd.php">
              <h6>Elfelejtett jelszó?</h6>
            </a>
          </div>
        </fieldset>
        <div class="feedback"> átirányítás.. <br />
        </div>

      </form>
      <h6 align=center id="SystemMsg" class="successtable2" style="display:none;"></h6>
      <footer class="page-footer font-small blue">
        <div class="fixed-bottom" align="center">

          <!-- Messages appear here -->
          <h3 id="errorbox"></h3>

          <p class="Footer">Made With 💙 by <a href="https://github.com/gutasiadam/mediaio">Árpád Média</a> - <i>Ver: 24w36 - AMOK</i></p>
        </div>
      </footer>
    </div>

  </body>
  <script>
    $(".input").focusin(function () {
      $(this).find("span").animate({
        "opacity": "0"
      }, 200);
    });
    $(".input").focusout(function () {
      $(this).find("span").animate({
        "opacity": "1"
      }, 300);
    });
    $(".login").submit(function () {
      $(this).find(".submit i").removeAttr('class').addClass("fa fa-check").css({
        "color": "#fff"
      });
      $(".submit").css({
        "background": "#2ecc71",
        "border-color": "#2ecc71"
      });
      $(".feedback").show().animate({
        "opacity": "1",
      }, 400);
      $("input").css({
        "border-color": "#2ecc71"
      });
      $(".login").submit();
    });
  </script>
<?php }

  </body>
  <script>
    $(".input").focusin(function () {
      $(this).find("span").animate({
        "opacity": "0"
      }, 200);
    });
    $(".input").focusout(function () {
      $(this).find("span").animate({
        "opacity": "1"
      }, 300);
    });
    $(".login").submit(function () {
      $(this).find(".submit i").removeAttr('class').addClass("fa fa-check").css({
        "color": "#fff"
      });
      $(".submit").css({
        "background": "#2ecc71",
        "border-color": "#2ecc71"
      });
      $(".feedback").show().animate({
        "opacity": "1",
      }, 400);
      $("input").css({
        "border-color": "#2ecc71"
      });
      $(".login").submit();
    });
  </script>
<?php }

//Recieved status messages from redirects

if ($_GET["signup"] == "success") {
  echo '
<script>document.getElementById("errorbox").innerHTML="Sikeres regisztráció!";
              document.getElementById("errorbox").className = "alert alert-success successtable";
              $("#zsoka").fadeIn();
              setTimeout(function(){ $("#errorbox").fadeOut(); }, 6000);
              </script>';
}
if ($_GET["logout"] == "success") {
  echo '
<script>document.getElementById("errorbox").innerHTML="Sikeres kijelentkezés!";
              document.getElementById("errorbox").className = "alert alert-success successtable";
              $("#zsoka").fadeIn();
              setTimeout(function(){ $("#errorbox").fadeOut(); }, 6000);
              </script>';
}
if ($_GET["logout"] == "pwChange") {
  echo '
<script>document.getElementById("errorbox").innerHTML="Sikeres jelszócsere!";
              document.getElementById("errorbox").className = "alert alert-success successtable";
              $("#zsoka").fadeIn();
              setTimeout(function(){ $("#errorbox").fadeOut(); }, 6000);
              </script>';
}
if ($_GET["error"] == "WrongPass") {
  echo '
<script>document.getElementById("errorbox").innerHTML="Helytelen jelszó!";
              document.getElementById("errorbox").className = "alert alert-danger successtable";
              $("#zsoka").fadeIn();
              setTimeout(function(){ $("#errorbox").fadeOut(); }, 6000);
              </script>';
}
if ($_GET["error"] == "NoUser") {
  echo '
<script>document.getElementById("errorbox").innerHTML="Hibás felhasználónév / jelszó!";
              document.getElementById("errorbox").className = "alert alert-danger successtable";
              $("#zsoka").fadeIn();
              setTimeout(function(){ $("#errorbox").fadeOut(); }, 6000);
              </script>';
}
if ($_GET["error"] == "AccessViolation") {
  echo '
<script>document.getElementById("errorbox").innerHTML="Ehhez a funkcióhoz be kell jelentkezned!";
              document.getElementById("errorbox").className = "alert alert-danger successtable";
              $("#zsoka").fadeIn();
              setTimeout(function(){ $("#errorbox").fadeOut(); }, 6000);
              </script>';
}
if ($_GET["error"] == "loginLimit") {
  echo '
<script>document.getElementById("errorbox").innerHTML="A belépések átmenetileg korlátozva vannak. Próbáld újra később.";
              document.getElementById("errorbox").className = "alert alert-danger successtable";
              $("#zsoka").fadeIn();
              setTimeout(function(){ $("#errorbox").fadeOut();
              window.location.href = "index.php"; }, 6000);
              </script>';
}
if ($_GET["error"] == "registrationLimit") {
  echo '
<script>document.getElementById("errorbox").innerHTML="Nincs regisztrációs időszak!";
              document.getElementById("errorbox").className = "alert alert-danger successtable";
              $("#zsoka").fadeIn();
              setTimeout(function(){ $("#errorbox").fadeOut();
              window.location.href = "index.php"; }, 6000);
              </script>';
}
?>