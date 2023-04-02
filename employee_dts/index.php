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
        $result = mysqli_query($conn,"SELECT * from employee where ");
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
			      <div class="col-sm">
				      <label for="email">Email address</label>
              <input type="email" class="form-control" id="email" aria-describedby="email" placeholder="Enter email">
			      </div>
			      <div class="col-sm">
				      <label for="password">Password</label>
				      <input type="password" class="form-control" id="password" placeholder="Password">
			      </div>
            <div>
			        <button type="submit" class="btn btn-primary">Login</button>
            </div>
		      </form>
	      </div>
      </div>
  </body>
  </html>