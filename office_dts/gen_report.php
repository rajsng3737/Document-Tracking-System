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
        <title>Generate Report</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
        <link href="home_style.css" rel="stylesheet">
        <!-- Load jQuery -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <!-- Load Bootstrap JavaScript library -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js"></script>
        <script src = "gen_report_script.js"></script>
    </head>
    <body>
        <?php include "header.php";
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
        $validFacultyOptions = array("1", "2", "3", "4", "5", "6");
          $selectFacultyErr = $selectSchoolErr = $selectOfficeErr = $selectMainOfficeErr = "";
          if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
              else if($_POST["offices"] == 3){
                include('../databasec.php');
                if($_POST['select_main_office'] == 0){
                    $selectMainOfficeErr = "Please Select an Option";
                }
              }
              if ($selectFacultyErr == "" && $selectSchoolErr == "" && $selectOfficeErr == "" && $selectMainOfficeErr == "") {
                  include ('../databasec.php');
                  if($_POST['offices']== 1){
                    $_SESSION['office_val'] = 1;
                    $_SESSION['select_faculty'] = $_POST['select_faculty'];
                    header('location: show_report.php');
                  }
                  else if($_POST['offices']== 2){
                    $_SESSION['office_val'] = 2;
                    $_SESSION['select_faculty'] = $_POST['select_faculty'];
                    $_SESSION['select_school'] = $_POST['select_school'];
                    header('location: show_report.php');
                  }
                  else if($_POST['offices'] == 3){
                    $_SESSION['office_val'] = 3;
                    header('location: show_report.php');
                  }
              }
            }
        ?>
        <div style= "padding-top:15pt; display: flex; justify-content: center; align-items: center; ">
            <div class = "card " style = "padding:10pt; width:50%;">
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
                        <?php showError($selectSchoolErr)?>
                    </div>
                    <div class="mb-3 row" id = "div_select_school" style="display:none;">
                        <select id="select_school" name="select_school" class="form-select form-select-lg mb-3" aria-label=".form-select-lg example">
                            <option selected value="0">Choose School</option>
                        </select>
                        <?php showError($selectSchoolErr)?>
                    </div>
                    <div class="mb-3 row" id = "div_select_office" style="">
                        <select id="select_main_office" name="select_main_office" class="form-select form-select-lg mb-3" aria-label=".form-select-lg example">
                            <option selected value="0">Select Main Office</option>
                            <?php showError($selectMainOfficeErr)?>
                        </select>
                    </div>
                    <div id="login" style="display:none; ">
                        <input type="submit" name = "submit" value="Submit">
                    </div>
		      </form>
            </div>
        </div>
    </body>
</html>