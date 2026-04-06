<?php

require_once __DIR__ . '/../bootstrap.php';


//  ADD ACTION WITH TICKET TIERS SUPPORT


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

    // Insert event
    $statement=$connection->prepare("INSERT INTO event_details(event_name,event_date,event_location,event_maps_link,event_image_path,event_description,category_id) VALUES(?,?,?,?,?,?,?)");
    $statement->bind_param("ssssssi",$event_name,$event_date,$event_location,$maps_link,$newfilename,$event_description,$category);
    $statement->execute();
    
    if($statement->error){
        die("Error inserting event: {$statement->error}");
    }
    
    // Get the inserted event ID
    $event_id = $connection->insert_id;
    
    // Process ticket tiers if provided
    if(isset($_POST['ticket_name']) && is_array($_POST['ticket_name'])) {
        $ticket_names = $_POST['ticket_name'];
        $ticket_prices = $_POST['ticket_price'] ?? [];
        $ticket_capacities = $_POST['ticket_capacity'] ?? [];
        $ticket_sale_starts = $_POST['ticket_sale_start'] ?? [];
        $ticket_sale_ends = $_POST['ticket_sale_end'] ?? [];
        
        // Prepare ticket insertion statement
        $ticket_stmt = $connection->prepare(
            "INSERT INTO ticket_type (eid, ticket_name, price, capacity, sold_cout, sale_start, sale_end, status) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        
        if(!$ticket_stmt) {
            die("Error preparing ticket statement: {$connection->error}");
        }
        
        $sold_count = 0;
        $status = 'active';
        
        // Insert each ticket tier
        for($i = 0; $i < count($ticket_names); $i++) {
            $name = $ticket_names[$i];
            $price = isset($ticket_prices[$i]) ? floatval($ticket_prices[$i]) : 0;
            $capacity = isset($ticket_capacities[$i]) ? intval($ticket_capacities[$i]) : 0;
            $sale_start = isset($ticket_sale_starts[$i]) && !empty($ticket_sale_starts[$i]) ? $ticket_sale_starts[$i] : NULL;
            $sale_end = isset($ticket_sale_ends[$i]) && !empty($ticket_sale_ends[$i]) ? $ticket_sale_ends[$i] : NULL;
            
            // Convert HTML5 datetime-local format to MySQL datetime if provided
            if($sale_start) {
                $sale_start = str_replace('T', ' ', $sale_start);
            }
            if($sale_end) {
                $sale_end = str_replace('T', ' ', $sale_end);
            }
            
            $ticket_stmt->bind_param(
                "isdiddssi",
                $event_id,
                $name,
                $price,
                $capacity,
                $sold_count,
                $sale_start,
                $sale_end,
                $status
            );
            
            if(!$ticket_stmt->execute()) {
                die("Error inserting ticket tier: {$ticket_stmt->error}");
            }
        }
        
        $ticket_stmt->close();
    }
    
    echo "<script>
        if(confirm('Event added successfully with " . count($ticket_names) . " ticket tier(s)! Click OK to continue.')) {
            window.location.href='../../admin/addEventForm_new.php';
        }
    </script>";
    exit();
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
