<?php
// function to sanitize the form data
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
      $email = $password = $select = "";
      $emailErr = $passwordErr = $selectFacultyErr = $selectDeptErr = $selectSchoolErr = "";
      // define the regular expression for password
      $passwordRegex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).{6,}$/";
      // define the valid options for select
      $validFacultyOptions = array("1", "2", "3", "4", "5", "6");
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
          // validate the select value
          if ($_POST["faculty"] == 0) {
            $selectFacultyErr = "Please select an option";
          } 
          else {
            $select = test_input($_POST["faculty"]);
            if (!in_array($select, $validFacultyOptions)) {
              $selectFacultyErr = "Invalid option selected";
            }  
          }
          if($_POST["school"]== 0){
            $selectSchoolErr = "Please select an option";
          }
          if ($_POST["department"] == 0) {
            $selectDeptErr = "Please select an option";
          }
        }
    ?>
