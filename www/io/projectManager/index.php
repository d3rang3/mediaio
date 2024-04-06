<?php
session_start();
include ("header.php");
include ("../translation.php"); ?>

<html>
<?php
if (!isset($_SESSION["userId"])) {
   echo "<script>window.location.href = '../index.php?error=AccessViolation';</script>";
   exit();
}
?>

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

<body class="background">
   <!-- <button class="btn btn-secondary" onclick=testNAS()>NAS LOFASZ</button> -->
   <?php include "modals.php"; ?>
   <h1 class="rainbow">
      <?php if (isset($_SESSION["userId"]) && in_array("admin", $_SESSION["groups"])) { ?>
         <button class="btn btn-secondary" onclick=showArchivedProjects()><i class="fas fa-archive fa-lg"></i></button>
      <?php } ?>
      &nbsp;Jelenlegi projektek&nbsp;
      <?php if (isset($_SESSION["userId"]) && in_array("admin", $_SESSION["groups"])) { ?>
         <button class="btn btn-success" onclick=createNewProject()><i class="fas fa-plus fa-lg"></i></button>
      <?php } ?>
   </h1>

   <!-- <button type="button" class="btn custom-kurva-anyja">LOFASZ</button> -->

   <div class="container">

      <div class="projectHolder" id="projectHolder">

      </div>

   </div>

</body>
<script>

   function testNAS() {
      $.ajax({
         url: 'nasCommunication.php',
         type: 'GET',
         data: {
            mode: 'getRootFolderData'
         },
         success: function (data) {
            console.log(data);
         }
      });
   }

   // Disable double tap zoom
   document.addEventListener('dblclick', function (event) {
      event.preventDefault();
   }, { passive: false });


   $(document).ready(function () {
      refreshProjects();
   });

   setInterval(() => {  
      // If any bootstrap modal is open, don't refresh
      if ($('.modal').hasClass('show')) {
         return;
      }
      refreshProjects();
      simpleToast("Projektek frissítve!");
   }, 60000);

   async function refreshProjects() {
      let mobile = window.innerWidth <= 768;

      let projectHolder = document.getElementById('projectHolder');
      projectHolder.innerHTML = '';

      //Make a spinner
      let spinner = document.createElement('div');
      spinner.classList.add('spinner-grow', 'text-secondary');
      spinner.innerHTML = '<span class="visually-hidden">Loading...</span>';
      projectHolder.appendChild(spinner);

      let projects = await fetchProjects();
      await generateProjects(projects, mobile);
      await dragAndDropReady();
      await toolTipRender();

      projectHolder.removeChild(spinner);
   }

</script>

</html>