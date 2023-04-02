<?php
    $conn = mysqli_connect("localhost","root","","document tracking system");
    if($conn == false)
        echo "connection gone";
    else{
        $school = $_POST['school'];
        $result = mysqli_query($conn,"select dept_id,dept_name from departments where school_id =".$school);
        if(mysqli_num_rows($result)!=0){
            $schools = array();
            while($row = mysqli_fetch_row($result))
                $schools[] = $row;
            echo json_encode($schools);
        }
        mysqli_close($conn);
    }
?>