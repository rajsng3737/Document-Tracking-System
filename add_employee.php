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
        <title>Shuats DTS</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
        <link href="home_style.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> 
    </head>
    <body>
        <?php
            include "header.php";
            function test_input($data) {
                $data = trim($data);
                $data = stripslashes($data);
                $data = htmlspecialchars($data);
                return $data;
            }
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
            $nameErr = $emailErr = $empidErr = "";
            if($_SERVER["REQUEST_METHOD"]=="POST"){
                if(empty($_POST['name']) || !isValidName($_POST['name']))
                    $nameErr = "Name Cannot be Empty. Min length 2 Max length 50";

                if (empty($_POST["email"])) {
                    $emailErr = "Email is required";
                } 
                else {
                    $email = test_input($_POST["email"]);
                    // check if e-mail address is well-formed
                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $emailErr = "Invalid email format";
                    }
                if(empty($_POST['employeeid'])){
                    $empidErr = "Employee Id cannot be empty";
                }
                if($nameErr == "" && $emailErr == "" && $empidErr == ""){
                    include "databasec.php";
                    $result = mysqli_query($conn,"INSERT INTO deptusers(name,email,employeeid,faculty,department,next_doc_no) VALUES ('".$_POST['name']."','".$_POST['email']."','".$_POST['employeeid']."','".$_SESSION['faculty']."','".$_SESSION['departments']."','1');");
                    if($result){
                       $modal_header = "Success";
                       $modal_val = "Employee Added Successfully";
                       $alert = "success";
                       include "modal.php";
                    }
                    else{
                        $modal_header = "Failed";
                       $modal_val = "Employee Cannot Be Added. Please Try Again.";
                       $alert = "success";
                       include "modal.php";
                    }
                }
          }
            }
        ?>
    <div class="container my-5 w-25">
            <form class="row g-3" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
                <div class="col-md-12">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" name = "name" id = "name" class="form-control my-2" value = <?php if(isset($_POST['name'])) echo "'".$_POST['name']."'"; ?>>
                    <?php showError($nameErr); ?>
                </div>
                <div class="col-md-12">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name = "email" id = "email" class="form-control my-2" value = <?php if(isset($_POST['email'])) echo "'".$_POST['email']."'"; ?>>
                    <?php showError($emailErr); ?>
                </div>
                <div class="col-md-12">
                    <label for="employeeid" class="form-label">Employee Id</label>
                    <input type="text" name = "employeeid" id = "employeeid" class="form-control my-2" value = <?php if(isset($_POST['employeeid'])) echo "'".$_POST['employeeid']."'"; ?>>
                    <?php showError($empidErr); ?>
                </div>
                <div class="d-grid col-lg-3 mx-auto">
                    <button type="submit" class="btn btn-primary">Add Employee</button>
                </div>
            </form>
        </div>
    </body>
</html>