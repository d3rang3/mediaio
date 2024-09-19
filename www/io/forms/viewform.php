<?php
session_start();
include("header.php");
include("../translation.php"); ?>
<html>

<script src="frontEnd/formCookies.js" type="text/javascript"></script>
<script src="frontEnd/fetchData.js" type="text/javascript"></script>
<script src="frontEnd/formElements.js" type="text/javascript"></script>

<body>

   <div class="container" id="form-container">
      <form class="row form-control" id="form-body">
         <h2 class="rainbow" id="form_name"></h2>
         <h5 class="formViewHeader" id="form_header"></h5>
      </form>

      <div class="row">
      </div>
   </div>
   <div class="container d-flex justify-content-center align-items-center">
      <div class="alert alert-info alert-dismissible fade show p-2" role="alert" id="cookieAlert"
         style="display: none; max-width: 600px; position: fixed; bottom: 0; margin: 10px">
         <div class="d-flex justify-content-between align-items-center">
            <span>
               Ez a weboldal <b>sütiket</b> használ a jobb felhasználói élmény érdekében. Az oldal használatával
               elfogadod a sütik használatát.
            </span>
            <div>
               <button type="button" class="btn btn-sm btn-success me-2" data-bs-dismiss="alert"
                  onclick="cookiesAcknowledged()">Rendben</button>
            </div>
         </div>
      </div>
   </div>
   <script>
      document.addEventListener('DOMContentLoaded', (event) => {
         if (getCookie('cookiesAcknowledged') == 'true') {
            document.getElementById('cookieAlert').style.display = 'none';
         } else {
            document.getElementById('cookieAlert').style.display = 'block';
         }
      });
   </script>


</body>

<script>
   let isAnonim = 0;
   <?php if (isset($_GET['success'])) { ?>

      $(document).ready(function () {

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

         async function loadPageAsync() {
            var form = await FetchData(formId, formHash);
            await loadPage(form, "success");

            var formContainer = document.getElementById("form-body");

            <?php if (isset($_SESSION['userId']) && in_array("admin", $_SESSION["groups"])) { ?>
               //Add view answers button
               var viewAnswers = document.createElement("button");
               viewAnswers.classList.add("btn", "btn-lg", "btn-success");
               viewAnswers.innerHTML = "Válaszok megtekintése";
               viewAnswers.onclick = function () {
                  event.preventDefault();
                  if (formId != -1) {
                     window.location.href = "formanswers.php?formId=" + formId;
                  } else {
                     window.location.href = "formanswers.php?form=" + formHash;
                  }
               }
               formContainer.appendChild(viewAnswers);
            <?php } ?>
         }
         loadPageAsync();
      });

   <?php } ?>


   <?php if ((isset($_GET['formId']) || isset($_GET['form'])) && !isset($_GET['success'])) { ?>

      let formId = <?php if (isset($_GET['formId'])) {
         echo $_GET['formId'];
      } else {
         echo '-1';
      } ?>;
      let formHash = '<?php if (isset($_GET['form'])) {
         echo '"' . $_GET['form'] . '"';
      } else {
         echo 'null';
      } ?>';

      $(document).ready(function () {

         async function loadPageAsync(formId, formHash) {
            var form = await FetchData(formId, formHash);
            await loadPage(form, "fill");

            <?php if (isset($_SESSION['userId']) && in_array("admin", $_SESSION["groups"])) { ?>
               var editForm = document.createElement("button");
               editForm.classList.add("btn");
               editForm.innerHTML = '<i class="fas fa-edit fa-2x" style="color: #747b86"></i>';
               editForm.onclick = function () {
                  if (formId != -1) {
                     window.location.href = "formeditor.php?formId=" + formId;
                  } else {
                     window.location.href = "formeditor.php?form=" + formHash;
                  }
               }
               document.getElementById("form_name").appendChild(editForm);
            <?php } ?>
         }

         loadPageAsync(formId, formHash);

         let cookieSaveTimeout;

         function handleEvent() {
            // If there's a timeout already, clear it
            if (cookieSaveTimeout) {
               clearTimeout(cookieSaveTimeout);
            }

            // Set a new timeout
            cookieSaveTimeout = setTimeout(function () {
               saveUserInputToCookie();
            }, 2000); // 5000 milliseconds = 5 seconds
         }

         document.addEventListener("click", handleEvent);
         document.addEventListener("keydown", handleEvent);
      });

   <?php } ?>



   <?php if (!isset($_GET['success'])) { ?>

      var form = document.getElementById("form-body");

      form.addEventListener("submit", function (event) {
         event.preventDefault();
         submitAnswer(formId, formHash, isAnonim);
      });

      function getUid() {
         return new Promise((resolve, reject) => {
            var uid = "<?php echo $_SESSION['userId'] ?>";
            if (uid == 0 || uid == null) {
               resolve(0);
            } else {
               resolve(uid);
            }
         });
      }


      function getIp() {
         return new Promise((resolve, reject) => {
            $.get('https://api.db-ip.com/v2/free/self', function (data) {
               resolve(data.ipAddress);
            }).fail(function () {
               console.log('Error occurred');
               resolve('<?php echo $_SERVER['REMOTE_ADDR'] ?>');
            });
         });
      }

   <?php } ?>

</script>