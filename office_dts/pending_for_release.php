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
        <title>Shuats DTS Home</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
        <link href="home_style.css" rel="stylesheet">
        <!-- Load jQuery -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <!-- Load Bootstrap JavaScript library -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js"></script>
    </head>
    <body>
        <?php include "header.php";?>
        <div style = "display: flex; justify-content: center; align-items: center;">
            <div class = "card" style = "width:95%">
                <div class = "card-header">
                    <u><h5>Pending Documents:</h5></u>
                </div>
                <div class="card-body">
                    <table class="table table-striped table-hover">
                        <thead >
                            <tr class ="bg-dark" style="color:white;">
                            <th scope="col">S. No</th>
                            <th scope="col">Document ID</th>
                            <th scope="col">Title</th>
                            <th scope="col">Remark</th>
                            <th scope="col">Last Modified</th>
                            <th scope="col">File Location</th>
                            <th scope="col">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                include "../databasec.php";
                                if($_SESSION['office_id'] == 2){
                                    $pending_doc_emp = mysqli_query($conn,"SELECT d.*
                                    FROM documents d
                                    INNER JOIN doc_emp_relationship der ON d.documentID = der.document_id
                                    INNER JOIN emp_dept_relationship edr ON der.employee_id = edr.employee_id
                                    INNER JOIN departments dpt ON edr.dept_id = dpt.dept_id
                                    INNER JOIN school_dean sd ON dpt.school_id = sd.school_id
                                    WHERE d.FileLocation = '2'
                                    AND d.status = 'Pending'
                                    AND sd.sdean_id = '".$_SESSION['sdean_id']."';");

                                    $pending_doc_dept = mysqli_query($conn,"SELECT d.*
                                    FROM documents d
                                    INNER JOIN doc_dept_relationship ddr on d.documentID = ddr.document_id
                                    INNER JOIN departments dp on ddr.dept_id = dp.dept_id
                                    INNER JOIN school_dean sd on dp.school_id = sd.school_id
                                    WHERE d.FileLocation = '2'
                                    AND d.status = 'Pending'
                                    AND sd.sdean_id = '".$_SESSION['sdean_id']."';");

                                    $pending_doc_sdean = mysqli_query($conn,"SELECT * FROM documents WHERE documentID LIKE '".$_SESSION['sdean_id']."_%' AND status ='Pending';");

                                    $pending_doc = array_merge(array_merge(mysqli_fetch_all($pending_doc_emp),mysqli_fetch_all($pending_doc_dept)),mysqli_fetch_all($pending_doc_sdean));
                                }
                                if($_SESSION['office_id'] == 3){
                                    $pending_doc_emp = mysqli_query($conn,"SELECT d.*
                                    FROM documents d
                                    INNER JOIN doc_emp_relationship der ON d.documentID = der.document_id
                                    INNER JOIN emp_dept_relationship edr ON der.employee_id = edr.employee_id
                                    INNER JOIN departments dpt ON edr.dept_id = dpt.dept_id
                                    INNER JOIN schools s ON dpt.school_id = s.school_id
                                    INNER JOIN faculty f ON s.faculty_id = f.faculty_id
                                    INNER JOIN faculty_dean fd ON f.faculty_id = fd.faculty_id 
                                    WHERE d.FileLocation = '3'
                                    AND d.status = 'Pending'
                                    AND fd.fdean_id = '".$_SESSION['fdean_id']."';");

                                    $pending_doc_dept = mysqli_query($conn,"SELECT d.*
                                    FROM documents d
                                    INNER JOIN doc_dept_relationship ddr ON d.documentID = ddr.document_id
                                    INNER JOIN departments dpt ON ddr.dept_id = dpt.dept_id
                                    INNER JOIN schools s ON dpt.school_id = s.school_id
                                    INNER JOIN faculty f ON s.faculty_id = f.faculty_id
                                    INNER JOIN faculty_dean fd ON f.faculty_id = fd.faculty_id 
                                    WHERE d.FileLocation = '3'
                                    AND d.status = 'Pending'
                                    AND fd.fdean_id = '".$_SESSION['fdean_id']."';");

                                    $pending_doc_fdean = mysqli_query($conn,"SELECT * FROM documents WHERE documentID LIKE '".$_SESSION['fdean_id']."_%' AND status ='Pending';");

                                    $pending_doc = array_merge(mysqli_fetch_all($pending_doc_emp),mysqli_fetch_all($pending_doc_dept));
                                    
                                }
                                else if($_SESSION['office_id'] > 3)
                                    $pending_doc = mysqli_query($conn,"select * from documents where FileLocation = '".$_SESSION['office_id']."' and status = 'Pending';");
                                $tmp = 1;
                                foreach ($pending_doc as $res_row) {
                                    echo "<tr>
                                    <th scope='row'>".$tmp."</th>
                                    <td>".$res_row[0]."</td>
                                    <td>".$res_row[1]."</td>
                                    <td>".$res_row[3]."</td>
                                    <td>".$res_row[5]."</td>
                                    <td>".$res_row[7]."</td>
                                    <td>".$res_row[8]."</td>
                                    </tr>";
                                    $tmp++;
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </body>
</html>