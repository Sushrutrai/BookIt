<?php

require '../config.php';

if(isset($_POST['add'])){
    $event_name=$_POST['event_name'];
    $event_date=$_POST['event_date'];
    $event_description=$_POST['event_description'];
    $event_location=$_POST['event_location'];
    $maps_link=$_POST['maps_link'];

    $image_name=$_FILES['event_image']['name'];
    $temp=explode('.',$image_name);
    $newfilename=round(microtime(true)).'.'.end($temp);
    $target_dir="C:\GitHub-C\BookIt\uploads\\".$newfilename;
    move_uploaded_file($_FILES['event_image']['tmp_name'],$target_dir);

    $statement=$connection->prepare("INSERT INTO event_details(event_name,event_date,event_location,event_maps_link,event_image_path,event_description) VALUES(?,?,?,?,?,?)");
    $statement->bind_param("ssssss",$event_name,$event_date,$event_location,$maps_link,$newfilename,$event_description);
    $statement->execute();
    if($statement->error){
        die("Error inserting event: {$statement->error}");
    }else{
        echo "<script>
            if(confirm('Event added successfully! Click OK to continue.')) {
                window.location.href='../src/addEventForm.php';
            }
        </script>";
        exit();
    }
}
?>