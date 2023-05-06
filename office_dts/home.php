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
                            $res = mysqli_query($conn,"select office_id from routesteps where route_id = '".$row['route_id']."' and step_number = '".($row['FileLocation'])."';");
                            if(mysqli_num_rows($res)== 1){
                                $res_array = mysqli_fetch_array($res);
                                if($res_array['office_id'] == $_SESSION['office_id']){
                                    $modal_header = "Already Received";
                                    $modal_val = "Document Already Received at Office.<br><i>Note, you can only receive a document which has been released from an office or employee.</i>";
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
                            $office_id_query = mysqli_query($conn,"select office_id from routesteps where route_id = '".$row['route_id']."' and step_number = '".($row['FileLocation']+1)."';");
                            if(mysqli_num_rows($office_id_query) == 1){
                                $office_id = mysqli_fetch_array($office_id_query)['office_id'];
                                // 2 means school dean
                                if($office_id == 2){
                                    $doc_by_emp = mysqli_query($conn,"select employee_id from doc_emp_relationship where document_id = '".$trackingno."';");
                                    $doc_by_dept = mysqli_query($conn,"select dept_id from doc_dept_relationship where document_id = '".$trackingno."';");
                                    $doc_by_office = mysqli_query($conn,"select office_id from doc_office_relationship where document_id = '".$trackingno."';");
                                    if(mysqli_num_rows($doc_by_emp) == 1){
                                        $sdean_id_query = mysqli_query($conn,"select sdean_id from school_dean where school_id IN (select school_id from departments where dept_id IN(select dept_id from emp_dept_relationship where employee_id = '".mysqli_fetch_array($doc_by_emp)['employee_id']."'));");
                                        $sdean_id = mysqli_fetch_array($sdean_id_query)['sdean_id'];
                                        if($_SESSION['office_id'] == '2' && $_SESSION['sdean_id'] == $sdean_id){
                                            $query_res = mysqli_query($conn,"update documents set FileLocation = '".$_SESSION['office_id']."',status='Pending' where DocumentID ='".$trackingno."';");
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
                                                $modal_header = "Please Try Again";
                                                $modal_val = "Error in Receiving the Document.";
                                                $alert = "danger";
                                                include "../modal.php";
                                            }
                                        }
                                        else{
                                            $modal_header = "Un-Authorized Access";
                                            $modal_val = "You are not authorized this document yet.";
                                            $alert = "danger";
                                            include "../modal.php";
                                        }
                                    }
                                    else if(mysqli_num_rows($doc_by_dept) == 2){
                                        $sdean_id_query = mysqli_query($conn,"select sdean_id from school_dean where school_id IN (select school_id from departments where dept_id = ".mysqli_fetch_array($doc_by_dept)['dept_id']." );");
                                        $sdean_id = mysqli_fetch_array($sdean_id_query)['sdean_id'];
                                        if($_SESSION['office_id'] == 2 && $_SESSION['sdean_id'] == $sdean_id){
                                            $query_res = mysqli_query($conn,"update documents set FileLocation = '".$_SESSION['office_id']."',status='Pending' where DocumentID ='".$trackingno."';");
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
                                                $modal_header = "Please Try Again";
                                                $modal_val = "Error in Receiving the Document.";
                                                $alert = "danger";
                                                include "../modal.php";
                                            }// receive document
                                        }
                                        else{
                                            $modal_header = "Un-Authorized Access";
                                            $modal_val = "You are not authorized this document yet.";
                                            $alert = "danger";
                                            include "../modal.php";
                                        }
                                    }
                                    else if(mysqli_num_rows($doc_by_office) == 1){
                                        //right now school dean cannot receive any document from above levels
                                    }
                                }
                                //3 means faculty dean
                                else if($office_id == 3){
                                    $doc_by_emp = mysqli_query($conn,"select employee_id from doc_emp_relationship where document_id = '".$trackingno."';");
                                    $doc_by_dept = mysqli_query($conn,"select dept_id from doc_dept_relationship where document_id = '".$trackingno."';");
                                    $doc_by_sdean = mysqli_query($conn,"select sdean_id from doc_sdean_relationship where document_id = '".$trackingno."';");
                                    $doc_by_fdean = mysqli_query($conn,"select fdean_id from doc_fdean_relationship where document_id = '".$trackingno."';");
                                    $doc_by_office = mysqli_query($conn,"select office_id from doc_office_relationship where document_id = '".$trackingno."';");
                                    if(mysqli_num_rows($doc_by_emp) == 1){
                                        $fdean_id_query = mysqli_query($conn,"select fdean_id from faculty_dean where faculty_id IN (select faculty_id from schools where school_id IN (select school_id from departments where dept_id IN (select dept_id from employee where employee_id = '".mysqli_fetch_array($doc_by_emp)['employee_id']."')))");
                                        $fdean_id = mysqli_fetch_array($fdean_id_query)['fdean_id'];
                                        if($_SESSION['office_id'] == 3 && $_SESSION['fdean_id'] == $fdean_id){
                                            $query_res = mysqli_query($conn,"update documents set FileLocation = '".$_SESSION['office_id']."',status='Pending' where DocumentID ='".$trackingno."';");
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
                                        }
                                        else{
                                            $modal_header = "Un-Authorized Access";
                                            $modal_val = "You are not authorized this document yet.";
                                            $alert = "danger";
                                            include "../modal.php";
                                        }
                                    }
                                    else if(mysqli_num_rows($doc_by_dept) == 1){
                                        $fdean_id_query = mysqli_query($conn,"select fdean_id from faculty_dean where faculty_id IN (select faculty_id from schools where school_id IN(select school_id from departments where dept_id = '".mysqli_fetch_array($doc_by_dept)['dept_id']."'))");
                                        $fdean_id = mysqli_fetch_array($fdean_id_query)['fdean_id'];
                                        if($_SESSION['office_id'] == 3 && $_SESSION['fdean_id'] == $fdean_id ){
                                            $query_res = mysqli_query($conn,"update documents set FileLocation = '".$_SESSION['office_id']."',status='Pending' where DocumentID ='".$trackingno."';");
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
                                        }
                                        else{
                                            $modal_header = "Un-Authorized Access";
                                            $modal_val = "You are not authorized this document yet.";
                                            $alert = "danger";
                                            include "../modal.php";
                                        } 
                                    }
                                    else if(mysqli_num_rows($doc_by_sdean) == 1){
                                        if($_SESSION['office_id'] == 3){
                                            $receiving_query = mysqli_query($conn,"SELECT documentID from documents d
                                            INNER JOIN doc_sdean_relationship dsr ON d.documentID = dsr.document_ID
                                            INNER JOIN school_dean sd ON dsr.sdean_id = sd.sdean_id
                                            INNER JOIN schools s ON sd.school_id = s.school_id
                                            INNER JOIN faculty f ON s.faculty_id = f.faculty_id
                                            INNER JOIN faculty_dean fd ON f.faculty_id = fd.faculty_id
                                            WHERE status = 'Released' AND fd.fdean_id = '".$_SESSION['fdean_id']."' AND d.documentID = '".$trackingno."';");
                                            if(mysqli_num_rows($receiving_query) == 1){
                                                $query_res = mysqli_query($conn,"update documents set FileLocation = '".$_SESSION['office_id']."',status='Pending' where DocumentID ='".$trackingno."';");
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
                                                    unauthorized_access();
                                                }
                                            }
                                        }
                                        else
                                            unauthorized_access();
                                    }
                                    else if(mysqli_num_rows($doc_by_fdean) == 1){
                                        
                                    }
                                    else if(mysqli_num_rows($doc_by_office) == 1){
                                        
                                        //right now faculty dean cannot receive any document from above levels
                                    }
                                }
                                else if($office_id > 3){
                                    if($_SESSION['office_id'] == $office_id){
                                        $query_res = mysqli_query($conn,"update documents set FileLocation = '".$_SESSION['office_id']."',status='Pending' where DocumentID ='".$trackingno."';");
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
                                    }
                                    else{
                                        $modal_header = "Un-Authorized Access";
                                        $modal_val = "You are not authorized this document yet.";
                                        $alert = "danger";
                                        include "../modal.php";
                                    }
                                }
                            }
                            mysqli_close($conn);
                        }
                    }
                    else{
                        $errName = "receive";
                    }
                }
                else if(array_key_exists("release",$_POST)){
                    function doc_release($conn,$document_data,$trackingno){
                        if($_SESSION['office_id'] == $document_data['FileLocation']){
                            if($document_data['status'] == "Pending"){
                                //release the document
                                $releasing_query = mysqli_query($conn,"update documents set status = 'Released' where documentID = '".$trackingno."';");
                                if($releasing_query){
                                    mysqli_commit($conn);
                                    $modal_header = "Released.";
                                    $modal_val = "Document Released Succesfully.";
                                    $alert = "success";
                                    include "../modal.php";    
                                }
                                else{
                                    mysqli_rollback($conn);
                                    $modal_header = "Please Try Again";
                                    $modal_val = "Document Cannot Be Released.";
                                    $alert = "danger";
                                    include "../modal.php";
                                }
                            }
                            else if($document_data['status'] == "Released"){
                                //Already Released
                                $modal_header = "Already Released";
                                $modal_val = "Document has been already released from your office.";
                                $alert = "danger";
                                include "../modal.php";    
                            }
                        }
                        else
                            unauthorized_access();
                    }
                    function unauthorized_access(){
                        $modal_header = "Un-Authorized Access";
                        $modal_val = "You are not authorized this document yet.";
                        $alert = "danger";
                        include "../modal.php";
                    }
                    $doc_info = mysqli_query($conn,"Select * from documents where DocumentID = '".$trackingno."';");
                    if(mysqli_num_rows($doc_info) == 1){
                        $document_data = mysqli_fetch_array($doc_info);
                        if($document_data['FileLocation'] == 2){
                            $doc_by_emp = mysqli_query($conn,"select employee_id from doc_emp_relationship where document_id = '".$trackingno."';");
                            $doc_by_dept = mysqli_query($conn,"select dept_id from doc_dept_relationship where document_id = '".$trackingno."';");
                            $doc_by_sdean = mysqli_query($conn,"select sdean_id from doc_sdean_relationship where document_id = '".$trackingno."';");
                            $doc_by_fdean = mysqli_query($conn,"select fdean_id from doc_fdean_relationship where document_id = '".$trackingno."';");
                            $doc_by_office = mysqli_query($conn,"select office_id from doc_office_relationship where document_id = '".$trackingno."';");
                            if(mysqli_num_rows($doc_by_emp) == 1){
                                $sdean_id_query = mysqli_query($conn,"select sdean_id from school_dean where school_id IN (select school_id from departments where dept_id IN(select dept_id from emp_dept_relationship where employee_id = '".mysqli_fetch_array($doc_by_emp)['employee_id']."'));");
                                $sdean_id = mysqli_fetch_array($sdean_id_query)['sdean_id'];
                                if($_SESSION['office_id'] == '2' && $_SESSION['sdean_id'] == $sdean_id)
                                    doc_release($conn, $document_data, $trackingno);
                                else
                                    unauthorized_access();
                            }
                            else if(mysqli_num_rows($doc_by_dept) == 2){
                                $sdean_id_query = mysqli_query($conn,"select sdean_id from school_dean where school_id IN (select school_id from departments where dept_id = ".mysqli_fetch_array($doc_by_dept)['dept_id']." );");
                                $sdean_id = mysqli_fetch_array($sdean_id_query)['sdean_id'];
                                if($_SESSION['office_id'] == 2 && $_SESSION['sdean_id'] == $sdean_id)
                                    doc_release($conn, $document_data, $trackingno);
                                else
                                    unauthorized_access();
                            }
                            else if(mysqli_num_rows($doc_by_sdean) == 1){
                                if($_SESSION['office_id'] == 2 && mysqli_fetch_array($doc_by_sdean)['sdean_id'] == $_SESSION['sdean_id'])
                                    doc_release($conn,$document_data,$trackingno);
                                else
                                    unauthorized_access();
                            }
                            else if(mysqli_num_rows($doc_by_fdean) == 1){

                            }
                            else if(mysqli_num_rows($doc_by_office) == 1){
                                
                                //right now school dean cannot Receive/Release any document from above levels
                            }   
                        }
                        else if($document_data['FileLocation'] == 3){
                            $doc_by_emp = mysqli_query($conn,"select employee_id from doc_emp_relationship where document_id = '".$trackingno."';");
                            $doc_by_dept = mysqli_query($conn,"select dept_id from doc_dept_relationship where document_id = '".$trackingno."';");
                            $doc_by_sdean = mysqli_query($conn,"select sdean_id from doc_sdean_relationship where document_id = '".$trackingno."';");
                            $doc_by_fdean = mysqli_query($conn,"select fdean_id from doc_fdean_relationship where document_id = '".$trackingno."';");
                            $doc_by_office = mysqli_query($conn,"select office_id from doc_office_relationship where document_id = '".$trackingno."';");
                            if(mysqli_num_rows($doc_by_emp) == 1){
                                if($_SESSION['office_id'] == 3){
                                    $releasing_query = mysqli_query($conn,"SELECT 1 FROM documents d 
                                    INNER JOIN doc_emp_relationship der ON d.documentID = der.documentID
                                    INNER JOIN emp_dept_relationship edr ON der.employee_id = edr.employee_id
                                    INNER JOIN departments d ON edr.dept_id = d.dept_id
                                    INNER JOIN schools s ON d.school_id = s.school_id
                                    INNER JOIN faculty f ON s.faculty_id = f.faculty_id
                                    INNER JOIN faculty_dean fd ON f.faculty_id = fd.faculty_id
                                    WHERE
                                    d.documentID = '".$trackingno."'
                                    AND d.FileLocation = 3
                                    AND fd.fdean_id = '".$_SESSION['fdean_id']."';");
                                    doc_release($conn, $document_data, $trackingno);
                                }
                                else
                                    unauthorized_access();
                            }
                            else if(mysqli_num_rows($doc_by_dept) == 1){
                                $fdean_id_query = mysqli_query($conn,"select fdean_id from faculty_dean where faculty_id IN (select faculty_id from schools where school_id IN(select school_id from departments where dept_id = '".mysqli_fetch_array($doc_by_dept)['dept_id']."'))");
                                $fdean_id = mysqli_fetch_array($fdean_id_query)['fdean_id'];
                                if($_SESSION['office_id'] == 3 && $_SESSION['fdean_id'] == $fdean_id )doc_release($conn, $document_data, $trackingno);
                                else
                                    unauthorized_access();
                            }
                            else if(mysqli_num_rows($doc_by_sdean) == 1){
                                if($_SESSION['office_id'] = '3'){
                                    $releasing_query = mysqli_query($conn,"SELECT 1 FROM documents d 
                                    INNER JOIN doc_sdean_relationship dsr ON d.documentID = dsr.document_id
                                    INNER JOIN school_dean sd ON dsr.sdean_id = sd.sdean_id
                                    INNER JOIN schools s ON sd.school_id = s.school_id
                                    INNER JOIN faculty f ON s.faculty_id = f.faculty_id
                                    INNER JOIN faculty_dean fd ON f.faculty_id = fd.faculty_id
                                    WHERE
                                    d.documentID = '".$trackingno."'
                                    AND d.FileLocation = 3
                                    AND fd.fdean_id = '".$_SESSION['fdean_id']."';");
                                    if(mysqli_num_rows($releasing_query) == 1)
                                        doc_release($conn,$document_data,$trackingno);
                                    else
                                        unauthorized_access();
                                }
                            }
                            else if(mysqli_num_rows($doc_by_office) == 1){
                            
                            }    
                        }
                        else if($document_data['FileLocation'] > 3)
                            doc_release($conn,$document_data,$trackingno);
                        else
                            unauthorized_access();
                    }
                    else{
                        $errName = "receive";
                    }
                }
                else if(array_key_exists("tagAsTerminal",$_POST)){
                    
                }
            };
        ?>
    <?php 
        //pending documents at current office
        include "../databasec.php";
        $pending = "0";
    /*    $result_dept = mysqli_query($conn,"select count(document_id) from doc_dept_relationship where dept_id = ".$_SESSION['department']." and document_id IN (select DocumentID from documents where FileLocation ='1' and status = 'Pending');");
        $result_emp = mysqli_query($conn,"select count(document_id) from doc_emp_relationship where document_id IN (select DocumentID from documents where FileLocation ='1' and status = 'Pending') 
                                                                                                and employee_id IN (select employee_id from emp_dept_relationship where dept_id = ".$_SESSION['department'].");");
        $result_office = mysqli_query($conn,"select count(document_id) from doc_office_relationship where document_id IN (select DocumentID from documents where FileLocation ='1' and status = 'Pending');");        
            $row_result_dept = mysqli_fetch_array($result_dept);
            $row_result_emp = mysqli_fetch_array($result_emp);
            $row_result_office = mysqli_fetch_array($result_office);
            $pending = $row_result_dept['count(document_id)']+$row_result_emp['count(document_id)']+$row_result_office['count(document_id)'];
   */ ?>
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
                                                    function generate_short_form($name) {
                                                        $short_form = '';
                                                        $words = explode(' ', $name); // Split the department name into words
                                                        
                                                        foreach($words as $word) {
                                                        $first_letter = substr($word, 0, 1); // Get the first letter of each word
                                                        $short_form .= $first_letter; // Append the first letter to the short form
                                                        }
                                                        return strtoupper($short_form); // Convert the short form to uppercase and return it
                                                    }
                                                    if(array_key_exists("fdean_id",$_SESSION)){
                                                        $result = mysqli_query($conn,"select next_doc_no from faculty_dean_next_doc where fdean_id = '".$_SESSION['fdean_id']."';");
                                                        $shortform = generate_short_form($_SESSION['fdean_name']);
                                                        if(mysqli_num_rows($result)==1){
                                                            $row = mysqli_fetch_assoc($result);
                                                            $nextdoc = $row['next_doc_no'];
                                                            $_SESSION['next_doc_no'] = $nextdoc;
                                                        }
                                                    }
                                                    else if(array_key_exists("sdean_id",$_SESSION)){
                                                        $result = mysqli_query($conn,"select next_doc_no from school_dean_next_doc where sdean_id = '".$_SESSION['sdean_id']."';");
                                                        $shortform = generate_short_form($_SESSION['sdean_name']);
                                                        if(mysqli_num_rows($result)==1){
                                                            $row = mysqli_fetch_assoc($result);
                                                            $nextdoc = $row['next_doc_no'];
                                                            $_SESSION['next_doc_no'] = $nextdoc;
                                                        }
                                                    }
                                                    else if(array_key_exists("office_id",$_SESSION)){
                                                        $result = mysqli_query($conn,"select next_doc_no from office_next_doc where office_id = '".$_SESSION['office_id']."';");
                                                        $shortform = generate_short_form($_SESSION['office_name']);
                                                        if(mysqli_num_rows($result)==1){
                                                            $row = mysqli_fetch_assoc($result);
                                                            $nextdoc = $row['next_doc_no'];
                                                            $_SESSION['next_doc_no'] = $nextdoc;
                                                        }
                                                    }
                                            ?>
                                            <input readonly type="text" name="trackingno" id = "add_input" class="form-control" placeholder=<?php if($errName == "add")echo '"Enter Correct Value" id="error"'; else echo '" "'?> 
                                                value = <?php if(array_key_exists("fdean_id",$_SESSION)) echo $_SESSION['fdean_id']."_".$shortform."_".$nextdoc;
                                                                else if(array_key_exists("sdean_id",$_SESSION)) echo $_SESSION['sdean_id']."_".$shortform."_".$nextdoc;
                                                                else if(array_key_exists("office_id",$_SESSION)) echo $_SESSION['office_id']."_".$shortform."_".$nextdoc;
                                                        ?>>
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