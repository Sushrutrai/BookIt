<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/admin_form_style.css">
    <title>Add Event</title>
</head>

<body>
    <?php
include'partial/adminheader.php';?>
    <div class="head ">
        <a href="adminPanel.php">
        <p>Back to Dashboard</p>
    </a>
    <h1>Add your Event</h1>
    <p>Fill the fields to create event</p>
    </div>
    <form method="post" action="../../app/actions/controller.php" enctype="multipart/form-data">
        <label for="event-name">Event Name<input type="text" id="event-name" name="event_name" placeholder="Enter name of the event"></label>
        <label for="event-date" >Date<input type="date" id="event-date" name="event_date"></label>
        <label for="event-date">Category
            <select name="event_category" id="event_category">
        <?php 
            require __DIR__.'/../app/config/config.php';

            $statement=$connection->prepare('select *from event_categories order by category_name asc');
            $statement->execute();

            $result=$statement->get_result();
            while($row=$result->fetch_assoc()){
                echo"<option value='".$row['category_id']."'>".$row['category_name']."</option>";
            }
        ?>
            </select>
        </label>
        <label for="event-description">Event Description
            <input id="event-description" name="event_description" placeholder="Describe the event in detail">
        </label>
        <label for="event-location">Event Location<input type="text" id="event-location" name="event_location" placeholder="Enter event location"></label>
        <label for="maps-link">Maps Link<input type="text" id="maps-link" name="maps_link" placeholder="https://maps.google.com/"></label>
        <label for="event_image">Event Image<input type="file" id="event-image" name="event_image"></label>

        <div class="buttons">
            <button type="submit" class="btn btn-primary" name="add" value="add">Submit</button>
            <button type="reset" class="btn btn-secondary">Reset</button>
        </div>
    </form>
    <?php include 'partial/adminFooter.php';?>
</body>

</html>