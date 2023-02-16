<?php
    session_start();

?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Shuats DTS</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
        <link href="home_style.css" rel="stylesheet">
    </head>
    <body>
    <nav class="navbar bg-body-tertiary" data-bs-theme="dark">
    <div class="container-fluid ">
        <a class="navbar-brand" href="#">
            <div class="container text-center">
                <div class ="row">
                    <div class = "col">
                        <img src="ico/shuatslogo.png" alt="Logo" width="50" height="50" class="d-inline-block align-text-top">
                    </div>
                    <div class = "col" style="display: flex; align-items: center;">
                        SHUATS Document Tracking System
                    </div>
                </div>
            </div>
        </a>
    </div>
    </nav>
    <h1>Hello, world!</h1>
    <div class="row">
        <div class = "col col-sm-3">
            <div style = "padding-left: 16px;">
                <div class="card" style="width: 100%;">
                    <div class="card-header" style = "background: linear-gradient(132deg, rgb(221, 221, 221) 0.00%, rgb(110, 136, 161) 100.00%);">
                        <b>Documents</b>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><div><a href="#"><img src="ico/pending.png" width = "20" height = "20">  Pending for Release</a></div></li>
                        <li class="list-group-item"><a href="#"><img src="ico/my_documents.png" width = "20" height = "20">  My Documents</a></li>
                        <li class="list-group-item"><a href="#"><img src="ico/release_receive.png" width = "20" height = "20">  Received / Released</a></li>
                        <li class="list-group-item"><a href="#"><img src="ico/terminal.png" width = "20" height = "20">  Tagged as Terminal</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <?php
        $errName = "";
        function test_input($data) {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
          }
            if ($_SERVER["REQUEST_METHOD"] == "POST"){
                $trackingNo =test_input($_POST["trackingno"]);
                include ('databasec.php');
                if(array_key_exists("track",$_POST)){
                    $result = mysqli_query($conn,"Select * from documentsrelation where dnumber = '".$trackingNo."';");
                    if(mysqli_num_rows($result)){
                        echo "success"; //will add Track Document page once other functionality done
                    }
                    else{
                        $errName = "track";
                    }
                }
                else if(array_key_exists("add",$_POST)){
                    echo ""; // have to work upon it
                }
            }
        ?>
        <div class = "col">
            <div class="row" style = "padding-left: 8px; padding-right:8px;">
                <div class="col-6">
                    <div class=" border " style="border-radius: 15px;">
                        <nav class="navbar" style="background: linear-gradient(132deg, rgb(227, 244, 253) 0.00%, rgb(170, 209, 226) 100.00%); border-top-left-radius: 15px; border-top-right-radius: 15px; ">
                            <div class="container-fluid">
                                <a class="navbar-brand">Track Document</a>
                            </div>
                        </nav>
                        <div style = "padding: 32px;">
                            <form class="input-group mb-3 my-3" action = "<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
                                <input type="text" name="trackingno" class="form-control" placeholder=<?php if($errName == "track")echo '"Enter Correct Value" id="error"'; else echo '"Tracking Number"';?>>
                                <button class="btn btn-outline-secondary" name = "track" type="submit" id="button-track"><img src="ico/Track.png" width = "20" height = "20">  Track</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class=" border " style="border-radius: 15px;">
                            <nav class="navbar" style="background: linear-gradient(132deg, rgb(227, 244, 253) 0.00%, rgb(170, 209, 226) 100.00%); border-top-left-radius: 15px; border-top-right-radius: 15px; ">
                                <div class="container-fluid">
                                    <a class="navbar-brand">Add Document</a>
                                </div>
                            </nav>
                            <div style = "padding: 32px;">
                            <form class="input-group mb-3 my-3"action = "<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
                                    <input type="text" name="trackingno" class="form-control" placeholder=<?php if($errName == "add")echo '"Enter Correct Value" id="error"'; else echo '"Tracking Number"';?>>
                                    <button class="btn btn-outline-secondary" name = "add" type="submit" id="button-track"><img src="ico/add.png" width = "20" height = "20">  Add</button>
                                </form>
                            </div>
                    </div>
                </div>
            </div>
            <div class="row" style = "padding-top:32px; padding-left: 8px; padding-right:8px;">
                <div class="col-6">
                    <div class=" border " style="border-radius: 15px;">
                        <nav class="navbar" style="background: linear-gradient(132deg, rgb(227, 244, 253) 0.00%, rgb(170, 209, 226) 100.00%); border-top-left-radius: 15px; border-top-right-radius: 15px; ">
                            <div class="container-fluid">
                                <a class="navbar-brand">Receive Document</a>
                            </div>
                        </nav>
                        <div style = "padding: 32px;">
                            <form class="input-group mb-3 my-3"action = "<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
                                <input type="text" name="trackingno" class="form-control" placeholder=<?php if($errName == "receive")echo '"Enter Correct Value" id="error"'; else echo '"Tracking Number"';?>>
                                <button class="btn btn-outline-secondary" name = "receive" type="submit" id="button-track"><img src="ico/receive.png" width = "20" height = "20">  Receive</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class=" border " style="border-radius: 15px;">
                            <nav class="navbar" style="background: linear-gradient(132deg, rgb(227, 244, 253) 0.00%, rgb(170, 209, 226) 100.00%); border-top-left-radius: 15px; border-top-right-radius: 15px; ">
                                <div class="container-fluid">
                                    <a class="navbar-brand">Release Document</a>
                                </div>
                            </nav>
                            <div style = "padding: 32px;">
                            <form class="input-group mb-3 my-3" action = "<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
                                    <input type="text" name="trackingno" class="form-control" placeholder=<?php if($errName == "releease")echo '"Enter Correct Value" id="error"'; else echo '"Tracking Number"';?>>
                                    <button class="btn btn-outline-secondary" name = "release" type="submit" id="button-track"><img src="ico/release.png" width = "24" height = "24">  Release</button>
                                </form>
                            </div>
                    </div>
                </div>
            </div>
            <div class="row" style = "padding-top:32px; padding-left: 8px; padding-right:8px;">
                <div class="col-6">
                    <div class=" border " style="border-radius: 15px;">
                        <nav class="navbar" style="background: linear-gradient(132deg, rgb(227, 244, 253) 0.00%, rgb(170, 209, 226) 100.00%); border-top-left-radius: 15px; border-top-right-radius: 15px; ">
                            <div class="container-fluid">
                                <a class="navbar-brand">Tag as Terminal</a>
                            </div>
                        </nav>
                        <div style = "padding: 32px;">
                            <form class="input-group mb-3 my-3" action = "<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
                                <input type="text" name="trackingno" class="form-control" placeholder=<?php if($errName == "tagAsTerminal")echo '"Enter Correct Value" id="error"'; else echo '"Tracking Number"';?>>
                                <button class="btn btn-outline-secondary" name = "tagAsTerminal" type="submit" id="button-track"><img src="ico/terminal.png" width = "20" height = "20">  Tag</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
  </body>
</html>