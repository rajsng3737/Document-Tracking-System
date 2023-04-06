<?php
    if(isset($_SESSION['loggedin'])){
        header("location: home.php");
        exit;
    }
?>
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="style.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <title>SHUATS DTS</title>
  </head>
  <body>
  <?php
      function test_input($data) {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
      }
      function showError($value){
        if(!empty($value)){
          echo "<span hidden class='alert alert-danger d-flex align-items-center' role='alert'>$value </span>";
        }
      }
      $email = $password = "";
      $emailErr = $passwordErr = "";
      // define the regular expression for password
      $passwordRegex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).{6,}$/";
      // check if the form is submitted
      if ($_SERVER["REQUEST_METHOD"] == "POST") {
      // validate the email format
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
      if (empty($_POST["password"])) {
        $passwordErr = "Password is required";
      }
      if($emailErr == "" && $passwordErr == ""){
        include "../databasec.php";
        mysqli_begin_transaction($conn,MYSQLI_TRANS_START_READ_WRITE);
        $result = mysqli_query($conn,"SELECT * from employee where email = '".$email."' and password = '".$_POST['password']."';");
        if(mysqli_num_rows($result)==1){
          session_start();
          $_SESSION['loggedin'] = true;
          $result_array = array();
          $result_array = mysqli_fetch_array($result);
          $_SESSION['employeename']=$result_array['employee_name'];
          $_SESSION['employeeid']=$result_array['employee_id'];
          header("location:home.php");
        }
        else{
          $modal_header = "Invalid Credentials.";
          $modal_val = "Contact Department.";
          $alert = "danger";
          include "../modal.php";
        }
      }
    }
    ?>
    <div class="background"></div>    
    <div class="card w-50">
        <div class="card-header">
		      <img src="../ico/shuatslogo.png" alt="Shuats Logo">
	      </div>
	      <div class="card-body">
		      <form method = "post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <div class="mb-3 row">
              <label for="inputEmail" class="col-sm-2 col-form-label my-2">Email</label>
              <div class="col-sm-10">
                <input type="email" name="email" class="form-control my-2" id="inputEmail">
                <?php showError($emailErr)?>
            </div>
			      <div class="mb-3 row">
              <label for="inputPassword" class="col-sm-2 col-form-label my-2">Password</label>
              <div class="col-sm-10">
                <input type="password" name="password" class="form-control my-2" id="inputPassword">
                <?php showError($passwordErr)?>
            </div>
            <div>
			        <button type="submit" class="btn btn-primary">Login</button>
            </div>
		      </form>
	      </div>
      </div>
  </body>
  </html>