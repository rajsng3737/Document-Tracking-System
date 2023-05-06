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
        <?php
            include "header.php";
            $emailErr = $empidErr = $nameErr = $passwordErr = "";
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
            function showError($value){
                if(!empty($value)){
                  echo "<span hidden class='alert alert-danger d-flex align-items-center' role='alert'>$value </span>";
                }
              }
            function test_input($data) {
                $data = trim($data);
                $data = stripslashes($data);
                $data = htmlspecialchars($data);
                return $data;
            }
            $passwordRegex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).{6,}$/";
            if($_SERVER['REQUEST_METHOD']== "POST"){
                $password = $_POST["password"];
                if (strlen($password) < 6 || !preg_match($passwordRegex, $password)) {
                    $passwordErr = "Password must have at least 6 characters, one uppercase letter, one lowercase letter and one digit";
                }
            $employeeid=test_input($_POST["employeeid"]);
            if(empty($employeeid))  
                $empidErr = "Employee Id cannot be empty";
            if(!isValidName($_POST['name']))
                $nameErr = "Name can have only alphabets. Minimum 2. Maximum 50";
            if (empty($_POST["email"])) {
                $emailErr = "Email is required";
            } 
            else {
                $email = test_input($_POST["email"]);
                // check if e-mail address is well-formed
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $emailErr = "Invalid email format";
                }
            }
            if( $empidErr == "" && $passwordErr == "" && $emailErr == "" && $nameErr == "")
            {
                include "../databasec.php";
                $result = $result2 = false;
                mysqli_begin_transaction($conn, MYSQLI_TRANS_START_READ_WRITE);
                $result_name = mysqli_query($conn,"INSERT INTO employee(employee_id,employee_name) VALUES ('".$employeeid."','".$_POST['name']."');");
                $result_credentials = mysqli_query($conn,"INSERT INTO employee_credentials(employee_id,email,password) VALUES ('".$employeeid."','".$email."','".$_POST['password']."');");
                $result_next_doc = mysqli_query($conn,"INSERT INTO employee_next_doc(employee_id,next_doc_no) VALUES ('".$employeeid."','1')");
                $result_empdept_relationship = mysqli_query($conn,"INSERT INTO emp_dept_relationship(employee_id,dept_id) VALUES ('".$employeeid."','".$_SESSION['department']."');");
                if($result_name && $result_credentials && $result_next_doc && $result_empdept_relationship){
                        mysqli_commit($conn);
                        echo '<div class="modal fade" id="receivedModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" style = "display:block">
                             <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header border-0">
                                        <h5 class="modal-title" id="exampleModalLongTitle">Employee Added !</h5>
                                    </div>
                                    <div class="modal-body alert alert-success">
                                        Employee has been added successfully.
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
                    mysqli_rollback($conn);
                        echo '<div class="modal fade" id="receivedModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" style = "display:none">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header border-0">
                                            <h5 class="modal-title" id="exampleModalLongTitle">Error!</h5>
                                        </div>
                                        <div class="modal-body alert alert-danger">
                                            Employee Cannot be created.
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
        ?>
        <div class="container my-5 w-25">
            <div class = "col" >
                <h2>Employee Registration</h2>
            </div>
            <form class="row g-3" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
                <div class="col-md-12">
                    <label for="name" class="form-label">Name</label>
                    <input type="name" name = "name" id = "name" class="form-control my-2" value = <?php if(isset($_POST['name'])) echo "'".$_POST['name']."'"; ?>>
                    <?php showError($nameErr); ?>
                </div>
                <div class="col-md-12">
                    <label for="employeeid" class="form-label">Employee Id</label>
                    <input type="text" name = "employeeid" id = "employeeid" class="form-control my-2" value = <?php if(isset($_POST['employeeid'])) echo "'".$_POST['employeeid']."'"; ?>>
                    <?php showError($empidErr); ?>
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
                
                <div class="d-grid col-lg-3 mx-auto">
                    <button type="submit" class="btn btn-primary">Create Account</button>
                </div>
            </form>
        </div>
</body>
</html>