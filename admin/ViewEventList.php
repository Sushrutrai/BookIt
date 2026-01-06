<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/table.css">
    <title>View Event List</title>
</head>

<body>
    <?php
    require __DIR__.'/../app/config/config.php';
    include'partial/adminheader.php';?>
    <div class="head ">
        <a href="adminPanel.php">
            <p>Back to Dashboard</p>
        </a>
        <h1>View event list</h1>
    </div>
    <div class="table_wrapper centered">
    <table class="table ">
        <thead>
            <tr>
                <th>Id</th>
                <th>Card Image</th>
                <th>Name</th>
                <th>Category</th>
                <th>Description</th>
                <th>Date</th>
                <th>Location </th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            

            $statement=$connection->prepare('Select *from event_details e inner join event_categories c on e.category_id=c.category_id;');
            $statement->execute();

            $result=$statement->get_result();
            while($row=$result->fetch_assoc()){
                echo"<tr>
                        <td>".htmlspecialchars($row['eid'])."</td>
                        <td><img src='../../uploads/".$row['event_image_path']."'></td>
                        <td>".htmlspecialchars($row['event_name'])."</td>
                        <td>".htmlspecialchars($row['category_name'])."</td>
                        <td>".htmlspecialchars($row['event_description'])."</td>
                        <td>".htmlspecialchars($row['event_date'])."</td>
                        <td>".htmlspecialchars($row['event_location'])."</td>           
                        <td>".htmlspecialchars($row['event_status'])."</td>
                        <td>
                        <a href=''>
                            <svg width='24' height='24' viewBox='0 0 24 24' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                <path d='M7 17L10.9211 16.9866L19.4793 8.454C19.8152 8.11592 20 7.66693 20 7.18932C20 6.71171 19.8152 6.26272 19.4793 5.92464L18.0701 4.50612C17.3984 3.82995 16.2264 3.83353 15.56 4.50344L7 13.0378V17ZM16.8138 5.7708L18.2256 7.18664L16.8066 8.60158L15.3974 7.18395L16.8138 5.7708ZM8.77705 13.7837L14.1349 8.44148L15.544 9.86L10.1871 15.2005L8.77705 15.2049V13.7837Z' fill='black'/>
                                <path d='M5.66667 20H17.3333C18.2525 20 19 19.2525 19 18.3333V11.11L17.3333 12.7767V18.3333H8.29833C8.27667 18.3333 8.25417 18.3417 8.2325 18.3417C8.205 18.3417 8.1775 18.3342 8.14917 18.3333H5.66667V6.66667H11.3725L13.0392 5H5.66667C4.7475 5 4 5.7475 4 6.66667V18.3333C4 19.2525 4.7475 20 5.66667 20Z' fill='black'/>
                            </svg></a>
                        <a href='../app/actions/controller.php?eid={$row['eid']}' onclick=\"return confirm('Are you sure you want to delete this event? This action cannot be undone.')\">
                            <svg width='24' height='24' viewBox='0 0 24 24' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                <path d='M6.8125 21C6.29687 21 5.85562 20.8152 5.48875 20.4456C5.12187 20.076 4.93812 19.6312 4.9375 19.1111V6.83333H4V4.94444H8.6875V4H14.3125V4.94444H19V6.83333H18.0625V19.1111C18.0625 19.6306 17.8791 20.0754 17.5122 20.4456C17.1453 20.8158 16.7038 21.0006 16.1875 21H6.8125ZM16.1875 6.83333H6.8125V19.1111H16.1875V6.83333ZM8.6875 17.2222H10.5625V8.72222H8.6875V17.2222ZM12.4375 17.2222H14.3125V8.72222H12.4375V17.2222Z' fill='black'/>
                            </svg></a>
                        </td>
                    </tr>";
            }
        ?>
        </tbody>
    </table>
    </div>
    <?php include 'partial/adminFooter.php';?>
</body>
</html>