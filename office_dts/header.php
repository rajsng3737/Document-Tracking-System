
    <nav class="navbar bg-body-tertiary" data-bs-theme="dark">
    <div class="container-fluid ">
        <a class="navbar-brand" href="home.php">
            <div class="container text-center">
                <div class ="row">
                    <div class = "col">
                        <img id = "shuats_logo" src="../ico/shuatslogo.png" alt="Logo" width="50" height="50" class="d-inline-block align-text-top">
                    </div>
                    <div class = "col" style="display: flex; align-items: center;">
                        SHUATS Document Tracking System
                    </div>
                </div>
            </div>
        </a>
    </div>
    </nav>
    <div style= "display: flex; justify-content: center; align-items: center; height: 100px; ">
        <h1 >
        <?php
        include "..\databasec.php";
            if(array_key_exists("fdean_id",$_SESSION)){
                echo "Hello, ".$_SESSION['fdean_name'];
            }
            else if(array_key_exists("sdean_id",$_SESSION)){
                echo "Hello, ".$_SESSION['sdean_name'];
            }
            else if(array_key_exists("office_id",$_SESSION)){
                echo "Office Of ".$_SESSION['office_name'];
            }
        ?>
        </h1>
    </div>