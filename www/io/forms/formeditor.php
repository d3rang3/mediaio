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

<script src="https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js"></script>
<html>

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
               drawMenuItemsLeft('forms', menuItems, 2);
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

<div class="centerTopAccessories">
   <button class="btn" onclick="window.location.href = 'formanswers.php?formId=' +<?php echo $_GET['formId'] ?>"><i
         class='fas fa-align-left fa-lg' style="color: fff"></i></button>
   <button class="btn" onclick="window.location.href = 'viewform.php?formId=' + <?php echo $_GET['formId'] ?>"
      style="color: fff"><i class="fas fa-eye"></i></button>
   <button class="btn" onclick="saveFormElements(false)"><i class="fas fa-save" style="color: #ffffff;"></i></button>
</div>


<?php include("modals.php"); ?>



<div class="form">
   <div id="form-header">
      <h2 class="rainbow" id="form_name" style="cursor: pointer;"></h2>
      <textarea class="form-control" id="description"></textarea>
   </div>
   <div class="container">
      <div class="row" id="form-option-buttons">
         <div class="dropdown" id="tools">

            <button class="btn btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown"
               aria-expanded="false">
               Új hozzáadása
            </button>
            <ul class="dropdown-menu">
               <li><a class="dropdown-item" href="#" onclick="addFormElement('email')"><i class="fas fa-at"></i>
                     E-Mail</a></li>
               <li><a class="dropdown-item" href="#" onclick="addFormElement('shortText')"><i
                        class="fas fa-grip-lines fa-lg"></i> Rövid szöveg</a></li>
               <li><a class="dropdown-item" href="#" onclick="addFormElement('longText')"><i
                        class="fas fa-align-justify fa-lg"></i> Hosszú szöveg</a></li>

               <!-- Feleletválasztós -->
               <li class="dropdown-divider"></li>
               <li><a class="dropdown-item" href="#" onclick="addFormElement('radio')"><i
                        class="far fa-dot-circle fa-lg"></i>
                     Feleletválasztós</a></li>
               <li><a class="dropdown-item" href="#" onclick="addFormElement('checkbox')"><i
                        class="far fa-check-square fa-lg"></i> Jelölőnégyzet</a>
               </li>
               <li><a class="dropdown-item" href="#" onclick="addFormElement('dropdown')"><i
                        class="fas fa-chevron-down fa-lg"></i> Legördülő lista</a></li>

               <!-- Skála -->
               <li class="dropdown-divider"></li>
               <li><a class="dropdown-item" href="#" onclick="addFormElement('scaleGrid')"><i
                        class="fas fa-th fa-lg"></i> Feleletválasztós rács</a></li>

               <!-- Idő -->
               <li class="dropdown-divider"></li>
               <li><a class="dropdown-item" href="#" onclick="addFormElement('date')"><i
                        class="fas fa-calendar-alt fa-lg"></i> Dátum</a></li>
               <li><a class="dropdown-item" href="#" onclick="addFormElement('time')"><i class="fas fa-clock fa-lg"></i>
                     Idő</a></li>

               <!-- Fájl -->
               <li class="dropdown-divider"></li>
               <li><a class="dropdown-item" href="#" onclick="/*addFormElement('fileUpload')*/"><i
                        class="fas fa-file fa-lg"></i><i>Fájl feltöltés (fejlesztés alatt)</i></a>
               </li>
            </ul>
         </div>
         <button class="btn" data-bs-target="#settings_Modal" data-bs-toggle="modal"><i
               class="fas fa-sliders-h fa-lg"></i></button>
      </div>
      <div class="row" id="editorZone">

      </div>
   </div>
</div>

<script src="frontEnd/backgroundManager.js" type="text/javascript"></script>
<script src="frontEnd/fetchData.js" type="text/javascript"></script>
<script src="frontEnd/formElements.js" type="text/javascript"></script>
<script src="frontEnd/drangAndDrop.js" type="text/javascript"></script>

<script>
   //Changing this variable if something is changed
   var everythingSaved = true;


   //If the user tries to leave the page without saving, show a warning
   window.onbeforeunload = function () {
      if (!everythingSaved) {
         return "Nem mentett változtatások vannak! Biztosan továbblépsz?";
      }
   }

   //After any keypress something is changed
   document.addEventListener('keypress', function (event) {
      everythingSaved = false;
   });

   //Place popover on default background
   document.addEventListener('DOMContentLoaded', function () {
      var popoverTriggerEl = document.getElementById('default-background');
      var popover = new bootstrap.Popover(popoverTriggerEl, {
         content: '<img src="./backgrounds/default.jpg" width="200px" alt="Popover image">',
         html: true,
         trigger: 'hover',
         placement: 'right'
      });
   });

   //Function to show title modal
   var i = 0;
   document.getElementById("form_name").addEventListener("click", function () {
      $('#Title_Modal').modal('show');
   });

   //Function to save title
   function save_title() {
      var title = document.getElementById("formTitle").value;
      document.getElementById("form_name").innerHTML = title + '&nbsp<i class="fas fa-edit fa-xs" style="color: #747b86"></i>';
      everythingSaved = false;
   }

   //Function to show link holder

   function showLink(formHash, show = true) {
      if (show) {
         document.getElementById("linkHolderGroup").style.display = "flex";
         document.getElementById("linkHolderGroup").classList.add("mb-1");
         var linkholder = document.getElementById("formLinkHolder");
         linkholder.value = "https://<?php echo $_SERVER['HTTP_HOST']; ?>/forms/viewform.php?form=" + formHash;
      } else {
         document.getElementById("linkHolderGroup").style.display = "none";
         document.getElementById("linkHolderGroup").classList.remove("mb-1");
         var linkholder = document.getElementById("formLinkHolder");
         linkholder.value = "https://<?php echo $_SERVER['HTTP_HOST']; ?>/forms/viewform.php?form=" + formHash;;
      }
   }

   function copyLink() {
      var copyText = document.getElementById("formLinkHolder");
      copyText.select();
      copyText.setSelectionRange(0, 99999);
      document.execCommand("copy");
   }


   $(document).ready(function () {
      //Load form from server

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

      async function loadPageAsync(formId, formHash) {

         var form = await FetchData(formId, formHash);
         await loadPage(form, "editor");
      }

      loadPageAsync(formId, formHash);
      $('#accessRestrict').change(function () {
         if ($(this).val() === '3') {
            showLink(formHash);
         } else {
            showLink(formHash, false);
         }
      });
   });

   //Function to check if question id is used
   function checkIdNotUsed(id) {
      for (var j = 0; j < formElements.length; j++) {
         if (formElements[j].id == id) {
            id++;
            checkIdNotUsed(id);
         }
      }
      return id;
   }
   //Function to add a new form element
   function addFormElement(type) {
      everythingSaved = false;
      i++;
      i = checkIdNotUsed(i); //Check if id is used
      console.log("Adding form element: " + type);
      var place = document.getElementById("editorZone").getElementsByClassName("form-member").length + 1; //Get the place of the new element

      let newElement = new FormElement(i, type, "", "", false, []);
      let container = document.getElementById("editorZone");
      newElement.createElement(container, "editor");
      formElements.push(newElement);
   };


   //Function to move an element up
   function moveUp(type, id) {
      everythingSaved = false;
      var element = document.getElementById(type + "-" + id);

      element.setAttribute('data-position', parseInt(element.getAttribute('data-position')) - 1);
      //Get the previous element
      var prevElement = element.previousElementSibling;
      prevElement.setAttribute('data-position', parseInt(prevElement.getAttribute('data-position')) + 1);
      if (prevElement != null) {
         element.parentNode.insertBefore(element, prevElement);
      }
   }

   //Function to move an element down
   function moveDown(type, id) {
      everythingSaved = false;
      var element = document.getElementById(type + "-" + id);
      element.setAttribute('data-position', parseInt(element.getAttribute('data-position')) + 1);
      var nextElement = element.nextElementSibling;
      nextElement.setAttribute('data-position', parseInt(nextElement.getAttribute('data-position')) - 1);
      if (nextElement != null) {
         element.parentNode.insertBefore(nextElement, element);
      }
   }



   //Every 10 seconds, save the form
   setInterval(function () {
      if (everythingSaved) {
         return;
      }
      //Add growing spinner to the first h6
      //document.getElementById("time").innerHTML = "<div class='spinner-grow text-success' role='status'></div>"
      saveFormElements(true);
   }, 10000);


   function saveForm(auto) {
      //Get all elements
      var formEditor = document.getElementById("editorZone");
      var elements = formEditor.getElementsByClassName("form-member");
      var formName = document.getElementById("form_name").innerHTML.split("&nbsp")[0];

      //If form name is empty, set it to "Névtelen"
      if (formName == "") {
         formName = "Névtelen";
      }


      var formState = document.getElementById("formState").value;
      var accessRestrict = document.getElementById("accessRestrict").value;
      var formHeader = document.getElementById("description").value;

      var formAnonim = document.querySelector('[data-setting="Anonim"]').checked ? 1 : 0;
      var formSingleAnswer = document.querySelector('[data-setting="SingleAnswer"]').checked ? 1 : 0;

      var form = {
         "name": formName,
         "header": formHeader,
         "state": formState,
         "access": accessRestrict,
         "anonim": formAnonim,
         "singleAnswer": formSingleAnswer
      };
      var formJson = JSON.stringify(form);

      var formId = <?php if (isset($_GET['formId'])) {
         echo $_GET['formId'];
      } else {
         echo '-1';
      } ?>;
      var formHash = <?php if (isset($_GET['form'])) {
         echo '"' . $_GET['form'] . '"';
      } else {
         echo 'null';
      } ?>;

      //console.log(formJson);
      //Send form to server
      $.ajax({
         type: "POST",
         url: "../formManager.php",
         data: { form: formJson, mode: "save", id: formId, formHash: formHash },
         success: function (data) {
            console.log(data);

            const toastLiveExample = document.getElementById('save_toast');
            const toastBootstrap = bootstrap.Toast.getOrCreateInstance(toastLiveExample, { delay: 3000 });

            //If data is 200, append a span with a message after time
            if (data == 200) {
               if (auto) {
                  document.getElementById("save_status").innerHTML = "Automatikusan mentve!";
               } else {
                  document.getElementById("save_status").innerHTML = "Sikeres mentés";
               }
               everythingSaved = true;
            } else {
               document.getElementById("save_status").innerHTML = "Sikertelen mentés";
            }
            toastBootstrap.show();

         }
      });
   }

   function deleteForm() {
      var formId = <?php if (isset($_GET['formId'])) {
         echo $_GET['formId'];
      } else {
         echo '-1';
      } ?>;
      var formHash = <?php if (isset($_GET['form'])) {
         echo '"' . $_GET['form'] . '"';
      } else {
         echo 'null';
      } ?>;
      //Send request to server
      $.ajax({
         type: "POST",
         url: "../formManager.php",
         data: { mode: "deleteForm", id: formId, formHash: formHash },
         success: function (data) {
            //console.log(data);
            window.location.href = "index.php";
         }
      });
   }
</script>