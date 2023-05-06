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
        <title>Track Document</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
        <link href="home_style.css" rel="stylesheet">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> 
    </head>
    <body>
        <?php
            include("header.php");
        ?>
        <div class = "table-responsive">
            <table id = "table_doc" class = "table table-bordered dashb head tablLeft dataTable no-footer">
                <thead>
                    <tr>
                        <th>Document No.</th>
                        <th>Document Name</th>
                        <th>Document Type</th>
                        <th>Remarks</th>
                        <th>Author</th>
                        <th>File Location</th>
                        <th>status</th>
                    </tr>
                </thead>
            </table>
        </div>
</body>
</html>