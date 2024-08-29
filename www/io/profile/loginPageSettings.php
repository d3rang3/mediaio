<?php
/**
 * Write a custom message to the index page.
 */
session_start();
if (!in_array("system", $_SESSION["groups"])) {
    echo "Ehhez a tartalomhoz nincs hozzáférésed!";
    exit();
}

global $limit;
global $registrationLimit;
global $message;

function fetchSettings()
{
    global $limit, $registrationLimit, $message;
    //If a motd currently exists, print it here.
    if (file_exists("../data/loginPageSettings.json")) {
        $file = fopen("../data/loginPageSettings.json", "r");
        $message = fread($file, filesize("../data/loginPageSettings.json"));
        $message = json_decode($message, true);

        $message["limit"] == "true" ? $limit = true : $limit = false;
        $message["registrationLimit"] == "true" ? $registrationLimit = true : $registrationLimit = false;

        fclose($file);
    } else {
        //<p style='color:red'>Nincs MOTD beállítva.</p>
    }
}


//If the user has submitted the form, and the message is not empty, then write it to the file.
if (isset($_POST["submit"])) {
    $message = $_POST["message"];
    $color = $_POST["color"];
    $limit = isset($_POST["limit"]) ? "true" : "false";
    $registrationLimit = isset($_POST["registrationLimit"]) ? "true" : "false";
    //creat a combined JSON from the message and the color
    $message = json_encode(
        array(
            "message" => $message,
            "color" => $color,
            "limit" => $limit,
            "registrationLimit" => $registrationLimit
        )
    );
    //write the JSON to the file
    $file = fopen("../data/loginPageSettings.json", "w");
    fwrite($file, $message);
    fclose($file);
    // if(empty($_POST["message"])){
    //     //delete the file
    //     unlink("../data/loginPageSettings.json");

    // }
    //reload the page
    //header("Refresh:0");


}
fetchSettings();

include "./header.php";
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
                        drawMenuItemsLeft('profile', menuItems, 2);
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

    <h1 class="rainbow">Belépések korlátozása</h1>

    <div class="container d-flex justify-content-center">
        <form action='loginPageSettings.php' method='post' style="max-width: 400px;">
            <div class="input-group mb-3">
                <input type='text' class="form-control" name='message' placeholder='MOTD üzenet'
                    value='<?php echo $message['message']; ?>'>
                <select class="form-select" name='color' style="max-width: fit-content;">
                    <option value='red' style='color:red' <?php echo $message['color'] == 'red' ? 'selected' : ''; ?>>
                        Piros</option>
                    <option value='green' style='color:green' <?php echo $message['color'] == 'green' ? 'selected' : ''; ?>>Zöld</option>
                    <option value='orange' style='color:orange' <?php echo $message['color'] == 'orange' ? 'selected' : ''; ?>>Sárga</option>
                </select>
            </div>

            Belépések korlátozása:
            <input type='checkbox' class="form-check-input" name='limit' value='true' <?php echo $limit ? 'checked' : ''; ?>><br>

            Regisztráció korlátozása:
            <input type='checkbox' class="form-check-input" name='registrationLimit' value='true' <?php echo $registrationLimit ? 'checked' : ''; ?>><br>

            <button type='submit' class="btn btn-success" name='submit'>Mentés</button>
        </form>
    </div>
</body>
<?php
exit();

?>