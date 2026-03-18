<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookmarked Events | BookIt</title>
    <link rel="stylesheet" href="../assets/css/style.css">
     <link rel="stylesheet" href="../assets/css/grids.css">
</head>
<body>
   <main>
     <?php include "header.php"; ?>
    <?php
    require __DIR__.'/../app/bootstrap.php';
    if(!isset($_SESSION['id'])){
        echo "<div class='alert alert-warning' style='text-align:center; margin:2em auto; max-width:400px; padding:1em; background:#fff3cd; color:#856404; border:1px solid #ffeeba; border-radius:5px;'>Login to view bookmarks</div>";
    }
    else{
    ?>
     <section class="centered main_content">
            <div class="grid">
    <?php
    
        $query=$connection->prepare("select * from event_details e join bookmarks b on e.eid=b.eid join users u on b.id=u.id where b.id=?;");
        $query->bind_param('i',$_SESSION['id']);
        $query->execute();
        $result=$query->get_result();
        while($row=$result->fetch_assoc()){
                echo "<article class='grid-item aspect' >
                    <img class='banner_img' src='../uploads/".$row["event_image_path"]."' alt='".htmlspecialchars($row["event_name"])."'>
                    <h3 onclick=\"location.href='event_card.php?eid=".$row['eid']."'\">".htmlspecialchars($row['event_name'])."</h3>             
                    
            <ul>
                <li><img class='icon' src='../assets/icons/calender.svg' > ".date('d M ,Y', strtotime($row['event_date'])) ."</li>
                <li><img class='icon' src='../assets/icons/time.svg' >".htmlspecialchars($row["event_location"])."</li>
            </ul>
            <button type='button' onclick=\"return alert('ticket purchased')\">Buy Ticket</button>

        </article>";
        }
         echo "</div>
        </section>"; 
    }
    ?>
      
   </main>
    <?php include "footer.php"; ?>
</body>
</html>