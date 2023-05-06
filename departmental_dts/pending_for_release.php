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
                                $res = mysqli_query($conn,"select * from documents where DocumentID IN (select document_id from doc_dept_relationship where dept_id = ".$_SESSION['department'].") and status = 'Pending' and FileLocation ='1';" );
                                $tmp = 1;
                                while($res_array = mysqli_fetch_assoc($res)){
                                    echo "<tr>
                                    <th scope='row'>".$tmp."</th>
                                    <td>".$res_array['DocumentID']."</td>
                                    <td>".$res_array['DocumentName']."</td>
                                    <td>".$res_array['remark']."</td>
                                    <td>".$res_array['LastModifiedDate']."</td>
                                    <td>".$res_array['FileLocation']."</td>
                                    <td>".$res_array['status']."</td>
                                    </tr>";
                                    $tmp++;
                                }
                                $res = mysqli_query($conn,"select * from documents where DocumentID IN (select document_id from doc_emp_relationship where employee_id IN (select employee_id from emp_dept_relationship where dept_id = ".$_SESSION['department'].")
                                                                                                         and document_id IN (select documentID from documents where FileLocation = 1 and status = 'pending'));");
                                while($res_array = mysqli_fetch_assoc($res)){
                                    echo "<tr>
                                    <th scope='row'>".$tmp."</th>
                                    <td>".$res_array['DocumentID']."</td>
                                    <td>".$res_array['DocumentName']."</td>
                                    <td>".$res_array['remark']."</td>
                                    <td>".$res_array['LastModifiedDate']."</td>
                                    <td>".$res_array['FileLocation']."</td>
                                    <td>".$res_array['status']."</td>
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