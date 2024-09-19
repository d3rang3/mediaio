<?php

namespace Mediao;

session_start();

if (!isset($_SESSION['userId'])) {
   echo "<script>window.location.href = '../index.php?error=AccessViolation';</script>";
   exit();
}

include "header.php";
error_reporting(E_ERROR | E_PARSE);

?>

<body>


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
                  drawMenuItemsLeft('adatok', menuItems, 2);
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

   <!-- Info toast -->
   <div class="toast-container fixed-bottom start-50 translate-middle-x p-3" style="z-index: 9999;">
      <div class="toast" id="infoToast" role="alert" aria-live="assertive" aria-atomic="true">
         <div class="toast-header">
            <img src="../logo.ico" class="rounded me-2" alt="..." style="height: 20px; filter: invert(1);">
            <strong class="me-auto" id="infoToastTitle">Projektek</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
         </div>
         <div class="toast-body">
         </div>
      </div>
   </div>


   <h2 class="rainbow">
      <?php if (in_array("admin", $_SESSION["groups"])) { ?>
         <input type="checkbox" class="btn-check filterButton" id="showEmpty" autocomplete="off">
         <label class="btn btn-outline-secondary" for="showEmpty">Üresek</label>
      <?php } ?>
      Leltár adatok&nbsp
      <?php if (in_array("admin", $_SESSION["groups"])) { ?>
         <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#newItemModal">Új</button>
      <?php } ?>
   </h2>

   <div class="container">
      <div class="row justify-content-center">
         <div class="btn-group mb-1" role="group" id="settings" aria-label="Beállítások">
            <input type="checkbox" class="btn-check filterButton" id="medias" autocomplete="off">
            <label class="btn btn-outline-secondary" for="medias">Médiás</label>

            <input type="checkbox" class="btn-check filterButton" id="studios" autocomplete="off">
            <label class="btn btn-outline-secondary" for="studios">Stúdiós</label>

            <input type="checkbox" class="btn-check filterButton" id="event" autocomplete="off">
            <label class="btn btn-outline-secondary" for="event">Event</label>

            <input type="checkbox" class="btn-check filterButton" id="isOut" autocomplete="off" checked>
            <label class="btn btn-outline-secondary" for="isOut">Kinnlévő</label>

            <input type="checkbox" class="btn-check filterButton" id="nonRentable" autocomplete="off">
            <label class="btn btn-outline-secondary" for="nonRentable">Nem kivehető</label>
         </div>
      </div>

      <div class="statsTable" id="tableContainer">

      </div>

      <!-- Navigation back to top -->
      <div id='toTop'><i class="fas fa-chevron-up"></i></div>
   </div>



   <?php if (in_array("admin", $_SESSION["groups"])) { ?>

      <!-- Edit Item Modal -->
      <div class="modal fade" id="editItemModal" tabindex="-1" aria-labelledby="editItemModalLabel" aria-hidden="true">
         <div class="modal-dialog">
            <div class="modal-content">
               <div class="modal-header">
                  <h5 class="modal-title" id="editItemModalLabel">Szerkesztés - xxxXXX</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
               </div>
               <div class="modal-body">
                  <!-- A form for editing Items Attributes-->
                  <form>
                     <input type="text" class="form-control" id="IDInput" aria-describedby="IDHelp" disabled="true"
                        placeholder="ID">
                     <div class="form-group">
                        <label for="UIDInput">UID</label>
                        <input type="text" class="form-control" id="UIDInput" aria-describedby="UIDHelp" placeholder="UID">
                     </div>
                     <div class="form-group">
                        <label for="NameInput">Név</label>
                        <input type="text" class="form-control" id="NameInput" aria-describedby="NameHelp"
                           placeholder="Név">
                     </div>
                     <div class="form-group">
                        <label for="TypeInput">Típus</label>
                        <input type="text" class="form-control" id="TypeInput" aria-describedby="TypeHelp"
                           placeholder="Típus">
                     </div>
                     <div class="form-group">
                        <label for="CategoryInput">Kategória</label>
                        <input type="text" class="form-control" id="CategoryInput" aria-describedby="CategoryHelp"
                           placeholder="Kategória">
                     </div>
                     <div class="form-group">
                        <label for="TakeRestrictInput">TakeRestrict</label>
                        <input type="text" class="form-control" id="TakeRestrictInput" aria-describedby="TakeRestrictHelp"
                           placeholder="TakeRestrict">
                     </div>
                  </form>


               </div>
               <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Mégse</button>
                  <button type="button" class="btn btn-warning" onclick="updateItemData()">Módosítás</button>
               </div>
            </div>
         </div>
      </div>

      <!-- New Item Modal -->
      <div class="modal fade" id="newItemModal" tabindex="-1" aria-labelledby="newItemModalLabel" aria-hidden="true">
         <div class="modal-dialog">
            <div class="modal-content">
               <div class="modal-header">
                  <h5 class="modal-title" id="newItemModalLabel">Új Eszköz hozzáadása</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
               </div>
               <div class="modal-body">
                  <!-- A form for adding a new Item Attributes-->
                  <form>
                     <div class="form-group">
                        <label for="UIDInput"><strong>UID</strong></label>
                        <input type="text" class="form-control" id="UIDInput" aria-describedby="UIDHelp" placeholder="UID"
                           required>
                     </div>
                     <div class="form-group">
                        <label for="NameInput">Név</label>
                        <input type="text" class="form-control" id="NameInput" aria-describedby="NameHelp"
                           placeholder="Név" required>
                     </div>
                     <div class="form-group">
                        <label for="TypeInput">Típus</label>
                        <input type="text" class="form-control" id="TypeInput" aria-describedby="TypeHelp"
                           placeholder="Típus">
                     </div>
                     <div class="form-group">
                        <label for="CategoryInput">Kategória</label>
                        <input type="text" class="form-control" id="CategoryInput" aria-describedby="CategoryHelp"
                           placeholder="Kategória">
                     </div>
                     <div class="form-group">
                        <label for="takeRestrictInput">TakeRestrict</label>
                        <input type="text" class="form-control" id="TakeRestrict" aria-describedby="takeRestrictHelp"
                           placeholder="TakeRestrict">
                     </div>
                  </form>
               </div>
               <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Mégse</button>
                  <button type="button" class="btn btn-success" onclick="createItem()">Létrehozás</button>
               </div>
            </div>
         </div>
      </div>
   <?php } ?>

</body>