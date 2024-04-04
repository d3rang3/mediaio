<!-- Default header  -->

<head>
  <link href='../style/common.scss' rel='stylesheet' />
  <link rel="icon" type="image/x-icon" href="../logo.ico">
  <div class="UI_loading"><img class="loadingAnimation" src="../utility/mediaIO_loading_logo.gif"></div>
  <meta charset='utf-8' />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css"
    integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
  <script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>
  <script src="https://kit.fontawesome.com/2c66dc83e7.js" crossorigin="anonymous"></script>
  <script src="../utility/_initMenu.js" crossorigin="anonymous"></script>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Arpad Media IO</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <script src="frontEnd/projektGen.js" crossorigin="anonymous"></script>
  <script src="frontEnd/taskGen.js" crossorigin="anonymous"></script>
  <script src="frontEnd/fetchData.js" crossorigin="anonymous"></script>
  <script src="frontEnd/taskAnswers.js" crossorigin="anonymous"></script>
  <script src="frontEnd/dragAndDrop.js" crossorigin="anonymous" defer></script>
  <script src="frontEnd/toastManager.js" crossorigin="anonymous" defer></script>


  <?php if (in_array("admin", $_SESSION["groups"])) { ?>
    <script src="frontEnd/projektSettings.js" crossorigin="anonymous"></script>
    <script src="frontEnd/adminButtons.js" crossorigin="anonymous"></script>
  <?php } ?>

  <link rel="stylesheet" href="style/projectMStyle.scss">

  <script>
    $(window).on('load', function () {
      console.log("Finishing UI");
      setInterval(() => {
        $(".UI_loading").fadeOut("slow");
      }, 200);
    });
  </script>
</head>