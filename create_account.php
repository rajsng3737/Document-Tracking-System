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
        <title>Create Account</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
        <link href="home_style.css" rel="stylesheet">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> 
    </head>
    <body>
        <nav class="navbar bg-body-tertiary" data-bs-theme="dark">
        <div class="container-fluid ">
            <a class="navbar-brand" href="index.php">
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
        <?php
            $empidErr = $dobErr = $nameErr = "";
            function isValidName($name) {
            $name = trim($name);
            
            if (!preg_match("/^[a-zA-Z ]*$/", $name)) {
              return false;
            }
            
            if (strlen($name) < 2) {
              return false;
            }
            
            if (strlen($name) > 50) {
              return false;
            }
            return true;
            }
            include "login_createaccount_infocheck.php";
            if($_SERVER['REQUEST_METHOD']== "POST"){
                $password = $_POST["password"];
                if (strlen($password) < 6 || !preg_match($passwordRegex, $password)) {
                    $passwordErr = "Password must have at least 6 characters, one uppercase letter, one lowercase letter and one digit";
                }
            $dob=test_input($_POST['dob']);
            if(!date($dob)){
                $dobErr = "Enter Correct date";
            }
            $employeeid=test_input($_POST["employeeid"]);
            if(empty($employeeid))  
                $empidErr = "Employee Id cannot be empty";
            if(!isValidName($_POST['name']))
                $nameErr = "Name can have only alphabets. Minimum 2. Maximum 50";
            if($dobErr == "" && $empidErr == "" && $passwordErr == "" && $emailErr == "" && $selectFacultyErr == "" && $selectDeptErr == "" && $nameErr == "")
            {
                include "databasec.php";
                $result = $result2 = false;
                $result = mysqli_query($conn,"INSERT INTO credentials(email,password,faculty,department) VALUES ('".$email."','".$_POST['password']."','".$_POST['faculty']."','".$_POST['departments']."');");
                if($result)
                    $result2 = mysqli_query($conn,"INSERT INTO userdata(email,name,employeeid,faculty,department,dob,next_doc_no) VALUES ('".$email."','".$_POST['name']."','".$_POST['employeeid']."','".$_POST['faculty']."','".$_POST['departments']."','".$_POST['dob']."','1');");
                if($result && $result2){
                    if(mysqli_affected_rows($conn) > 0){
                        echo '<div class="modal fade" id="receivedModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" style = "display:block">
                             <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header border-0">
                                        <h5 class="modal-title" id="exampleModalLongTitle">Account Created!</h5>
                                    </div>
                                    <div class="modal-body alert alert-success">
                                        Account has been created successfully.
                                    </div>
                                    <div class="modal-footer border-0">
                                        <button type="button" id = "close_modal" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <script>
                            jQuery.noConflict();
                            jQuery(document).ready(function() {
                            console.log("before modal");
                            jQuery("#receivedModal").modal("show");
                            console.log("after modal");
                            jQuery("#close_modal").click(function(){
                                jQuery("#receivedModal").modal("hide");
                            });
                        });
                    </script>';
                    }
                    else{
                        if(!$result2 && $result){
                            mysqli_query($conn,"DELETE FROM credentials WHERE email = '".$_POST['email']."';");
                        }
                        echo '<div class="modal fade" id="receivedModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" style = "display:none">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                           <div class="modal-content">
                               <div class="modal-header border-0">
                                   <h5 class="modal-title" id="exampleModalLongTitle">Error!</h5>
                               </div>
                               <div class="modal-body alert alert-danger">
                                   Account Cannot be created.
                               </div>
                               <div class="modal-footer border-0">
                                   <button type="button" id = "close_modal" class="btn btn-secondary" data-dismiss="modal">Close</button>
                               </div>
                           </div>
                       </div>
                   </div>
               <script>
                    jQuery.noConflict();
                    jQuery(document).ready(function(){
                        console.log("before modal");
                        jQuery("#receivedModal").modal("show");
                        console.log("after modal");
                        jQuery("#close_modal").click(function(){
                            jQuery("#receivedModal").modal("hide");
                    });
                });
               </script>';
                        }
                   }
                }
            }
          ?>
        <div class="container my-5 w-25">
            <form class="row g-3" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
                <div class="col-md-12">
                    <label for="name" class="form-label">Name</label>
                    <input type="name" name = "name" id = "name" class="form-control my-2" value = <?php if(isset($_POST['name'])) echo "'".$_POST['name']."'"; ?>>
                    <?php showError($nameErr); ?>
                </div>
                <div class="col-md-12">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name = "email" id = "email" class="form-control my-2" value = <?php if(isset($_POST['email'])) echo "'".$_POST['email']."'"; ?>>
                    <?php showError($emailErr); ?>
                </div>
                <div class="col-md-12">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name = "password" id = "password" class="form-control my-2">
                    <?php showError($passwordErr); ?>
                </div>
                <div class="col-md-12">
                    <label for="faculty" class = "form-label">Faculty Name</label>
                    <select id = "faculty" name="faculty" class="form-select form-select-md mb-3" aria-label=".form-select-lg example" value =>
                        <option selected  value="0">Choose Academics</option>
                        <option value="1" >Faculty Of Agriculture</option>
                        <option value="2" >Faculty Of Engineering and Technology</option>
                        <option value="3" >Faculty of Theology</option>
                        <option value="4" >Faculty Of Management, Humanities and Social Science</option>
                        <option value="5" >Faculty Of Health Science</option>
                        <option value="6" >Faculty of Science</option>
                    </select>
                    <?php showError($selectFacultyErr); ?>
                </div>
                <div class="col-md-12">
                <label for="departments" class = "form-label">Department Name</label>
                    <select id = "departments" name="departments" class="form-select form-select-md mb-3" aria-label=".form-select-lg example">
                    <option selected value="0">Choose Department</option>
                    </select>
                    <?php showError($selectDeptErr); ?>
                    <script type="text/javascript" src="selectoption.js"></script>
                </div>
                <div class="col-md-12">
                    <label for="employeeid" class="form-label">Employee Id</label>
                    <input type="text" name = "employeeid" id = "employeeid" class="form-control my-2" value = <?php if(isset($_POST['employeeid'])) echo "'".$_POST['employeeid']."'"; ?>>
                    <?php showError($empidErr); ?>
                </div>
                <div class="col-md-12">
                    <label for="inputState" class="form-label ">Date Of birth</label>
                    <input type="date" name="dob"  id ="dob" class="form-control my-2" value = <?php if(isset($_POST['dob'])) echo "'".$_POST['dob']."'"; ?>>
                    <?php showError($dobErr); ?>
                </div>
                <div class="d-grid col-lg-3 mx-auto">
                    <button type="submit" class="btn btn-primary">Create Account</button>
                </div>
            </form>
        </div>
</body>
</html>