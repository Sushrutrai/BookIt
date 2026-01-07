<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../assets/css/event_page_style.css">
    <?php
            require __DIR__.'/../app/bootstrap.php';
            if(isset($_GET['eid'])){
                $eid=$_GET['eid'];
                $query=$connection->prepare('select * from event_details where eid=?');
                $query->bind_param('i',$eid);
                $query->execute();
                $result=$query->get_result();
                $row=$result->fetch_assoc();
                }
    echo"
        <style>
            .cover{
                background-image:url('../uploads/".$row["event_image_path"]."');
                background-size: cover;
                background-position: center;
                width   : 100%;

            }
        </style>
    ";
    ?>
</head>
<body>
    <?php include 'header.php';?>
    <main class=" "><div class="cover overlae"></div>
       <div class="flex_content centered">
         <div class="event_left">
          
                <!-- // echo"
                //     <img class='img_    banner'src='../uploads/".$row["event_image_path"]."' alt=''>

                // "; -->
            
               
                 <img src='../uploads/<?php echo $row['event_image_path'];?>' alt=''>
                
        </div>
        <div class="event_right">
            <h3><?php echo $row['event_name'];?></h3>
            <p><?php echo $row['event_description'];?></p>
        </div>
       </div>

    </main>
    <?php include 'footer.php';?>
</body>

</html>