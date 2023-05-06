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
    <script src = "loginScript.js"></script>
    <script src = "selectionScript.js"></script>
    <title>SHUATS DTS</title>
  </head>
  <body>
    <?php 
    function showError($value){
        if(!empty($value)){
            echo "<span hidden class='alert alert-danger d-flex align-items-center' role='alert'>$value </span>";
        }
      }
// function to sanitize the form data
    function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
      $email = $password = $select = "";
      $emailErr = $passwordErr = $selectFacultyErr = $selectSchoolErr = $selectOfficeErr = "";
      // define the regular expression for password
      $passwordRegex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).{6,}$/";
      // define the valid options for select
      $validFacultyOptions = array("1", "2", "3", "4", "5", "6");
      // check if the form is submitted
      if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // validate the email format
          if (empty($_POST["email"])) {
            $emailErr = 'Email is required" id="error"';
          } 
          else {
            $email = test_input($_POST["email"]);
            // check if e-mail address is well-formed
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
              $emailErr = "Invalid email format";
              }
          }
          // validate the select value
          if($_POST["offices"] == 0){
            $selectOfficeErr = "Please select an option";
          }
          else if($_POST["offices"] == 1){
            if ($_POST["select_faculty"] == 0) {
              $selectFacultyErr = "Please select an option";
            }
            else {
              $select = test_input($_POST["select_faculty"]);
              if (!in_array($select, $validFacultyOptions)) {
                $selectFacultyErr = "Invalid option selected";
              }  
            }
          }
          else if($_POST["offices"] == 2){
            if ($_POST["select_faculty"] == 0) {
              $selectFacultyErr = "Please select an option";
            }
            else {
              $select = test_input($_POST["select_faculty"]);
              if (!in_array($select, $validFacultyOptions)) {
                $selectFacultyErr = "Invalid option selected";
              }
            }
            if($_POST["select_school"]== 0){
              $selectSchoolErr = "Please select an option";
            }
          }
          if (empty($_POST["password"])) {
              $passwordErr = 'Password is required" id="error"';
          }
          if ( $passwordErr == "" && $selectFacultyErr == "" && $selectSchoolErr == "" && $selectOfficeErr == "" && $emailErr == "") {
              include ('../databasec.php');
              if($_POST['offices']== 1){
                $result = mysqli_query($conn,"SELECT fdean_id from faculty_dean_credentials where fdean_id in(select fdean_id from faculty_dean where faculty_id = '".$_POST['select_faculty']."') and fdean_email = '".$email."' and fdean_password = '".test_input($_POST["password"])."';");
                if(mysqli_num_rows($result) == 1){
                    session_start();
                    $_SESSION['loggedin'] = true;
                    $_SESSION['office_id'] = 3;
                    $_SESSION['fdean_id'] = mysqli_fetch_array($result)['fdean_id'];
                    $result2 = mysqli_query($conn,"SELECT fdean_name from faculty_dean where fdean_id = '".$_SESSION['fdean_id']."';");
                    $_SESSION['fdean_name']= mysqli_fetch_array($result2)['fdean_name'];
                    header("location: home.php");
                  }
                  else
                    echo "Invalid Data";
              }
              else if($_POST['offices']== 2){
                $result = mysqli_query($conn,"SELECT sdean_id from school_dean_credentials where sdean_id in( select sdean_id from school_dean where school_id in(select school_id from schools where faculty_id = '".$_POST['select_faculty']."')) and sdean_email = '".$email."' and sdean_password = '".test_input($_POST["password"])."';");
                if(mysqli_num_rows($result) == 1){
                    session_start();
                    $_SESSION['loggedin'] = true;
                    $_SESSION['office_id'] = 2;
                    $_SESSION['sdean_id'] = mysqli_fetch_array($result)['sdean_id'];
                    $result2 = mysqli_query($conn,"SELECT sdean_name from school_dean where sdean_id = '".$_SESSION['sdean_id']."';");
                    $_SESSION['sdean_name']= mysqli_fetch_array($result2)['sdean_name'];
                    header("location: home.php");
                }
                else{
                    echo var_dump($result);
                }
              }
              else if($_POST['offices'] == 3){
                $result = mysqli_query($conn,"SELECT office_id from office_credentials where office_email = '".$email."' and office_password = '".test_input($_POST["password"])."';");
                if(mysqli_num_rows($result) == 1){
                    session_start();
                    $_SESSION['loggedin'] = true;
                    $_SESSION['office_id'] = mysqli_fetch_array($result)['office_id'];
                    $result2 = mysqli_query($conn,"SELECT office_name from offices where office_id = '".$_SESSION['office_id']."';");
                    $_SESSION['office_name']= mysqli_fetch_array($result2)['office_name'];
                    header("location: home.php");
                }
                else{
                    echo "Invalid Data";
                }
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
		      <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
                    <div class="mb-3 row">
                    <select id="offices" name="offices" class="form-select form-select-lg mb-3" aria-label=".form-select-lg example">
                        <option value="0" <?php if(isset($_POST['offices']) && $_POST['offices'] == '0') echo 'selected'; ?>>Choose Office</option>
                        <option value="1" <?php if(isset($_POST['offices']) && $_POST['offices'] == '1') echo 'selected'; ?>>Faculty Dean</option>
                        <option value="2" <?php if(isset($_POST['offices']) && $_POST['offices'] == '2') echo 'selected'; ?>>School Dean</option>
                        <option value="3" <?php if(isset($_POST['offices']) && $_POST['offices'] == '3') echo 'selected'; ?>>Main offices</option>
                    </select>
                        <?php showError($selectOfficeErr)?>
                    </div>
                    <div class="mb-3 row" id = "div_select_faculty" style="display:none;">
                    <select id="select_faculty" name="select_faculty" class="form-select form-select-lg mb-3" aria-label=".form-select-lg example">
                        <option value="0" <?php if(isset($_POST['select_faculty']) && $_POST['select_faculty'] == '0') echo 'selected'; ?>>Choose Faculty</option>
                        <option value="1" <?php if(isset($_POST['select_faculty']) && $_POST['select_faculty'] == '1') echo 'selected'; ?>>Faculty Of Agriculture</option>
                        <option value="2" <?php if(isset($_POST['select_faculty']) && $_POST['select_faculty'] == '2') echo 'selected'; ?>>Faculty Of Engineering and Technology</option>
                        <option value="3" <?php if(isset($_POST['select_faculty']) && $_POST['select_faculty'] == '3') echo 'selected'; ?>>Faculty of Theology</option>
                        <option value="4" <?php if(isset($_POST['select_faculty']) && $_POST['select_faculty'] == '4') echo 'selected'; ?>>Faculty Of Management, Humanities and Social Science</option>
                        <option value="5" <?php if(isset($_POST['select_faculty']) && $_POST['select_faculty'] == '5') echo 'selected'; ?>>Faculty Of Health Science</option>
                        <option value="6" <?php if(isset($_POST['select_faculty']) && $_POST['select_faculty'] == '6') echo 'selected'; ?>>Faculty of Science</option>
                    </select>
                            <?php showError($selectFacultyErr)?>
                    </div>
                    <div class="mb-3 row" id = "div_select_school" style="display:none;">
                            <select id="select_school" name="select_school" class="form-select form-select-lg mb-3" aria-label=".form-select-lg example">
                                <option selected value="0">Choose School</option>
                            </select>
                            <?php showError($selectSchoolErr)?>
                    </div>
                    <div id="login" style="display:none; ">
                        <input type="text" name="email" placeholder="<?php if($emailErr != "") echo $emailErr; else echo "Enter Email id ";?>">
                        <input type="password" name="password" placeholder="<?php if($passwordErr != "") echo $passwordErr; else echo "Password";?>">
                        <input type="submit" name = "submit" value="Login">
                    </div>
		      </form>
              <script>
                    
                </script>
	      </div>
      </div>
  </body>
  </html>