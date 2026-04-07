<?php

require_once __DIR__ . '/../bootstrap.php';


//  ADD ACTION


if(isset($_POST['add'])){
    $event_name=$_POST['event_name'];
    $event_date=$_POST['event_date'];
    $event_description=$_POST['event_description'];
    $event_location=$_POST['event_location'];
    $maps_link=$_POST['maps_link'];
    $category=$_POST['event_category'];

    $image_name=$_FILES['event_image']['name'];
    $temp=explode('.',$image_name);
    $newfilename=round(microtime(true)).'.'.end($temp);
    $target_dir="C:\GitHub-C\BookIt\uploads\\".$newfilename;
    move_uploaded_file($_FILES['event_image']['tmp_name'],$target_dir);

    $statement=$connection->prepare("INSERT INTO event_details(event_name,event_date,event_location,event_maps_link,event_image_path,event_description,category_id) VALUES(?,?,?,?,?,?,?)");
    $statement->bind_param("ssssssi",$event_name,$event_date,$event_location,$maps_link,$newfilename,$event_description,$category);
    $statement->execute();
    if($statement->error){
        die("Error inserting event: {$statement->error}");
    }else{
        echo "<script>
            if(confirm('Event added successfully! Click OK to continue.')) {
                window.location.href='../../admin/addEventForm.php';
            }
        </script>";
        exit();
    }
}



//  DELETE ACTION


else if(isset($_GET['eid'])){
    $eid=$_GET['eid'];
    $query=$connection->prepare('select event_image_path from event_details where eid=?');
    $query->bind_param('i',$eid);
    $query->execute();
    $result=$query->get_result();
    $row=$result->fetch_assoc();
    $image_path=$row['event_image_path'];
    $full_path="C:\GitHub-C\BookIt\uploads\\".$image_path;
        if(file_exists($full_path)){    //check if the file exists
            unlink($full_path);         //delete the image file from the server 
        }

    $statement=$connection->prepare('delete from event_details where eid=?');
    $statement->bind_param('i',$eid);
    $statement->execute();
        if($statement->error){
        die ("Error deleting event: {$statement->error}");
        }
        else{    
            echo"
                <script>
                    if(confirm('Event successfully deleted! Click OK to continue.')){
                        window.location.href='../../admin/ViewEventList.php'
                    }
                </script>
            ";
            exit();
        }
    }
?>