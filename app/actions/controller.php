<?php

require_once __DIR__ . '/../bootstrap.php';

function redirectWithErrors(string $location, array $errors, array $old = []): void {
    $_SESSION['form_errors'] = $errors;
    if (!empty($old)) {
        $_SESSION['old_add_event'] = $old;
    }
    header("Location:{$location}");
    exit();
}

function normalizeDateTimeLocal(?string $value): ?string {
    $value = $value !== null ? trim($value) : '';
    if ($value === '') {
        return null;
    }
    $obj = date_create(str_replace('T', ' ', $value));
    if (!$obj) {
        return null;
    }
    return $obj->format('Y-m-d H:i:s');
}

function validateTicketRows(array $ticketNames, array $prices, array $capacities, array $saleStarts, array $saleEnds, array $statuses): array {
    $errors = [];
    $validated = [];
    $allowedStatuses = ['available', 'hidden', 'sold_out', 'expired'];

    $max = max(count($ticketNames), count($prices), count($capacities), count($saleStarts), count($saleEnds), count($statuses));
    for ($i = 0; $i < $max; $i++) {
        $ticketName = isset($ticketNames[$i]) ? trim((string)$ticketNames[$i]) : '';
        $priceRaw = isset($prices[$i]) ? trim((string)$prices[$i]) : '';
        $capacityRaw = isset($capacities[$i]) ? trim((string)$capacities[$i]) : '';
        $saleStartRaw = isset($saleStarts[$i]) ? (string)$saleStarts[$i] : '';
        $saleEndRaw = isset($saleEnds[$i]) ? (string)$saleEnds[$i] : '';
        $status = isset($statuses[$i]) ? trim((string)$statuses[$i]) : 'available';

        $isEmpty = ($ticketName === '' && $priceRaw === '' && $capacityRaw === '' && trim($saleStartRaw) === '' && trim($saleEndRaw) === '');
        if ($isEmpty) {
            continue;
        }

        if ($ticketName === '') {
            $errors[] = "Ticket type (Tier ".($i + 1).") is required.";
        }
        if ($priceRaw === '' || !is_numeric($priceRaw) || (float)$priceRaw <= 0) {
            $errors[] = "Price for Tier ".($i + 1)." must be a number greater than 0.";
        }
        if ($capacityRaw === '' || !ctype_digit($capacityRaw) || (int)$capacityRaw <= 0) {
            $errors[] = "Capacity for Tier ".($i + 1)." must be an integer greater than 0.";
        }

        $saleStartDb = null;
        $saleEndDb = null;
        if (trim($saleStartRaw) !== '' || trim($saleEndRaw) !== '') {
            $saleStartDb = normalizeDateTimeLocal($saleStartRaw);
            $saleEndDb = normalizeDateTimeLocal($saleEndRaw);
            if ($saleStartDb === null) {
                $errors[] = "Sale start for Tier ".($i + 1)." must be a valid datetime.";
            }
            if ($saleEndDb === null) {
                $errors[] = "Sale end for Tier ".($i + 1)." must be a valid datetime.";
            }
            if ($saleStartDb !== null && $saleEndDb !== null) {
                $startObj = new DateTime($saleStartDb);
                $endObj = new DateTime($saleEndDb);
                if ($startObj > $endObj) {
                    $errors[] = "Sale start must be earlier than or equal to sale end for Tier ".($i + 1).".";
                }
            }
        }

        if (!in_array($status, $allowedStatuses, true)) {
            $status = 'available';
        }

        $validated[] = [
            'row_index' => $i,
            'ticket_name' => $ticketName,
            'price' => is_numeric($priceRaw) ? (float)$priceRaw : null,
            'capacity' => ctype_digit($capacityRaw) ? (int)$capacityRaw : null,
            'sale_start' => $saleStartDb,
            'sale_end' => $saleEndDb,
            'status' => $status
        ];
    }

    return [$errors, $validated];
}

function validateEventFields(array $post): array {
    $errors = [];
    $event_name = isset($post['event_name']) ? trim((string)$post['event_name']) : '';
    $event_date = isset($post['event_date']) ? trim((string)$post['event_date']) : '';
    $event_description = isset($post['event_description']) ? trim((string)$post['event_description']) : '';
    $event_location = isset($post['event_location']) ? trim((string)$post['event_location']) : '';
    $maps_link = isset($post['maps_link']) ? trim((string)$post['maps_link']) : '';
    $category = isset($post['event_category']) ? trim((string)$post['event_category']) : '';

    if ($event_name === '') $errors[] = "Event name is required.";
    if ($event_description === '') $errors[] = "Event description is required.";
    if ($event_location === '') $errors[] = "Event location is required.";
    if ($maps_link === '') $errors[] = "Maps embed link is required.";
    if ($category === '' || !ctype_digit($category)) $errors[] = "Please select a valid category.";

    if ($event_date === '' || strtotime($event_date) === false) {
        $errors[] = "A valid event date is required.";
    } else {
        $today = new DateTime('today');
        $dateObj = new DateTime($event_date);
        if ($dateObj < $today) {
            $errors[] = "Event date must be future-dated (today or later).";
        }
    }

    return [$errors, [
        'event_name' => $event_name,
        'event_date' => $event_date,
        'event_description' => $event_description,
        'event_location' => $event_location,
        'maps_link' => $maps_link,
        'category' => (int)$category
    ]];
}


//  ADD ACTION


if(isset($_POST['add'])){
    [$eventErrors, $eventData] = validateEventFields($_POST);
    $errors = $eventErrors;

    // Validate image upload.
    if (!isset($_FILES['event_image']) || !isset($_FILES['event_image']['tmp_name']) || $_FILES['event_image']['error'] !== UPLOAD_ERR_OK) {
        $errors[] = "Event image upload is required.";
    } else {
        $image_name = $_FILES['event_image']['name'];
        $temp = explode('.', $image_name);
        $ext = strtolower((string)end($temp));
        if ($ext === '') {
            $errors[] = "Event image file extension is invalid.";
        }
    }

    [$ticketErrors, $ticketRows] = validateTicketRows(
        $_POST['ticket_name'] ?? [],
        $_POST['price'] ?? [],
        $_POST['capacity'] ?? [],
        $_POST['sale_start'] ?? [],
        $_POST['sale_end'] ?? [],
        $_POST['status'] ?? []
    );
    $errors = array_merge($errors, $ticketErrors);
    $validatedTickets = [];
    foreach ($ticketRows as $t) {
        if ($t['ticket_name'] !== '' && $t['price'] !== null && $t['capacity'] !== null) {
            $validatedTickets[] = $t;
        }
    }
    if (count($validatedTickets) === 0) $errors[] = "At least one ticket tier is required.";

    if (!empty($errors)) {
        redirectWithErrors("../../admin/addEventForm.php", $errors, $_POST);
    }

    // Move uploaded image after validation passes.
    $image_name = $_FILES['event_image']['name'];
    $temp = explode('.', $image_name);
    $newfilename = round(microtime(true)).'.'.end($temp);
    $target_dir = 'C:\\GitHub-C\\BookIt\\uploads\\'.$newfilename;
    if (!move_uploaded_file($_FILES['event_image']['tmp_name'], $target_dir)) {
        $_SESSION['form_errors'] = ["Failed to upload event image."];
        $_SESSION['old_add_event'] = $_POST;
        header("Location:../../admin/addEventForm.php");
        exit();
    }

    // Transaction: event + ticket tiers.
    $connection->begin_transaction();
    try {
        $event_image_path = $newfilename;

        $statement=$connection->prepare("INSERT INTO event_details(event_name,event_date,event_location,event_maps_link,event_image_path,event_description,category_id) VALUES(?,?,?,?,?,?,?)");
        $bindOk = $statement->bind_param(
            "ssssssi",
            $eventData['event_name'],
            $eventData['event_date'],
            $eventData['event_location'],
            $eventData['maps_link'],
            $event_image_path,
            $eventData['event_description'],
            $eventData['category']
        );
        if ($bindOk === false) {
            throw new Exception("Failed to bind event insert params.");
        }
        $statement->execute();
        if($statement->error){
            throw new Exception("Error inserting event: {$statement->error}");
        }

        $eid = (int)$connection->insert_id;

        // ticket_type.ticket_id is not auto-increment in schema.sql, so we generate it.
        $maxStmt = $connection->query("SELECT IFNULL(MAX(ticket_id),0) AS max_ticket_id FROM ticket_type FOR UPDATE");
        $maxRow = $maxStmt ? $maxStmt->fetch_assoc() : ['max_ticket_id' => 0];
        $nextTicketId = (int)$maxRow['max_ticket_id'] + 1;

        $ticketStmt = $connection->prepare("
            INSERT INTO ticket_type
                (ticket_id, eid, ticket_name, price, capacity, sold_count, sale_start, sale_end, status)
            VALUES
                (?,?,?,?,?,?,?,?,?)
        ");

        foreach ($validatedTickets as $t) {
            $ticket_id = $nextTicketId++;
            $sold_count = 0;
            $sale_start = $t['sale_start']; // null or datetime string
            $sale_end = $t['sale_end'];   // null or datetime string

            $ticketStmt->bind_param(
                "iisdiisss",
                $ticket_id,
                $eid,
                $t['ticket_name'],
                $t['price'],
                $t['capacity'],
                $sold_count,
                $sale_start,
                $sale_end,
                $t['status']
            );

            $ticketStmt->execute();
            if ($ticketStmt->error) {
                throw new Exception("Error inserting ticket tier: {$ticketStmt->error}");
            }
        }

        $connection->commit();
        echo "<script>
                if(confirm('Event added successfully! Click OK to continue.')) {
                    window.location.href='../../admin/ViewEventList.php';
                }
              </script>";
        exit();
    } catch (Exception $e) {
        $connection->rollback();
        $_SESSION['form_errors'] = [$e->getMessage()];
        $_SESSION['old_add_event'] = $_POST;
        header("Location:../../admin/addEventForm.php");
        exit();
    }
}

// UPDATE EVENT ACTION
else if (isset($_POST['update_event'])) {
    $eid = isset($_POST['eid']) ? (int)$_POST['eid'] : 0;
    if ($eid <= 0) {
        redirectWithErrors("../../admin/ViewEventList.php", ["Invalid event ID."]);
    }

    [$eventErrors, $eventData] = validateEventFields($_POST);
    $errors = $eventErrors;

    // Ticket inputs include ticket_id[] for existing rows.
    $ticketIds = $_POST['ticket_id'] ?? [];
    [$ticketErrors, $ticketRows] = validateTicketRows(
        $_POST['ticket_name'] ?? [],
        $_POST['price'] ?? [],
        $_POST['capacity'] ?? [],
        $_POST['sale_start'] ?? [],
        $_POST['sale_end'] ?? [],
        $_POST['status'] ?? []
    );
    $errors = array_merge($errors, $ticketErrors);

    // Build validated ticket rows aligned with submitted indices, include ticket_id.
    $validatedTickets = [];
    foreach ($ticketRows as $t) {
        $idx = $t['row_index'];
        $ticketId = isset($ticketIds[$idx]) ? (int)$ticketIds[$idx] : 0;
        $isExisting = $ticketId > 0;

        // For updates, skip fully empty rows; validateTicketRows already skipped empties.
        if ($t['ticket_name'] === '' || $t['price'] === null || $t['capacity'] === null) {
            continue;
        }

        $validatedTickets[] = $t + ['ticket_id' => $ticketId, 'is_existing' => $isExisting];
    }

    if (count($validatedTickets) === 0) {
        $errors[] = "At least one ticket tier is required.";
    }

    if (!empty($errors)) {
        $_SESSION['form_errors'] = $errors;
        header("Location:../../admin/editEvent.php?eid=".$eid);
        exit();
    }

    // Optional image replace.
    $newImageFilename = null;
    if (isset($_FILES['event_image']) && isset($_FILES['event_image']['tmp_name']) && $_FILES['event_image']['error'] === UPLOAD_ERR_OK) {
        $image_name = $_FILES['event_image']['name'];
        $temp = explode('.', $image_name);
        $newImageFilename = round(microtime(true)).'.'.end($temp);
        $target_dir = 'C:\\GitHub-C\\BookIt\\uploads\\'.$newImageFilename;
        if (!move_uploaded_file($_FILES['event_image']['tmp_name'], $target_dir)) {
            $_SESSION['form_errors'] = ["Failed to upload event image."];
            header("Location:../../admin/editEvent.php?eid=".$eid);
            exit();
        }
    }

    $connection->begin_transaction();
    try {
        if ($newImageFilename !== null) {
            $stmt = $connection->prepare("
                UPDATE event_details
                SET event_name=?, event_date=?, event_location=?, event_maps_link=?, event_image_path=?, event_description=?, category_id=?
                WHERE eid=?
            ");
            $stmt->bind_param(
                "ssssssii",
                $eventData['event_name'],
                $eventData['event_date'],
                $eventData['event_location'],
                $eventData['maps_link'],
                $newImageFilename,
                $eventData['event_description'],
                $eventData['category'],
                $eid
            );
        } else {
            $stmt = $connection->prepare("
                UPDATE event_details
                SET event_name=?, event_date=?, event_location=?, event_maps_link=?, event_description=?, category_id=?
                WHERE eid=?
            ");
            $stmt->bind_param(
                "sssssii",
                $eventData['event_name'],
                $eventData['event_date'],
                $eventData['event_location'],
                $eventData['maps_link'],
                $eventData['event_description'],
                $eventData['category'],
                $eid
            );
        }
        $stmt->execute();
        if ($stmt->error) {
            throw new Exception("Error updating event: {$stmt->error}");
        }

        // Generate ticket IDs for new rows if needed.
        $maxStmt = $connection->query("SELECT IFNULL(MAX(ticket_id),0) AS max_ticket_id FROM ticket_type FOR UPDATE");
        $maxRow = $maxStmt ? $maxStmt->fetch_assoc() : ['max_ticket_id' => 0];
        $nextTicketId = (int)$maxRow['max_ticket_id'] + 1;

        $updateTicket = $connection->prepare("
            UPDATE ticket_type
            SET ticket_name=?, price=?, capacity=?, sale_start=?, sale_end=?, status=?
            WHERE ticket_id=? AND eid=?
        ");
        $insertTicket = $connection->prepare("
            INSERT INTO ticket_type (ticket_id, eid, ticket_name, price, capacity, sold_count, sale_start, sale_end, status)
            VALUES (?,?,?,?,?,?,?,?,?)
        ");

        foreach ($validatedTickets as $t) {
            $sale_start = $t['sale_start'];
            $sale_end = $t['sale_end'];

            if (!empty($t['is_existing'])) {
                $ticket_id = (int)$t['ticket_id'];
                $updateTicket->bind_param(
                    "sdisssii",
                    $t['ticket_name'],
                    $t['price'],
                    $t['capacity'],
                    $sale_start,
                    $sale_end,
                    $t['status'],
                    $ticket_id,
                    $eid
                );
                $updateTicket->execute();
                if ($updateTicket->error) {
                    throw new Exception("Error updating ticket tier: {$updateTicket->error}");
                }
            } else {
                $ticket_id = $nextTicketId++;
                $sold_count = 0;
                $insertTicket->bind_param(
                    "iisdiisss",
                    $ticket_id,
                    $eid,
                    $t['ticket_name'],
                    $t['price'],
                    $t['capacity'],
                    $sold_count,
                    $sale_start,
                    $sale_end,
                    $t['status']
                );
                $insertTicket->execute();
                if ($insertTicket->error) {
                    throw new Exception("Error inserting ticket tier: {$insertTicket->error}");
                }
            }
        }

        $connection->commit();
        header("Location:../../admin/ViewEventList.php");
        exit();
    } catch (Exception $e) {
        $connection->rollback();
        $_SESSION['form_errors'] = [$e->getMessage()];
        header("Location:../../admin/editEvent.php?eid=".$eid);
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