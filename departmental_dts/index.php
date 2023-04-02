<?php
    session_start();
    if($_SESSION == true && isset($_SESSION['loggedin'])){
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

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <title>Login to SHUATS DTS</title>
  </head>
  <body>
    <?php
          // if there are no errors, proceed with the form data
          include "login_createaccount_infocheck.php";
          if($_SERVER["REQUEST_METHOD"] == "POST"){
            if (empty($_POST["password"])) {
              $passwordErr = "Password is required";
            }   
            if ( $passwordErr == "" && $selectFacultyErr == "" && $selectDeptErr == "" &&$selectSchoolErr == "") {
              include ('../databasec.php');
              $result = mysqli_query($conn,"SELECT dept_id from departments where password = '".test_input($_POST["password"])."' 
                                     and dept_id = ".test_input($_POST["department"]).";");
              if(mysqli_num_rows($result) == 1){
                session_start();
                $_SESSION['loggedin'] = true;
                $_SESSION['faculty'] = test_input($_POST["faculty"]);
                $_SESSION['school'] = test_input($_POST["school"]);
                $_SESSION['department'] = test_input($_POST["department"]);
                header("location: home.php");
              }
              else
                echo "Invalid Data";
            }
          }
    ?>
    <div class="container my-5 w-25">
      <div class ="col-4 mx-auto mb-4 w-100">
        <img src="../ico/shuatslogo.png" class="img-fluid" alt="Shuats Logo">
      </div>
      <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
        <select id = "faculty" name="faculty" class="form-select form-select-lg mb-3" aria-label=".form-select-lg example">
            <option selected value="0">Choose Academics</option>
            <option value="1">Faculty Of Agriculture</option>
            <option value="2">Faculty Of Engineering and Technology</option>
            <option value="3">Faculty of Theology</option>
            <option value="4">Faculty Of Management, Humanities and Social Science</option>
            <option value="5">Faculty Of Health Science</option>
            <option value="6">Faculty of Science</option>
        </select>
        <?php showError($selectFacultyErr)?>
        <select id = "school" name="school" class="form-select form-select-lg mb-3" aria-label=".form-select-lg example">
          <option selected value="0">Choose School</option>
          <script type="text/javascript" src="selectSchool.js"></script>
        </select>
        <?php showError($selectSchoolErr)?>
        <select id = "department" name="department" class="form-select form-select-lg mb-3" aria-label=".form-select-lg example">
          <option selected value="0">Choose Department</option>
          <script type="text/javascript" src="selectDepartment.js"></script>
        </select>
        <?php showError($selectDeptErr)?>
        <div class="mb-3 row">
          <label for="inputPassword" class="col-sm-2 col-form-label my-2">Password</label>
          <div class="col-sm-10">
            <input type="password" name="password" class="form-control my-2" id="inputPassword">
            <?php showError($passwordErr)?>
          </div>
          <div class="d-grid gap-2 col-6 mx-auto">
            <button type="submit" class="btn btn-primary mb-3 my-3">Login</button>
          </div>
        </div>
      </form>
      <a class ="d-grid text-center mx-auto" href="create_account.php">Create Account</a>
    </div>
    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
  </body>
</html>