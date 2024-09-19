<?php
session_start();

include("header.php");
include("../translation.php");

if (!isset($_SESSION["userId"])) {
   echo "<script>window.location.href = '../index.php?error=AccessViolation';</script>";
   exit();
}
if (!in_array("admin", $_SESSION["groups"])) {
   echo "<script>window.location.href = './index.php';</script>";
   exit();
}
?>
<html>

<script src="frontEnd/formElements.js" type="text/javascript"></script>


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

<?php include("modals.php"); ?>

<div class="centerTopAccessories">
   <button class="btn" onclick="window.location.href = 'formeditor.php?formId=' + <?php echo $_GET['formId'] ?>"><i
         class='fas fa-edit fa-lg' style="color: fff"></i></button>
   <button class="btn" onclick="window.location.href = 'viewform.php?formId=' + <?php echo $_GET['formId'] ?>"
      style="color: fff"><i class="fas fa-eye"></i></button>
</div>

<body>
   <div class="form" id="doboz">
      <h2 class="rainbow" id="form_name"></h2>
      <div class="row" id="form-option-buttons">
         <div class="dropdown" id="tools">
            <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
               <i class="fas fa-align-left"></i>
            </button>
            <ul class="dropdown-menu" id="answers_dropdown">
            </ul>
         </div>
         <button class="btn btn-success" onclick="showTable()"><i class="fas fa-table fa-lg"></i></button>
         <button class="btn" onclick="createXLSX()"><i class="far fa-share-square fa-lg"></i></button>

      </div>
      <div class="justify-content-center">
         <table class="table" id="answersTable" style="display: none">
            <thead>
               <tr id="headerHolder">

               </tr>
            </thead>
            <tbody class="table-group-devider" id="answerHolder">

            </tbody>
         </table>
         <form class="row form-control" id="form-body" style="display: none">

         </form>
         <br>
      </div>
   </div>
</body>

<script src="frontEnd/fetchData.js" type="text/javascript"></script>
<script src="frontEnd/formAnswers.js" type="text/javascript"></script>

<script>
   let currentFormState = null;

   let formId = <?php if (isset($_GET['formId'])) {
      echo $_GET['formId'];
   } else {
      echo '-1';
   } ?>;
   let formHash = <?php if (isset($_GET['form'])) {
      echo '"' . $_GET['form'] . '"';
   } else {
      echo 'null';
   } ?>;

   $(document).ready(function () {
      //Load form from server

      async function loadPageAsync() {
         currentFormState = await FetchData(formId, formHash);
         document.getElementById("form_name").innerHTML = currentFormState.Name;

         // Set backgroud 
         const style = document.createElement('style');
         style.innerHTML = `
         body::before {
         content: "";
         position: fixed;
         top: 0;
         right: 0;
         bottom: 0;
         left: 0;
         background-image: url(../forms/backgrounds/${currentFormState.Background});
         background-size: cover;
         background-position: center;
         z-index: -1;
         }`;
         document.head.appendChild(style);

         await fetchAnswers(formId, formHash);
         showTable();
      }
      loadPageAsync();

   });


   function setButtonClass(name) {
      var buttons = document.getElementById("form-option-buttons").getElementsByTagName("button");
      for (var i = 0; i < buttons.length; i++) {
         buttons[i].classList.remove("btn-success");
      }

      switch (name) {
         case "singleAnswer":
            buttons[0].classList.add("btn-success");
            break;
         case "table":
            buttons[1].classList.add("btn-success");
            break;
      }
   }

   async function createXLSX() {
      simpleToast("Ez a funkció még fejlesztés alatt áll!");
      return;
      // Send request to backend to create xlsx file
      const response = await $.ajax({
         type: "POST",
         url: "../formManager.php",
         data: { mode: "generateXlsx", id: formId, hash: formHash },
      });

      // Download the file
      console.log(response);
   }

</script>

</html>