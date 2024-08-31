<?php
session_start();
include "header.php";

error_reporting(E_ERROR | E_PARSE);
?>
<link href="utility/themes/default/style.min.css" rel="stylesheet" />


<body>
  <?php if (isset($_SESSION["userId"])) { ?>
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
              drawMenuItemsLeft('forms', menuItems, 2);
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

    <!-- Clear Modal -->
    <div class="modal fade" id="delete_Modal" tabindex="-1" role="dialog" aria-labelledby="delete_ModalLabel"
      aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Törlés</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <a>Biztosan ki akarod törölni a kérdőívet?</a>
          </div>
          <div class="modal-footer">
            <button class="btn btn-danger col-lg-auto mb-1" id="clear" data-bs-dismiss="modal">Törlés</button>
            <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Mégse</button>
          </div>
        </div>
      </div>
    </div>
    <!-- End of Clear Modal -->

    <h2 class="rainbow">Kérdőívek</h2>
    <div class="container">
      <div class="row" id="admin_opt">

      </div>
      <div class="row" id="available_forms">

      </div>
    </div>

    <?php

  } else {
    echo "<h1 class='rainbow'>Árpád Média - Kitölthető kérdőívek</h1>";
    ?>
    <div class="container">
      <div class="row" id="available_forms">

      </div>
    </div>
    <?php
  }
  ?>


</body>


<script>
  console.log("Loading forms");

  $(document).ready(function () {

    <?php if (isset($_GET['invalidID'])) { ?>
      alert("A kérdőív nem elérhető vagy nem létezik!");
    <?php } ?>


    //If user is admin
    <?php if (isset($_SESSION['userId']) && in_array("admin", $_SESSION["groups"])) { ?>
      $("#admin_opt").append('<button class="btn btn-success noprint mb-2 mr-sm-2" onclick=createNewForm()><i class="fas fa-plus fa-lg"></i></button>');
    <?php } ?>
    //Make an ajax call to formManager.php
    listForms();

  });

  function showDeleteModal(id) {
    $('#delete_Modal').modal('show');
    document.getElementById("clear").onclick = function () { deleteForm(id) };
  }

  function calculateLastEdited(lastEdited) {
    var lastEditedDate = new Date(lastEdited);
    var now = Date.now();
    var differenceInMilliseconds = now - lastEditedDate;
    var differenceInSeconds = Math.floor(differenceInMilliseconds / 1000);
    var differenceInMinutes = Math.floor(differenceInSeconds / 60);
    var differenceInHours = Math.floor(differenceInMinutes / 60);
    var differenceInDays = Math.floor(differenceInHours / 24);

    if (differenceInDays > 0) return differenceInDays + " napja";
    if (differenceInHours > 0) return differenceInHours + " órája";
    if (differenceInMinutes > 0) return differenceInMinutes + " perce";
    if (differenceInSeconds >= 0) return "Épp most";
  }

  function createCard(formId, formName, formStatus, StillEditing, formAccessRestrict, formLastEdited, backgroundImg) {
    var card = document.createElement("div");
    card.className = "card";
    <?php if (isset($_SESSION['userId']) && in_array("admin", $_SESSION["groups"])) { ?>
      var cardHeader = document.createElement("div");
      cardHeader.className = "card-header";
      cardHeader.innerHTML = "Utolsó szerkesztés: " + calculateLastEdited(formLastEdited);
      card.appendChild(cardHeader);
    <?php } ?>
    var img = document.createElement("img");
    img.className = "card-img-top";
    img.src = backgroundImg;
    img.height = "100";
    if (backgroundImg == "") {
      img.src = "https://via.placeholder.com/100";
    }
    card.appendChild(img);

    var cardBody = document.createElement("div");
    cardBody.className = "card-body";

    var cardTitle = document.createElement("h5");
    cardTitle.className = "card-title";
    cardTitle.innerHTML = formName;
    cardBody.appendChild(cardTitle);



    var cardText = document.createElement("p");
    cardText.className = "card-text";
    cardText.innerHTML = formStatus;
    cardBody.appendChild(cardText);

    var ButtonHolder = document.createElement("div");
    ButtonHolder.className = "card-option-buttons";
    ButtonHolder.role = "group";


    var cardButton = document.createElement("button");
    cardButton.className = "btn";
    cardButton.innerHTML = "<i class='fas fa-eye'></i>";
    if (formStatus == "Lezárt") {
      cardButton.disabled = true;
    }
    cardButton.onclick = function () { openForm(formId) };


    ButtonHolder.appendChild(cardButton);

    <?php if (isset($_SESSION['userId']) && in_array("admin", $_SESSION["groups"])) { ?>
      var editButton = document.createElement("button");
      editButton.className = "btn noprint";
      editButton.innerHTML = "<i class='fas fa-pen fa-lg'></i>";
      editButton.onclick = function () { editForm(formId) };
      ButtonHolder.appendChild(editButton);

      var answersButton = document.createElement("button");
      answersButton.className = "btn noprint";
      answersButton.innerHTML = "<i class='fas fa-align-left fa-lg'></i>";
      answersButton.onclick = function () { window.location.href = "formanswers.php?formId=" + formId };
      ButtonHolder.appendChild(answersButton);

      var deleteButton = document.createElement("button");
      deleteButton.className = "btn noprint";
      deleteButton.innerHTML = "<i class='fas fa-trash-alt fa-lg' style='color: red;'></i>";
      deleteButton.onclick = function () { showDeleteModal(formId) };
      ButtonHolder.appendChild(deleteButton);
    <?php } ?>

    cardBody.appendChild(ButtonHolder);
    card.appendChild(cardBody);

    <?php if (isset($_SESSION['userId']) && in_array("admin", $_SESSION["groups"])) { ?>
      var cardFooter = document.createElement("div");
      cardFooter.className = "card-footer text-body-secondary";
      cardFooter.innerHTML = formAccessRestrict;
      card.appendChild(cardFooter);
    <?php } ?>

    //console.log(card);
    document.getElementById("available_forms").appendChild(card);
  }

  function deleteForm(id) {
    //Send request to server
    $.ajax({
      type: "POST",
      url: "../formManager.php",
      data: { mode: "deleteForm", id: id },
      success: function (data) {
        //console.log(data);
        window.location.href = "index.php";
      }
    });
  }


  //List forms that are in editing phase
  function listForms() {
    $.ajax({
      url: "../formManager.php",
      method: "POST",
      data: { mode: "listForms" },
      success: function (response) {

        response = JSON.parse(response);
        
        i = 1;
        response.forEach(element => {
          //console.log(element);
          if (element.Name == null) { element.Name = "Névtelen" + i; i++; }
          if (element.AccessRestrict == "0") { element.AccessRestrict = "Publikus"; }
          if (element.AccessRestrict == "1") { element.AccessRestrict = "Privát"; }
          if (element.AccessRestrict == "2") { element.AccessRestrict = "Médiás"; }
          if (element.AccessRestrict == "3") { element.AccessRestrict = "Csak linkkel elérhető"; }

          if (element.Status == "0") { element.Status = "Lezárt"; }
          if (element.Status == "1") { element.Status = "Kitölthető"; }

          var Background = "./backgrounds/" + element.Background;
          createCard(element.ID, element.Name, element.Status, true, element.AccessRestrict, element.Last_edit, Background);
        });
      }
    });
  }
  function createNewForm() {
    //Make an ajax call to formManager.php
    $.ajax({
      url: "../formManager.php",
      method: "POST",
      data: { mode: "createNewForm" },
      success: function (response) {
        console.log(response);
        window.location.href = "formeditor.php?formId=" + response;
      }
    });
  }

  function generateXlsx() {
    //Make an ajax call to formManager.php
    $.ajax({
      url: "../formManager.php",
      method: "POST",
      data: { mode: "generateXlsx", id: 12 },
      success: function (response) {
        console.log(response);
      }
    });
  }


  function openForm(formId) {
    window.open("viewform.php?formId=" + formId, "_self");
  }

  function editForm(formId) {
    window.location.href = "formeditor.php?formId=" + formId;
  }
</script>