<?php
session_start();
include ("header.php");
include ("../translation.php"); ?>

<nav class="navbar sticky-top navbar-expand-lg navbar-dark bg-dark">
   <a class="navbar-brand" href="./index.php">
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
               drawMenuItemsLeft('projectmanager', menuItems, 2);
            });
         </script>
      </ul>
      <ul class="navbar-nav ms-auto navbarPhP">
         <li>
            <a class="nav-link disabled timelock" href="#"><span id="time"> 30:00 </span>
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
               startTimer(display, timeUpLoc, 30);
            };
         </script>
      </form>
   </div>
</nav>


<body>
   <?php include "modals.php"; ?>
   <!-- <h1 class="rainbow" id="projectTitle">Project</h1> -->

   <div class="container">
      <div class="projectHolder" id="projectHolder">

      </div>

   </div>

</body>

<script>

   // Disable double tap zoom
   document.addEventListener('dblclick', function (event) {
      event.preventDefault();
   }, { passive: false });

   $(document).ready(function () {

      if (window.innerWidth > 768) {
         window.location.href = "./index.php";
      }

      refreshProjects();
   });

   // Check for updates every minute
   let lastUpdate = new Date().getTime();

   setInterval(async () => {
      // Check if there is anything updated
      if (await checkForUpdates(lastUpdate) == 'false') {
         console.log('No updates found');
         return;
      }

      if ($('.modal').hasClass('show') || document.querySelectorAll('.dragging').length > 0) {
         return;
      }
      // Hide all tooltips
      document.querySelectorAll('.tooltip').forEach(e => e.style.display = 'none');


      refreshProjects();
      simpleToast("Projekt frissítve!");
   }, 60000);

   async function refreshProjects() {
      let project = await fetchProject(<?php echo $_GET['projectID']; ?>);

      let projectHolder = document.getElementsByClassName('projectHolder')[0];
      projectHolder.innerHTML = '';

      //Make a spinner
      let spinner = document.createElement('div');
      spinner.classList.add('spinner-grow', 'text-secondary');
      spinner.innerHTML = '<span class="visually-hidden">Loading...</span>';
      projectHolder.appendChild(spinner);

      await generateMobileView(project);
      //TODO: ORDER FUNCTIONALITY NEEDED HERE
      await toolTipRender();

      lastUpdate = new Date().getTime();
      //Remove spinner
      projectHolder.removeChild(spinner);
   }

</script>