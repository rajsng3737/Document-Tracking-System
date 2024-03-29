<?php
    session_start();
    if($_SESSION == true && isset($_SESSION['loggedin'])){
      if(array_key_exists("employeeid",$_SESSION) && array_key_exists("employeename",$_SESSION))
        header("location:employee_dts/home.php");
      else if(array_key_exists('faculty',$_SESSION) && array_key_exists('school',$_SESSION) && array_key_exists('department',$_SESSION))
        header("location: departmental_dts/home.php");
      else if(array_key_exists('office_id',$_SESSION))
        header("location: office_dts/home.php");
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

    <div class="background"></div>
    <div class="card w-50">
		<div class="card-header">
			<img src="./ico/shuatslogo.png" alt="Shuats Logo">
		</div>
		<div class="card-body">
      <div class = "row">
        <div class = "col-sm">
          <a href="./departmental_dts" class="login-column">
            <div class="login-button">
              <h2>Departmental Login</h2>
            </div>
          </a>
        </div>
      </div>
      <div class ="row">
        <div class = "col-sm-6">
          <a href="./employee_dts" class="login-column">
            <div class="login-button">
              <h2>Employee Login</h2>
            </div>
          </a>
        </div>
        <div class = "col-sm-6">
          <a href="./office_dts" class="login-column">
            <div class="login-button">
              <h2>Offices Login</h2>
            </div>
          </a>
        </div>
		</div>
	</div>
  </div>
  </body>
</html>