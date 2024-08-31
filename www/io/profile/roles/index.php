<?php
namespace Mediaio;


session_start();
include "header.php";


error_reporting(E_ALL ^ E_NOTICE);

if (!isset($_SESSION["userId"])) {
  echo "<script>window.location.href = '../../index.php?error=AccessViolation';</script>";
  exit();
}

// Prevent unauthorized access
if (!in_array("system", $_SESSION["groups"])) {
  echo "Nincs jogosultságod az oldal megtekintéséhez!";
  exit();
}
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

  <!-- Info toast -->
  <div class="toast-container fixed-bottom start-50 translate-middle-x p-3" style="z-index: 9999;">
    <div class="toast" id="infoToast" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="toast-header">
        <img src="../../logo.ico" class="rounded me-2" alt="..." style="height: 20px; filter: invert(1);">
        <strong class="me-auto" id="infoToastTitle">Projektek</strong>
        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
      <div class="toast-body">
      </div>
    </div>
  </div>


  <!-- Are you sure? modal -->

  <div class="modal fade" id="areyousureModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="areyousureModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="cancelButton">Mégse</button>
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal" id="confirmButton">Mehet!</button>
        </div>
      </div>
    </div>
  </div>

  <h1 class="rainbow">Felhasználói jogkörök</h1>

  <div class="container mb-3">

  </div>
  <br>
  <br>
  <div class="text-center">
    <button class="btn btn-success w-50 mb-3" id="saveButton">Mentés</button>
  </div>
</body>

<style>
  .card {
    width: 18rem;
  }

  .container {
    display: flex;
    flex-wrap: wrap;
    flex-direction: row;
    justify-content: safe center;
    gap: 10px;

    @media (max-width: 768px) {
      justify-content: center;
      max-width: 700px;
    }
  }

  #saveButton {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    margin: 0 auto;
  }
</style>

<script>

  $(document).ready(function () {
    loadPage();

    document.getElementById('saveButton').onclick = async function () {
      $('#areyousureModal').modal('show');

      document.querySelector('#areyousureModal .modal-title').innerText = 'Biztos menteni szeretnéd a változtatásokat?';
      document.getElementById('confirmButton').classList.add('btn-success');
      document.getElementById('confirmButton').classList.remove('btn-danger');

      // Create a new Promise that resolves when the button is clicked
      let buttonClicked = new Promise((resolve, reject) => {
        document.getElementById('confirmButton').addEventListener('click', resolve);
        document.getElementById('cancelButton').addEventListener('click', reject);
      });

      buttonClicked.then(async () => {
        // Code to run when 'confirmButton' is clicked
        console.log("Saving changes...");
        let users = document.querySelectorAll('.card');
        let usersData = [];

        users.forEach(user => {
          let userId = user.id;
          let groups = Array.from(user.querySelectorAll('input[type="checkbox"]'))
            .filter(checkbox => checkbox.checked)
            .map(checkbox => checkbox.value);
          usersData.push({ userId, groups });
        });

        console.log(usersData);

        let response = await fetch('roleManager.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: 'mode=submitRoles&data=' + JSON.stringify(usersData)
        });

        if (response.status == 200) {
          successToast('Sikeres mentés!');
          loadPage();
        } else {
          errorToast('Hiba történt a mentés során!');
        }
      }).catch(() => {
        // Code to run when 'cancelButton' is clicked
        console.log("Changes discarded.");
        return;
      });
    };
  });

  async function loadPage() {
    let data = await fetchdata();

    let container = document.querySelector('.container');
    container.innerHTML = '';
    for (let i = 0; i < data.length; i++) {
      let user = data[i];
      container.appendChild(await createUserCard(user));
    }
  }

  async function fetchdata() {
    let response = await fetch('roleManager.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: 'mode=getUsers'
    });

    let data = await response.json();
    return data;
  }


  async function createUserCard(user) {
    let userGroups = JSON.parse(user.AdditionalData);
    userGroups = userGroups.groups;

    let card = document.createElement('div');
    card.classList.add('card', 'mb-3');
    card.style.border = '2px solid';
    card.id = user.idUsers;

    await colorCardBasedOnRole(card, userGroups);

    let cardHeader = document.createElement('div');
    cardHeader.classList.add('card-header', 'd-flex', 'justify-content-between', 'align-items-center');
    cardHeader.innerText = user.usernameUsers;
    card.appendChild(cardHeader);

    let cardBody = document.createElement('div');
    cardBody.classList.add('card-body');
    card.appendChild(cardBody);

    let cardTitle = document.createElement('h5');
    cardTitle.classList.add('card-title');
    cardTitle.innerText = user.lastName + ' ' + user.firstName;
    cardBody.appendChild(cardTitle);


    let dropdownMenu = document.createElement('ul');


    let roles = [
      { value: 'média', text: 'Médiás' },
      { value: 'studio', text: 'Stúdiós' },
      { value: 'event', text: 'Eventes' },
      { value: 'admin', text: 'Vezetőségi tag' },
      { value: 'teacher', text: 'Tanár' },
      { value: 'system', text: 'SysAdmin' }
    ];

    roles.forEach(role => {
      let roleItem = document.createElement('li');
      roleItem.classList.add('dropdown-item');
      roleItem.style.cursor = 'pointer';
      roleItem.onclick = function () {
        event.stopPropagation();
        let checkBox = roleItem.querySelector('input');
        checkBox.checked = !checkBox.checked;
      };

      let checkBox = document.createElement('input');
      checkBox.classList.add('form-check-input');
      checkBox.type = 'checkbox';
      checkBox.value = role.value;
      checkBox.style.cursor = 'pointer';
      checkBox.checked = Array.from(userGroups).includes(role.value);
      checkBox.onclick = function () {
        event.stopPropagation();
      };
      roleItem.appendChild(checkBox);

      let label = document.createElement('label');
      label.classList.add('form-check-label');
      label.style.userSelect = 'none';
      label.style.marginLeft = '10px';
      label.style.cursor = 'pointer';
      label.innerText = role.text;
      roleItem.appendChild(label);

      dropdownMenu.appendChild(roleItem);
    });

    cardBody.appendChild(dropdownMenu);

    let deleteButton = document.createElement('button');
    deleteButton.classList.add('btn', 'btn-sm', 'btn-danger');
    deleteButton.innerText = 'Törlés';
    deleteButton.onclick = function () {
      event.stopPropagation();
      deleteUser(user);
    };
    cardHeader.appendChild(deleteButton);

    return card;
  }


  function colorCardBasedOnRole(card, userGroups) {
    if (userGroups.includes('system')) {
      card.classList.add('border-danger');
    }
    else if (userGroups.includes('teacher')) {
      card.classList.add('border-info');
    }
    else if (userGroups.includes('admin')) {
      card.classList.add('border-warning');
    }
    else if (userGroups.includes('event')) {
      card.classList.add('border-success');
    }
    else if (userGroups.includes('studio')) {
      card.classList.add('border-primary');
    }
    else if (userGroups.includes('média')) {
      card.classList.add('border-secondary');
    }
  }

</script>


</html>