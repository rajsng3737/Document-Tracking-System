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
        <title>Show Report</title>
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
            include "../databasec.php";
            if($_SESSION['office_val'] == 1){
                $doc_report_by_emp = mysqli_query($conn,"SELECT dr.documentID, dr.received_date, dr.released_date from document_report dr
                INNER JOIN routesteps rs ON rs.step_number = dr.which_step
                INNER JOIN documents d ON d.DocumentID =  dr.DocumentId
                INNER JOIN doc_emp_relationship der ON der.document_id = d.DocumentID 
                INNER JOIN emp_dept_relationship edr ON edr.employee_id = der.employee_id
                INNER JOIN departments dp ON dp.dept_id = edr.dept_id
                INNER JOIN schools s ON s.school_id = dp.school_id
                INNER JOIN faculty f ON s.faculty_id = f.faculty_id
                AND f.faculty_id = '".$_SESSION['select_faculty']."' 
                AND rs.office_id = '3';");
                $doc_report_by_dept = mysqli_query($conn,"SELECT dr.documentID, dr.received_date, dr.released_date from document_report dr
                INNER JOIN routesteps rs ON rs.step_number = dr.which_step
                INNER JOIN documents d ON d.DocumentID =  dr.DocumentId
                INNER JOIN doc_dept_relationship ddr ON ddr.document_id = d.DocumentID
                INNER JOIN departments dp ON dp.dept_id = ddr.dept_id
                INNER JOIN schools s ON s.school_id = dp.school_id
                INNER JOIN faculty f ON s.faculty_id = f.faculty_id
                AND f.faculty_id = '".$_SESSION['select_faculty']."' 
                AND rs.office_id = '3';");
                $doc_report_by_sdean = mysqli_query($conn,"SELECT dr.documentID, dr.received_date, dr.released_date from document_report dr
                INNER JOIN routesteps rs ON rs.step_number = dr.which_step
                INNER JOIN documents d ON d.DocumentID =  dr.DocumentId
                INNER JOIN doc_sdean_relationship dsr ON dsr.document_id = d.DocumentID
                INNER JOIN school_dean sd ON sd.sdean_id = dsr.sdean_id
                INNER JOIN schools s ON s.school_id = sd.school_id
                INNER JOIN faculty f ON s.faculty_id = f.faculty_id
                AND f.faculty_id = '".$_SESSION['select_faculty']."' 
                AND rs.office_id = '3';");
            }
            if($_SESSION['office_val'] == 2){
                $doc_report_by_emp = mysqli_query($conn, "SELECT dr.documentID, dr.received_date, dr.released_date FROM document_report dr
                INNER JOIN routesteps rs ON rs.step_number = dr.which_step
                INNER JOIN documents d ON d.DocumentID = dr.documentID
                INNER JOIN doc_emp_relationship der ON der.document_id = d.DocumentID 
                INNER JOIN emp_dept_relationship edr ON edr.employee_id = der.employee_id
                INNER JOIN departments dp ON dp.dept_id = edr.dept_id
                INNER JOIN schools s ON s.school_id = dp.school_id
                AND s.school_id = '".$_SESSION['select_school']."'
                AND rs.office_id = '2';");
                $doc_report_by_dept = mysqli_query($conn,"SELECT dr.documentID, dr.received_date, dr.released_date from document_report dr
                INNER JOIN routesteps rs ON rs.step_number = dr.which_step
                INNER JOIN documents d ON d.DocumentID =  dr.DocumentId
                INNER JOIN doc_dept_relationship ddr ON ddr.document_id = d.DocumentID
                INNER JOIN departments dp ON dp.dept_id = ddr.dept_id
                INNER JOIN schools s ON s.school_id = dp.school_id
                AND s.school_id = '".$_SESSION['select_school']."' 
                AND rs.office_id = '2';");
            }
            if($_SESSION['office_val'] == 3){
                $doc_report_by_office = mysqli_query($conn,"SELECT dr.documentID, dr.received_date, dr.released_date from document_report dr
                INNER JOIN routesteps rs ON rs.step_number = dr.which_step and rs.office_id > '3';");
            }
        ?>
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
                            <th scope="col">Document Recieved</th>
                            <th scope="col">Document Released</th>
                            <th scope="col">No of Days Pending at Office</th>
                            </tr>
                        </thead>
                        
                        <tbody>
                            <?php
                            if($_SESSION['office_val'] == 1){
                                $t = 1;
                                foreach($doc_report_by_emp as $row){
                                    if($row['released_date'] == '0000-00-00 00:00:00'){
                                        $received_date = $row['received_date'];
                                        $current_date = new DateTime();
                                        $pending_time = $current_date->diff(new DateTime($received_date));
                                    }
                                    else{
                                        $pending_time = (new DateTime($row['released_date']))->diff(new DateTime($row['received_date']));
                                    }
                                    echo "<tr>
                                        <td>".$t."</td>
                                        <td>".$row['documentID']."</td>
                                        <td>".$row['received_date']."</td>";
                                        if($row['released_date'] == '0000-00-00 00:00:00')
                                            echo "<td>Not Released From This Office.</td>";
                                        else
                                            echo "<td>".$row['released_date']."</td>";
                                        echo "<td>".$pending_time->days." days ".$pending_time->h." hours ".$pending_time->i." min
                                     </tr>";
                                }
                                foreach($doc_report_by_dept as $row){
                                    if($row['released_date'] == '0000-00-00 00:00:00'){
                                        $received_date = $row['received_date'];
                                        $current_date = new DateTime();
                                        $pending_time = $current_date->diff(new DateTime($received_date));
                                    }
                                    else{
                                        $pending_time = (new DateTime($row['released_date']))->diff(new DateTime($row['received_date']));
                                    }
                                    echo "<tr>
                                        <td>".$t."</td>
                                        <td>".$row['documentID']."</td>
                                        <td>".$row['received_date']."</td>";
                                        if($row['released_date'] == '0000-00-00 00:00:00')
                                            echo "<td>Not Released From This Office.</td>";
                                        else
                                            echo "<td>".$row['released_date']."</td>";
                                        echo "<td>".$pending_time->days." days ".$pending_time->h." hours ".$pending_time->i." min
                                     </tr>";
                                }
                                foreach($doc_report_by_sdean as $row){
                                    if($row['released_date'] == '0000-00-00 00:00:00'){
                                        $received_date = $row['received_date'];
                                        $current_date = new DateTime();
                                        $pending_time = $current_date->diff(new DateTime($received_date));
                                    }
                                    else{
                                        $pending_time = (new DateTime($row['released_date']))->diff(new DateTime($row['received_date']));
                                    }
                                    echo "<tr>
                                        <td>".$t."</td>
                                        <td>".$row['documentID']."</td>
                                        <td>".$row['received_date']."</td>";
                                        if($row['released_date'] == '0000-00-00 00:00:00')
                                            echo "<td>Not Released From This Office.</td>";
                                        else
                                            echo "<td>".$row['released_date']."</td>";
                                        echo "<td>".$pending_time->days." days ".$pending_time->h." hours ".$pending_time->i." min
                                     </tr>";
                                }
                            }
                            if($_SESSION['office_val'] == 2){
                                $t = 1;
                                foreach($doc_report_by_emp as $row){
                                    if($row['released_date'] == '0000-00-00 00:00:00'){
                                        $received_date = $row['received_date'];
                                        $current_date = new DateTime();
                                        $pending_time = $current_date->diff(new DateTime($received_date));
                                    }
                                    else{
                                        $pending_time = (new DateTime($row['released_date']))->diff(new DateTime($row['received_date']));
                                    }
                                    echo "<tr>
                                        <td>".$t."</td>
                                        <td>".$row['documentID']."</td>
                                        <td>".$row['received_date']."</td>";
                                        if($row['released_date'] == '0000-00-00 00:00:00')
                                            echo "<td>Not Released From This Office.</td>";
                                        else
                                            echo "<td>".$row['released_date']."</td>";
                                        echo "<td>".$pending_time->days." days ".$pending_time->h." hours ".$pending_time->i." min
                                     </tr>";
                                }
                                foreach($doc_report_by_dept as $row){
                                    if($row['released_date'] == '0000-00-00 00:00:00'){
                                        $received_date = $row['received_date'];
                                        $current_date = new DateTime();
                                        $pending_time = $current_date->diff(new DateTime($received_date));
                                    }
                                    else{
                                        $pending_time = (new DateTime($row['released_date']))->diff(new DateTime($row['received_date']));
                                    }
                                    echo "<tr>
                                        <td>".$t."</td>
                                        <td>".$row['documentID']."</td>
                                        <td>".$row['received_date']."</td>";
                                        if($row['released_date'] == '0000-00-00 00:00:00')
                                            echo "<td>Not Released From This Office.</td>";
                                        else
                                            echo "<td>".$row['released_date']."</td>";
                                        echo "<td>".$pending_time->days." days ".$pending_time->h." hours ".$pending_time->i." min
                                     </tr>";
                                }
                            }
                            if($_SESSION['office_val'] == 3){
                                $t = 1;
                                foreach($doc_report_by_office as $row){
                                    if($row['released_date'] == '0000-00-00 00:00:00'){
                                        $received_date = $row['received_date'];
                                        $current_date = new DateTime();
                                        $pending_time = $current_date->diff(new DateTime($received_date));
                                    }
                                    else{
                                        $pending_time = (new DateTime($row['released_date']))->diff(new DateTime($row['received_date']));
                                    }
                                    echo "<tr>
                                        <td>".$t."</td>
                                        <td>".$row['documentID']."</td>
                                        <td>".$row['received_date']."</td>";
                                        if($row['released_date'] == '0000-00-00 00:00:00')
                                            echo "<td>Not Released From This Office.</td>";
                                        else
                                            echo "<td>".$row['released_date']."</td>";
                                        echo "<td>".$pending_time->days." days ".$pending_time->h." hours ".$pending_time->i." min
                                     </tr>";
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    
    </body>
    </head>
</html>