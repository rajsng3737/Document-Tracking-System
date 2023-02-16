<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
    <title>Login to SHUATS DTS</title>
  </head>
  <body>
    <?php
      $email = $password = $select = "";
      $emailErr = $passwordErr = $selectErr = $selectDeptErr = "";
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
          // validate the password length and characters
          if (empty($_POST["password"])) {
            $passwordErr = "Password is required";
          } 
          /*else {
            $password = test_input($_POST["password"]);
            if (strlen($password) < 6 || !preg_match($passwordRegex, $password)) {
              $passwordErr = "Password must have at least 6 characters, one uppercase letter, one lowercase letter and one digit";
              return false;
            }
          }*/

          // validate the select value
          if ($_POST["faculty"] == 0) {
            $selectErr = "Please select an option";
          } 
          else {
            $select = test_input($_POST["faculty"]);
            if (!in_array($select, $validFacultyOptions)) {
              $selectErr = "Invalid option selected";
            }
          }
          if ($_POST["departments"] == 0) {
            $selectDeptErr = "Please select an option";
          } 
          // if there are no errors, proceed with the form data
          if ($emailErr == "" && $passwordErr == "" && $selectErr == "" && $selectDeptErr == "") {
            include ('databasec.php');
            $result = mysqli_query($conn,"SELECT email,faculty,department from credentials where email = '".test_input($_POST["email"])."' and password = '".test_input($_POST["password"])."' 
                                  and faculty = ".test_input($_POST["faculty"])." and department = ".test_input($_POST["departments"]).";");
            if(mysqli_num_rows($result) > 0)
              echo "<script>window.location.href='home.php'</script>";
            else
              echo "Username Not Found";
            
          }
        }
        // function to sanitize the form data
        function test_input($data) {
          $data = trim($data);
          $data = stripslashes($data);
          $data = htmlspecialchars($data);
          return $data;
        }
    ?>
    <div class="container my-5 w-25">
      <div class ="col-4 mx-auto mb-4 w-100">
      <img src="shuatslogo.png" class="img-fluid" alt="Shuats Logo">
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
        <?php showError($selectErr)?>
        <select id = "departments" name="departments" class="form-select form-select-lg mb-3" aria-label=".form-select-lg example">
        <option selected value="0">Choose Department</option>
        </select>
        <?php showError($selectDeptErr)?>
         <script>
          let deptArr = {
            1:["Naini Agricultural Institute (NAI)","Ethelind College of Home Science (ECHS)","Makino School of Continuing And Non-Formal Education (MSCNE)","College of Forestry (CF)"],
            2:["Jacob Institute of Biotechnology And Bio-Engineering (JIBB) ","Vaugh Institute of Agricultural Engineering And Technology (VIAET)","Warner College of  Dairy Technology (WCDT)"],
            3:["Gospel and Plough Institute of Theology","Department of Advanced Theological Studies","Yeshu Darbar Bible School"],
            4:["Joseph School of Business Studies and Commerce","Chitamber School of Humanities and Social Sciences ","Allahabad School of Education","School of Film and Mass Communication"],
            5:["Shalom Institute of Health and Allied Sciences (SIHAS)"],
            6:["Faculty of Science"]
          };
          function addOptions(x){
            i = 1;
            deptArr[x].forEach(element => {
                  dept.add(new Option(element,i));
                  i++;
                });
          };
          let facaulty = document.querySelector("#faculty");
          let dept = document.querySelector("#departments");
          faculty.addEventListener("change",()=>{
            dept.options.length = 1;
            switch(faculty.value){
              case '1':
                addOptions(1);
                break;
              case '2':
                addOptions(2);
                break;
              case '3':
                addOptions(3);
                break;
              case '4':
                addOptions(4);
                break;
              case '5':
                addOptions(5);
                break;
              case '6':
                addOptions(6);
                break;
            }
          });
        </script>
        <?php
        function showError($value){
          if(!empty($value)){
            echo "<span hidden class='alert alert-danger d-flex align-items-center ' role='alert'>
            $value </span>";
          }
        }
        ?>
        <div class="mb-3 row">
          <label for="email" class="col-sm-2 col-form-label">Email</label>
          <div class="col-sm-10">
            <input type="text" name = "email" class="form-control" id="email" value="email@example.com">
            <?php showError($emailErr)?>
          </div>
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
    </div>
    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
  </body>
</html>