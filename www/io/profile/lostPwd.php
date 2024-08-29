
<?php
include "header.php";
?>

<h3 class="rainbow">Elfelejtett jelszó pótlása</h3>

<table class="logintable">
  <?php
  if (isset($_GET['error'])) {
    if ($_GET['error'] == 'emptyField') {
      echo '<tr><td><h5 class="registererror text text-danger">Kérlek MINDEN mezőt tölts ki!</h5></td></tr>';
    } else if ($_GET['error'] == 'PasswordCheck') {
      echo '<tr><td><h5 class="registererror text text-danger">A megadott jelszavak nem egyeznek, vagy túl rövid jelszót adtál meg!</h5></td></tr>';
    } else if ($_GET['error'] == 'PasswordLenght') {
      echo '<tr><td><h5 class="registererror text text-danger">Az új jelszónak legalább 8 karakter hosszúnak kell lennie!</h5></td></tr>';
    } else if ($_GET['error'] == 'OldPwdError') {
      echo '<tr><td><h5 class="registererror text text-danger">Hibásan adtad meg a jelenlegi jelszavadat!</h5></td></tr>';
    } else if ($_GET['error'] == 'userData') {
      echo '<tr><td><h5 class="registererror text text-danger">A megadott adatokkal nem létezik felhasználó a rendszerben!</h5></td></tr>';
    } else if ($_GET['error'] == 'tokenError') {
      echo '<tr><td><p class="success">A token, vagy az email cím/felhasználónév párod hibás.</p></td></tr>';
    } else if ($_GET['error'] == 'tokenSent') {
      echo '<tr><td><p class="text-success">A tokenedet elküldtük az e-mail címedre.</br> Nézd meg a spam mappát is.</p></td></tr>';
    } else if ($_GET['error'] == 'none') {
      echo '<tr><td><p class="text-success"><strong>Sikeres jelszócsere.</strong> Mostmár beléphetsz az új jelszavaddal.</p></td></tr>';
    }
  }
  ?>
  <form action="../Core.php" method="post">
    <tr>
      <td>
        <h3><strong>1. lépés</strong>: kérj egy tokent!</strong></h3>
      </td>
    </tr>
    <tr>
      <td>Ennek segítségével tudsz majd új jelszót megadni.</td>
    </tr>
    <tr>
      <td><input class="form-control mb-2 mr-sm-2" type="email" name="emailAddr" placeholder="e-mail" required></td>
    </tr>
    <tr>
      <td><input class="form-control mb-2 mr-sm-2" type="text" name="userName" placeholder="felhasználónév" required></td>
    </tr>
    <tr>
      <td><br><button class="btn btn-dark" id="submitPwdCh1" align=center type="submit" name="pwdLost-submit" required>Token küldése</button></td>
    </tr>
    <tr>
      <td>
        <div class="spinner-border" role="status">
          <span class="sr-only">Folyamatban...</span>
        </div>
    </tr>
    </td>
  </form>
  <form action="../Core.php" method="post">
    <tr>
      <td>
        <h3><strong>2. lépés</strong>: új jelszó</strong></h3>
      </td>
    </tr>
    <tr>
      <td>Az 1. lépésben kapott tokened megadása után adj meg egy új jelszót!</td>
    </tr>
    <tr>
      <td><input class="form-control mb-2 mr-sm-2" type="text" name="token" placeholder="token" required></td>
    </tr>
    <tr>
      <td><input class="form-control mb-2 mr-sm-2" type="text" name="userName" placeholder="felhasználónév" required></td>
    </tr>
    <tr>
      <td><input class="form-control mb-2 mr-sm-2" type="email" name="emailAddr" placeholder="e-mail" required></td>
    </tr>
    <tr>
      <td><input class="form-control mb-2 mr-sm-2" type="password" name="chPwd-1" placeholder="új jelszó" required></td>
    </tr>
    <tr>
      <td><input class="form-control mb-2 mr-sm-2" type="password" name="chPwd-2" placeholder="új jelszó még egyszer" required></td>
    </tr>
    <tr>
      <td><button class="btn btn-dark" id="submitPwdCh2" align=center type="submit" name="pwdLost-change-submit">Csere</button></td>
    </tr>
    <tr>
      <td>
        <div class="spinner-border" role="status">
          <span class="sr-only">Folyamatban...</span>
        </div>
    </tr>
    </td>
  </form>

  <tr>
    <td>A sikeres jelszóváltoztatásról e-mailben értesítünk. Eztuán lépj vissza a belépéshez.</td>
  </tr>
  <tr>
    <td><a href="../index.php"><button class="btn btn-dark">Vissza a belépéshez</button></a></td>
  </tr>
  <?php
  echo "</table>";

  ?>

</html>
<script>
  $("#submitPwdCh").click(function() {
    $(".spinner-border").fadeIn();
  });

  $(document).ready(function() {
    $(".spinner-border").hide();
  });
</script>
<style>
  .logintable {
    max-width: 300px;
    width: 100%;
    text-align: center;
    margin: 0 auto;
  }

  .rainbow {
    -webkit-animation: color 10s linear infinite;
    animation: color 10s linear infinite;
  }


  @-webkit-keyframes color {
    0% {
      color: #000000;
    }

    20% {
      color: #c91d2b;
    }

    40% {
      color: #ba833e;
    }

    60% {
      color: #0f6344;
    }

    80% {
      color: #09457a;
    }

    100% {
      color: #5f0976;
    }
  }

  @keyframes background {
    0% {
      color: #000000;
    }

    20% {
      color: #c91d2b;
    }

    40% {
      color: #ba833e;
    }

    60% {
      color: #0f6344;
    }

    80% {
      color: #09457a;
    }

    100% {
      color: #5f0976;
    }
  }
</style>