
    <nav class="navbar bg-body-tertiary" data-bs-theme="dark">
    <div class="container-fluid ">
        <a class="navbar-brand" href="#">
            <div class="container text-center">
                <div class ="row">
                    <div class = "col">
                        <img src="ico/shuatslogo.png" alt="Logo" width="50" height="50" class="d-inline-block align-text-top">
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
            echo '<script>
            let deptArr = {
                1:["Naini Agricultural Institute (NAI)","Ethelind College of Home Science (ECHS)","Makino School of Continuing And Non-Formal Education (MSCNE)","College of Forestry (CF)"],
                2:["Jacob Institute of Biotechnology And Bio-Engineering (JIBB) ","Vaugh Institute of Agricultural Engineering And Technology (VIAET)","Warner College of  Dairy Technology (WCDT)"],
                3:["Gospel and Plough Institute of Theology","Department of Advanced Theological Studies","Yeshu Darbar Bible School"],
                4:["Joseph School of Business Studies and Commerce","Chitamber School of Humanities and Social Sciences ","Allahabad School of Education","School of Film and Mass Communication"],
                5:["Shalom Institute of Health and Allied Sciences (SIHAS)"],
                6:["Faculty of Science"]
            }; 
                document.write(deptArr['.$_SESSION['faculty'].']'.'['.($_SESSION['departments']-1).']);
        </script>'
        ?>
        </h1>
    </div>