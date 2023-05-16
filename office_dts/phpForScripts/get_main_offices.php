<?php
    $conn = mysqli_connect("localhost","root","","document tracking system");
    if($conn == false)
        echo "connection gone";
    else{
        $result = mysqli_query($conn,"select office_id,office_name from offices where office_id > 3;");
        if(mysqli_num_rows($result)!=0){
            $main_offices = array();
            while($row = mysqli_fetch_row($result))
                $main_offices[] = $row;
            echo json_encode($main_offices);
        }
        mysqli_close($conn);
    }
?>