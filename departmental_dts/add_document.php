<?php
    session_start();
    if($_SESSION != true || !isset($_SESSION['loggedin'])){
        header("location: index.php");
        exit;
    }
    else if(!isset($_SESSION['document_no'])){
        header("location.home.php");
    }
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Shuats DTS</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
        <link href="home_style.css" rel="stylesheet">
        <!-- Load jQuery -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <!-- Load Bootstrap JavaScript library -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js"></script>
    </head>
    <body>
    <?php include "header.php"; ?>
    <?php
        $titleErr = $remarksErr ="";
        if($_SERVER["REQUEST_METHOD"] == "POST"){
            if($_POST['trackingno'] != $_SESSION['document_no']){
                session_destroy();
                header("location: index.php");
            }
            $title = htmlspecialchars($_POST['title']);
            if (strlen($title) > 250)
                $titleErr = "Title too long.";
            else if(strlen($title)<3)
                $titleErr = "Title too short.";
            $remarks = htmlspecialchars($_POST['remark']);
            if(strlen($remarks)>500)
                $remarksErr = "Remark too Long";
            if($titleErr == "" && $remarksErr == ""){
                include "../databasec.php";
                $time = date('Y-m-d H:i:s');
                mysqli_begin_transaction($conn, MYSQLI_TRANS_START_READ_WRITE);
                $route_id_result = mysqli_query($conn,"Select route_id from document_type where type_id = "."'".$_POST['type']."';");
                if(mysqli_num_rows($route_id_result)==1){
                    $row = mysqli_fetch_array($route_id_result);
                    $route_id = $row['route_id'];
                    $result = mysqli_query($conn,"INSERT INTO documents(DocumentID, DocumentName, DocumentType, remark, CreationDate, LastModifiedDate,route_id, FileLocation,step_number, status) 
                    VALUES ('".$_SESSION['document_no']."','".$title."','".$_POST['type']."','".$remarks."','".$time."','".$time."','".$route_id."','"."1','1','Pending');");
                    if($result){
                        $result = mysqli_query($conn,"INSERT INTO doc_dept_relationship(document_id,dept_id) values ('".$_SESSION['document_no']."','".$_SESSION['department']."');");
                        if($result){
                            $updatedValue = mysqli_query($conn,"UPDATE departments SET next_doc_no = ".($_SESSION['next_doc_no']+1)." where dept_id = ".$_SESSION['department']);
                            if($updatedValue){
                                mysqli_commit($conn);
                                $modal_header = "Added.";
                                $modal_val = "Document Added Succesfully.";
                                $alert = "success";
                                include "../modal.php";
                            }
                            else{
                                mysqli_rollback($conn);
                                $modal_header = "Please Try Again";
                                $modal_val = "Document Cannot Be Added.";
                                $alert = "danger";
                                include "../modal.php";
                            }
                        }
                        else{
                            mysqli_rollback($conn);
                            $modal_header = "Please Try Again";
                            $modal_val = "Document Cannot Be Added.";
                            $alert = "danger";
                            include "../modal.php";
                        }
                    }
                    else{
                        mysqli_rollback($conn);
                        $modal_header = "Please Try Again";
                        $modal_val = "Document Cannot Be Added.";
                        $alert = "danger";
                        include "../modal.php";
                    }
                }
                else{
                        $modal_header = "Please Try Again";
                        $modal_val = "Document Cannot Be Added.";
                        $alert = "danger";
                        include "../modal.php";
                }
                mysqli_close($conn);
        }
        }
    ?>
   
    <div style= "padding-top:15pt; display: flex; justify-content: center; align-items: center; ">
        <div class = "card " style = "padding:10pt; width:50%;">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
            <h2 style="color:saddlebrown">Add Document</h2>
            <label for = "trackingno" class = "form-label pt-4"><b>Tracking Number</b></label>
            <input  readonly type = "text" name = "trackingno"  value = <?php if(isset($_SESSION['document_no'])) echo "'".$_SESSION['document_no']."'";?> class = "form-control bg-secondary text-white">
            <label for = "trackingno" class = "form-label"><i>Please make sure to attach the same document number as mentioned above.</i></label>
            
            <label for = "Title" class = "form-label pt-4"><b>Title</b></label>
            <input type = "text" name = "title"  class = "form-control"></input>
            <label for = "Title" class = "form-label"><i>- You may remove any sensitive information(monetary amount,names) from the title if they are not necessary in the tracking document.
                <br>- Max Length: 250 characters
            </i></label>

            <label for = "type" class = "form-label pt-4"><b>Type</b></label>
                <select name = "type" class = "form-control">
                    <option value = '1'>Leave/Promotion/Service files</option>
                </select>
            
            <label for = "remark" class = "form-label pt-4"><b>Remarks</b></label>
            <input type = "text" name = "remark"  class = "form-control"></input>
            <label for = "remark" class = "form-label"><i>- Max Length: 500- characters</i></label>

            <label for="notification" class = "form-label pt-4"><b>Email Notifications</b></label>
            <div style = "display: flex;">
                <input type="checkbox" name="notification"  checked autocomplete="off">
                <i style = "padding-left: 2pt;">Notify me whenever someone processes this document.</i>
            </div>
            <div  style = "padding-top:15pt; display: flex; justify-content: center; align-items: center;">
            <button class ="btn btn-primary" type = "submit" >Finalize Document</button> 
            </div>
            </form>
        </div>
    </div>
    
</body>
</html>