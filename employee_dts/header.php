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
        <h1 style = "color:saddlebrown;">
        <?php
            echo "Hello, ".$_SESSION['employeename']."!";
        ?>
        </h1>
    </div>