<?php
    session_start();
    if($_SESSION != true || !isset($_SESSION['loggedin'])){
        header("location: index.php");
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
        <!-- Load jQuery -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <!-- Load Bootstrap JavaScript library -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js"></script>
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
                    $result = mysqli_query($conn,"Select * from documents where DocumentID = '".$trackingno."';");
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
                    $result = mysqli_query($conn,"Select * from documents where DocumentID = '".$trackingno."';");
                    if(mysqli_num_rows($result) == 1){
                        $row = mysqli_fetch_assoc($result);
                        if($row['status']=="Pending"){
                            mysqli_begin_transaction($conn, MYSQLI_TRANS_START_READ_WRITE);
                            $res = mysqli_query($conn,"select office_id from routesteps where route_id = '".$row['route_id']."' and step_number = '".($row['step_number'])."';");
                            if(mysqli_num_rows($res)== 1){
                                $res_array = mysqli_fetch_array($res);
                                if($res_array['office_id'] == 1){
                                    $modal_header = "Already Received";
                                    $modal_val = "Document Already Received at HOD Office.<br><i>Note, you can only receive a document which has been released from a office or employee.</i>";
                                    $alert = "danger";
                                }
                                else{
                                    $modal_header = "Pending";
                                    $modal_val = "Document Pending at another Office or Employee.<br><i>Note, you can only receive a document which has been released from a office or employee.</i>";
                                    $alert = "danger";
                                }
                            include "../modal.php";
                            }
                        }
                        else if($row['status']=="Terminal"){
                            $modal_header = "Tagged Terminal";
                            $modal_val = "Document Has Been Already Tagged As Terminal<i>Note, you can only receive a document which is released from a office.</i>";
                            $alert="danger";
                            include "../modal.php";
                        }
                        else if($row['status'] == "Released"){
                            mysqli_begin_transaction($conn, MYSQLI_TRANS_START_READ_WRITE);
                            $res = mysqli_query($conn,"select office_id from routesteps where route_id = '".$row['route_id']."' and step_number = '".($row['FileLocation']+1)."';");
                            if(mysqli_num_rows($res)== 1){
                                $res_array = mysqli_fetch_array($res);
                                if($res_array['office_id'] == 1){
                                    $emp = mysqli_query($conn,"select employee_id from doc_emp_relationship where document_id = '".$trackingno."';");
                                    if(mysqli_num_rows($emp)== 1){
                                        $dept_id = mysqli_query($conn,"select dept_id from emp_dept_relationship where employee_id = '".mysqli_fetch_array($emp)['employee_id']."';");
                                        if(mysqli_fetch_array($dept_id)['dept_id'] == $_SESSION['department']){
                                            $query_res = mysqli_query($conn,"update documents set FileLocation = '1',status='Pending', step_number = '".($row['step_number']+1)."' where DocumentID ='".$trackingno."';");
                                            $query_updating_report = mysqli_query($conn,"INSERT INTO document_report(DocumentId,received_date, which_step) VALUES('".$trackingno."' ,'".date('Y-m-d H:i:s')."' ,'".($row['step_number']+1)."')");
                                            if($query_res && $query_updating_report){
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
                                                $modal_header = "Please Try Again";
                                                $modal_val = "Error in Receiving the Document.";
                                                $alert = "danger";
                                                include "../modal.php";
                                            }
                                        }
                                        else{
                                            $modal_header = "Un-Authorized Access";
                                            $modal_val = "The document you are trying to receive, belongs to a different Department.";
                                            $alert = "danger";
                                            include "../modal.php";
                                        }
                                    }
                                }
                                else{
                                    $modal_header = "Un-Authorized Acces";
                                    $modal_val = "You are not authorized to access this document yet.";
                                    $alert = "danger";
                                    include "../modal.php";
                                }
                            }
                            else if (mysqli_num_rows($res)== 0){
                                $modal_header = "Last Office";
                                $modal_val = "Already in the Last Office.";
                                $alert = "danger";
                                include "../modal.php";
                            }
                            mysqli_close($conn);
                        }
                    }
                    else{
                        $errName = "receive";
                    }
                }
                else if(array_key_exists("release",$_POST)){
                    mysqli_begin_transaction($conn, MYSQLI_TRANS_START_READ_WRITE);
                    $res = mysqli_query($conn,"select office_id from routesteps where route_id = (select route_id from documents where DocumentID = '".$trackingno."') 
                                                                        and step_number = (select step_number from documents where DocumentID = '".$trackingno."');");
                    if($res){
                        $res_array = mysqli_fetch_array($res);
                        if($res_array['office_id']==1){
                            $res = mysqli_query($conn,"select status from documents where documentID = '".$trackingno."';");
                            $status_res = mysqli_fetch_array($res);
                            if($status_res['status'] == "Released"){
                                $modal_header = "Released";
                                $modal_val = "Document has been already released.";
                                $alert = "danger";
                                include "../modal.php";
                            }
                            else if($status_res['status'] == "Pending"){
                                $res = mysqli_query($conn,"update documents set status = 'Released' where documentID = '".$trackingno."';");
                                if($res){
                                    mysqli_commit($conn);
                                    $modal_header = "Released Successfully";
                                    $modal_val = "Document has been Released.";
                                    $alert = "success";
                                    include "../modal.php";
                                }
                                else{
                                    mysqli_rollback($conn);
                                    $modal_header = "Unable to Release";
                                    $modal_val = "Please Contact Department.";
                                    $alert = "success";
                                    include "../modal.php";
                                }
                            }
                        }
                    }
                    mysqli_close($conn);        
                }
                else if(array_key_exists("tagAsTerminal",$_POST)){

                }
            };
        ?>
    <?php 
        //pending documents at current office
        include "../databasec.php";
        $pending = "0";
        $result_dept = mysqli_query($conn,"select count(document_id) from doc_dept_relationship where dept_id = ".$_SESSION['department']." and document_id IN (select DocumentID from documents where FileLocation ='1' and status = 'Pending');");
        $result_emp = mysqli_query($conn,"select count(document_id) from doc_emp_relationship where document_id IN (select DocumentID from documents where FileLocation ='1' and status = 'Pending') 
                                                                                                and employee_id IN (select employee_id from emp_dept_relationship where dept_id = ".$_SESSION['department'].");");
        $result_office = mysqli_query($conn,"select count(document_id) from doc_office_relationship where document_id IN (select DocumentID from documents where FileLocation ='1' and status = 'Pending');");        
            $row_result_dept = mysqli_fetch_array($result_dept);
            $row_result_emp = mysqli_fetch_array($result_emp);
            $row_result_office = mysqli_fetch_array($result_office);
            $pending = $row_result_dept['count(document_id)']+$row_result_emp['count(document_id)']+$row_result_office['count(document_id)'];
    ?>
    <div class="row">
        <div class = "col col-sm-2">
            <div style = "padding-left: 16px;">
                <div class="card" style="width: 100%;">
                    <div class="card-header">
                        <b>Documents</b>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><div><a href="pending_for_release.php"><img src="../ico/pending.png" width = "20" height = "20">  Pending for Release (<?php echo $pending ?>)</a></div></li>
                        <li class="list-group-item"><a href="#"><img src="../ico/terminal.png" width = "20" height = "20">  Tagged as Terminal</a></li>
                        <li class="list-group-item"><a href="add_employee.php"><img src="../ico/employee.png" width = "20" height = "20">  Add an Employee</a></li>
                        <li class="list-group-item"><a href="#"><img src="../ico/my_documents.png" width = "20" height = "20">  My Documents</a></li>
                        <li class="list-group-item"><a href="../logout.php?logout=true"><img src="../ico/logout.png" width = "20" height = "20">  Log Out</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class = "col padding-right:32px">
            <div class = "card p-4">
                <div class="card-header text-center text-white">
                    <h4>SERVICES</h4>
                </div>
                <div class = "card-body">
                    <div class="row" style = "padding-left: 8px; padding-right:8px;">
                        <div class="col-6">
                            <div class=" border border-dark" style="border-radius: 15px;">
                                <nav class="navbar">
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
                            <div class=" border border-dark" style="border-radius: 15px;">
                                    <nav class="navbar"  >
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
                            <div class=" border border-dark" style="border-radius: 15px;">
                                <nav class="navbar"  >
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
                            <div class=" border border-dark" style="border-radius: 15px;">
                                    <nav class="navbar"  >
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
                            <div class=" border border-dark" style="border-radius: 15px;">
                                <nav class="navbar"  >
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
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
  </body>
</html>