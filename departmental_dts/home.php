<?php
    session_start();
    if($_SESSION != true || !isset($_SESSION['loggedin'])){
        header("location: departmental_login.php");
        exit;
    }
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Shuats DTS Home</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
        <link href="home_style.css" rel="stylesheet">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> 
    </head>
    <body>
        <?php include "header.php";?>
        <?php
        $errName = "";
        function test_input($data) {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
          }
            if ($_SERVER["REQUEST_METHOD"] == "POST"){
                $trackingno = test_input($_POST["trackingno"]);
                include ('../databasec.php');
                if(array_key_exists("track",$_POST)){
                    $result = mysqli_query($conn,"Select * from document where DocumentID = '".$trackingno."';");
                    if(mysqli_num_rows($result) == 1){
                        $result_array = array();
                        while($row = mysqli_fetch_assoc($result)){
                            $result_array = $row;
                        }
                        $_SESSION['track_results'] = $result_array;
                        header("location: track_document.php");
                    }
                    else{
                        $errName = "track";
                    }
                }
                else if(array_key_exists("add",$_POST)){
                    if(empty($trackingno)){
                        $errName = "add";
                    }
                    else{
                        $_SESSION['document_no'] = test_input($trackingno);
                        header("location: add_document.php");
                    }
                }
                else if(array_key_exists("receive",$_POST)){
                    $result = mysqli_query($conn,"Select * from document where DocumentID = '".$trackingno."';");
                    if(mysqli_num_rows($result) == 1){
                        $row = mysqli_fetch_assoc($result);
                        if($row['FileLocation'] == $_SESSION['department'])
                        {
                            $modal_header = "Already Received";
                            $modal_val = "Document Has Been Already Received.";
                            $alert = "danger";
                            include "../modal.php";
                        }
                        else if($row['status']=="pending"){
                            $modal_header = "Pending";
                            $modal_val = "Document Pending At Another Office<br><i>Note, you can only receive a document which is released from a office.</i>";
                            $alert = "danger";
                            include "../modal.php";
                        }
                        else if($row['status']=="terminal"){
                            $modal_header = "Tagged Terminal";
                            $modal_val = "Document Has Been Already Tagged As Terminal<i>Note, you can only receive a document which is released from a office.</i>";
                            $alert="danger";
                            include "../modal.php";
                        }
                        else if($row['status'] == "released"){
                            mysqli_begin_transaction($conn, MYSQLI_TRANS_START_READ_WRITE);
                            $query_res = mysqli_query($conn,"update document set FileLocation = '".$_SESSION['department'].",status='received' where DocumentID ='".$trackingno."';");
                            if($query_res){
                                if(mysqli_affected_rows($conn) == 1){
                                    mysqli_commit($conn);
                                    $modal_header = "Received.";
                                    $modal_val = "Document Received Succesfully.";
                                    $alert = "success";
                                    include "../modal.php";
                                }
                                else{
                                    mysqli_rollback($conn);
                                    $modal_header = "Please Try Again";
                                    $modal_val = "Document Cannot Be Recieved.";
                                    $alert = "danger";
                                    include "../modal.php";
                                }
                            }
                            else{
                                mysqli_rollback($conn);
                            }
                            mysqli_close($conn);
                        }
                    }
                    else{
                        $errName = "receive";
                    }
                }
                else if(array_key_exists("release",$_POST)){
                    
                }
                else if(array_key_exists("tagAsTerminal",$_POST)){

                }
            };
        ?>
    <?php 
        //pending documents at current office
        include "../databasec.php";
        $pending = "0";
        $result = mysqli_query($conn,"select count(DocumentID) from document where FileLocation='".$_SESSION['department']."';");
        if(mysqli_num_rows($result)>0){
            $row = mysqli_fetch_array($result);
            $pending = $row['count(DocumentID)'];
        }
    ?>
    <div class="row">
        <div class = "col col-sm-3">
            <div style = "padding-left: 16px;">
                <div class="card" style="width: 100%;">
                    <div class="card-header" style = "background: linear-gradient(132deg, rgb(221, 221, 221) 0.00%, rgb(110, 136, 161) 100.00%);">
                        <b>Documents</b>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><div><a href="#"><img src="../ico/pending.png" width = "20" height = "20">  Pending for Release (<?php echo $pending ?>)</a></div></li>
                        <li class="list-group-item"><a href="#"><img src="../ico/terminal.png" width = "20" height = "20">  Tagged as Terminal</a></li>
                        <li class="list-group-item"><a href="add_employee.php"><img src="../ico/employee.png" width = "20" height = "20">  Add an Employee</a></li>
                        <li class="list-group-item"><a href="#"><img src="../ico/employee.png" width = "20" height = "20">  My Documents</a></li>
                        <li class="list-group-item"><a href="../logout.php?logout=true"><img src="../ico/logout.png" width = "20" height = "20">  Log Out</a></li>
                    </ul>
                </div>
            </div>
        </div>
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
                                <button class="btn btn-outline-secondary" name = "track" type="submit" id="button-track"><img src="../ico/Track.png" width = "20" height = "20">  Track</button>
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
                                        <?php
                                            function generate_short_form($department_name) {
                                                $short_form = '';
                                                $words = explode(' ', $department_name); // Split the department name into words
                                                
                                                foreach($words as $word) {
                                                  $first_letter = substr($word, 0, 1); // Get the first letter of each word
                                                  $short_form .= $first_letter; // Append the first letter to the short form
                                                }
                                                
                                                return strtoupper($short_form); // Convert the short form to uppercase and return it
                                              }
                                            $result = mysqli_query($conn,"select next_doc_no from departments where dept_id = ".$_SESSION['department']);
                                            $shortform = generate_short_form($deptArr[$_SESSION['department']-1]);
                                            if(mysqli_num_rows($result)==1){
                                                $row = mysqli_fetch_assoc($result);
                                                $nextdoc = $row['next_doc_no'];
                                                $_SESSION['next_doc_no'] = $nextdoc;
                                            }
                                    ?>
                                    <input readonly type="text" name="trackingno" id = "add_input" class="form-control" placeholder=<?php if($errName == "add")echo '"Enter Correct Value" id="error"'; else echo '" "'?> value = <?php echo $_SESSION['department']."_".$shortform."_".$nextdoc ?>>
                                <button class="btn btn-outline-secondary" name = "add" type="submit" id="button-track"><img src="../ico/add.png" width = "20" height = "20">  Add</button>
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
                                <button class="btn btn-outline-secondary" name = "receive" type="submit" id="button-track"><img src="../ico/receive.png" width = "20" height = "20">  Receive</button>
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
                                    <button class="btn btn-outline-secondary" name = "release" type="submit" id="button-track"><img src="../ico/release.png" width = "24" height = "24">  Release</button>
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
                                <button class="btn btn-outline-secondary" name = "tagAsTerminal" type="submit" id="button-track"><img src="../ico/terminal.png" width = "20" height = "20">  Tag</button>
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