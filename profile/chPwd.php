
<html>
    <?php 
    include "header.php";
        session_start();
        $username = $_SESSION['userId'];
        if(isset($_SESSION['userId'])){ ?>
            
            <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      
            <a class="navbar-brand" href="../index.php"><img src="../utility/logo2.png" height="50"></a>
					<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
					  <span class="navbar-toggler-icon"></span>
					</button>
          
					<div class="collapse navbar-collapse" id="navbarSupportedContent">
					  <ul class="navbar-nav mr-auto navbarUl">
            </ul>
            <ul class="navbar-nav navbarPhP"><li><a class="nav-link disabled timelock" href="#">⌛ <span id="time"> 10:00 </span></a></li>';
            <?php if (($_SESSION['role']=="Admin") || ($_SESSION['role']=="Boss")){ ?>
              <li><a class="nav-link disabled" href="#">Admin jogok</a></li> <?php  }?>
            </ul>
						<form class="form-inline my-2 my-lg-0" action=../utility/logout.ut.php>
                      <button class="btn btn-danger my-2 my-sm-0" type="submit">Kijelentkezés</button>
                      </form>
                      <div class="menuRight"></div>
					</div>
          <script> $( document ).ready(function() {
              menuItems = importItem("../utility/menuitems.json");
              drawMenuItemsLeft("profile",menuItems,2);
              drawMenuItemsRight('profile',menuItems,2);
            });</script>
    </nav> <?php 
            
            echo '<table class="logintable"><tr><td><p>Jelszócsere <br><h3 class="rainbow">'.$_SESSION['lastName'].' '.$_SESSION['firstName'].'</h3><br>Számára</p></td></tr>
            <form action="../utility/chPwd.ut.php" method="post">
            <tr><td><input class="form-control mb-2 mr-sm-2" type="password" name="pwd-Old" placeholder="Jelenlegi jelszó"></td></tr> <br>
            <tr><td><input class="form-control mb-2 mr-sm-2" type="password" name="pwd-New" placeholder="Új jelszó" ></td></tr> <br>
            <tr><td><input class="form-control mb-2 mr-sm-2" type="password" name="pwd-New-Check" placeholder="Új jelszó még egyszer"></td></tr> <br>
            <tr><td><br><button class="btn btn-dark" id="submitPwdCh"align=center type="submit" name="pwdCh-submit">Mehet</button></td></tr>
            <tr><td><div class="spinner-border" role="status">
            <span class="sr-only">Loading...</span>
            </div></tr></td>
            </form>
            ';
                if (isset($_GET['error'])){
                    if( $_GET['error'] == 'emptyField'){
                        echo '<tr><td><h5 class="registererror text-danger">Kérlek MINDEN mezőt tölts ki!</h5></td></tr>';
                    }else if ($_GET['error'] == 'PasswordCheck'){
                        echo '<tr><td><h5 class="registererror text-danger">A megadott jelszavak nem egyeznek, vagy túl rövid jelszót adtál meg!</h5></td></tr>';
                    }else if ($_GET['error'] == 'PasswordLenght'){
                        echo '<tr><td><h5 class="registererror text-danger">Az új jelszónak legalább 8 karakter hosszúnak kell lennie!</h5></td></tr>';
                    }else if ($_GET['error'] == 'OldPwdError'){
                        echo '<tr><td><h5 class="registererror text-danger">Hibásan adtad meg a jelenlegi jelszavadat!</h5></td></tr>';
                    }else if ($_GET['error'] == 'none'){
                    echo '<tr><td><p class="success">Successfully changed password! Please log out in order to use your brand new, shiny password! </p></td></tr>';
                    session_unset();
                    session_destroy();

                    header("Location: ../index.php?logout=pwChange");}
                }
            echo "</table>";
        }else{
            header("Location: ../index.php?XD");
            exit();
        }
    ?>
</html>
<script>
$("#submitPwdCh").click(function(){
  $(".spinner-border").fadeIn();
});

$( document ).ready(function() {
  $(".spinner-border").hide();
});
(function(){
  setInterval(updateTime, 1000);
});

function startTimer(duration, display) {
    var timer = duration, minutes, seconds;
    setInterval(function () {
        minutes = parseInt(timer / 60, 10)
        seconds = parseInt(timer % 60, 10);

        minutes = minutes < 10 ? "0" + minutes : minutes;
        seconds = seconds < 10 ? "0" + seconds : seconds;

        display.textContent = minutes + ":" + seconds;

        if (--timer < 0) {
            timer = duration;
            window.location.href = "../utility/logout.ut.php"
        }
    }, 1000);
}

window.onload = function () {
    var fiveMinutes = 3 * 60 - 1,
        display = document.querySelector('#time');
    startTimer(fiveMinutes, display);
    setInterval(updateTime, 1000);
    updateTime();
};
</script>
<style>
.logintable{
  width: 15%;
  text-align: center;
  margin: 0 auto; 
}

.rainbow {
  -webkit-animation: color 10s linear infinite;
  animation: color 10s linear infinite;  
}


@-webkit-keyframes color {
  0% { color: #000000; }
  20% { color: #c91d2b; } 
  40% { color: #ba833e; }
  60% { color: #0f6344; }
  80% { color: #09457a; }
  100% { color: #5f0976; }
}

@keyframes background {
  0% { color: #000000; }
  20% { color: #c91d2b; } 
  40% { color: #ba833e; }
  60% { color: #0f6344; }
  80% { color: #09457a; }
  100% { color: #5f0976; }
}

</style>