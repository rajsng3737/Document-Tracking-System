<?php
    $conn = mysqli_connect("localhost","root","","document tracking system");
    if($conn == false)
        echo "connection gone";
    else{
        $faculty = $_POST['faculty'];
        $result = mysqli_query($conn,"select school_id,school_name from schools where faculty_id = ".$faculty);
        if(mysqli_num_rows($result)!=0){
            $schools = array();
            while($row = mysqli_fetch_row($result))
                $schools[] = $row;
            echo json_encode($schools);
        }
        mysqli_close($conn);
    }
?>