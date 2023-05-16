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
        <link href="table.css" rel = "stylesheet">
        <!-- Load jQuery -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <!-- Load Bootstrap JavaScript library -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js"></script>
    </head>
    <body>
        <?php include "header.php";
            include "..\databasec.php";
            ?>
        <div style = "display: flex; justify-content: center; align-items: center;">
             <h2><?php echo $_SESSION['track_results']['DocumentName']; ?><h2>
        </div>
        <br>
        <div style = "display: flex; justify-content: center; align-items: center;">
            <div class = "card" style = "width:95%">
                <div class = "card-header bg-dark" style="color:white;">
                    <u><h3>Overview</h3></u>
                </div>
                <div class="card-body">
                    <table class="table table-striped table-hover">
                        <tbody>
                            <tr>
                                <td class = "col-3"><h4>Tracking Number</h4></td>
                                <td class = "data"><?php echo $_SESSION['track_results']['DocumentID'] ?></td>
                            </tr>
                            <tr>
                                <td class = "col-3"><h4>Document Name/Title</h4></td>
                                <td class = "data"><?php echo $_SESSION['track_results']['DocumentName'] ?></td>
                            </tr>
                            <tr>
                                <td class = "col-3"><h4>Document Type</h4></td>
                                <td class = "data"><?php $type_query = mysqli_query($conn,"Select type_name from document_type where type_id = '".$_SESSION['track_results']['DocumentType']."';");
                                                         if(mysqli_num_rows($type_query) == 1){
                                                            echo mysqli_fetch_array($type_query)['type_name'];
                                                         }
                                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td class = "col-3"><h4>Originating Office</h4></td>
                                <td class = "data"><?php echo $_SESSION['track_results']['DocumentID'] ?></td>
                            </tr>
                            <tr>
                                <td class = "col-3"><h4>Current Office</h4></td>
                                <td class = "data"><?php $query = mysqli_query($conn,"SELECT office_name from offices where office_id = '".$_SESSION['track_results']['FileLocation']."'");
                                                        if(mysqli_num_rows($query) == 1){
                                                            echo mysqli_fetch_array($query)['office_name'];
                                                        } 
                                                    ?> <button> View Trail</button></td>
                            </tr>
                            <tr>
                                <td class = "col-3"><h4>Received in Current Office</h4></td>
                                <td class = "data"><?php echo $_SESSION['track_results']['LastModifiedDate'] ?></td>
                            </tr>
                            <tr>
                                <td class = "col-3"><h4>Status</h4></td>
                                <td class = "data"><?php echo $_SESSION['track_results']['status'] ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </body>
</html>
