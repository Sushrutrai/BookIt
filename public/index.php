<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/grids.css">

    <title>BookIt</title>
</head>

<body>
    <?php include "header.php"; ?> 
    <main>
        <section class="hero" id="hero">
            <div class="overlay" id="left"></div>
            <div class="overlay" id="right"></div>
        </section>
         <section class="centered main_content">
            <div class="grid">
                <?php
                    require __DIR__.'/../app/bootstrap.php';

                    $statement=$connection->prepare('select * from event_details order by event_date desc;');
                    $statement->execute();
                    $result=$statement->get_result();

                    $userId = isset($_SESSION['id']) ? $_SESSION['id'] : 0;
                 
                    $bookmarkedEvents = [];
                    if ($userId) {
                        $bookmarkStmt = $connection->prepare("SELECT eid FROM bookmarks WHERE id=?");
<<<<<<< HEAD
                          $bookmarkStmt->bind_param('i', $userId);
=======
                        $bookmarkStmt->bind_param('i', $userId);
>>>>>>> copilot/worktree-2026-04-06T14-04-23
                        $bookmarkStmt->execute();
                        $bookmarkRes = $bookmarkStmt->get_result();
                        while ($bookmarkRow = $bookmarkRes->fetch_assoc()) {
                            $bookmarkedEvents[] = $bookmarkRow['eid'];
                        }
                        $bookmarkStmt->close();
                    }

                    while($row=$result->fetch_assoc()){
                        $activeClass = in_array($row['eid'], $bookmarkedEvents) ? 'is_active' : '';
<<<<<<< HEAD

=======
                        $disabledAttr = (isset($_SESSION['role']) && isset($_SESSION['id'])) ? '' : 'disabled';
                        $titleAttr = $disabledAttr ? 'title="Please log in to bookmark events"' : '';
>>>>>>> copilot/worktree-2026-04-06T14-04-23

                        echo "<article class='grid-item ' >
                            <div class='banner_img_container banner_img'>
                            <img  src='../uploads/".$row["event_image_path"]."' alt='".htmlspecialchars($row["event_name"])."'> 
<<<<<<< HEAD
                            <button class='favourite ".$activeClass."'>
=======
                            <button class='favourite ".$activeClass."' ".$disabledAttr." ".$titleAttr.">
>>>>>>> copilot/worktree-2026-04-06T14-04-23
                            <svg  width='24' height='24' viewBox='0 0 24 24' fill='none' xmlns='http://www.w3.org/2000/svg'>
                            <path d='M12 21.35L10.55 20.03C5.4 15.36 2 12.27 2 8.5C2 5.41 4.42 3 7.5 3C9.24 3 10.91 3.81 12 5.08C13.09 3.81 14.76 3 16.5 3C19.58 3 22 5.41 22 8.5C22 12.27 18.6 15.36 13.45 20.03L12 21.35Z' stroke='black' stroke-width='1'/>
                        </svg>
                        </button>
                            </div>
                            <h3 onclick=\"location.href='event_card.php?eid=".$row['eid']."'\">".htmlspecialchars($row['event_name'])."</h3>             
                            
                    <ul>
                        <li><img class='icon' src='../assets/icons/calender.svg' > ".date('d M ,Y', strtotime($row['event_date'])) ."</li>
                        <li><img class='icon' src='../assets/icons/time.svg' >".htmlspecialchars($row["event_location"])."</li>
                    </ul>
                    <button class='buy_button'type='button' onclick=\"return alert('ticket purchased')\">Buy Ticket</button>
      
                </article>
                        ";
                    }
                ?>
            </div>
        </section> 
    </main>
    <?php include "footer.php"; ?>
      <script>
    const favouriteButtons = document.querySelectorAll('.favourite');
    favouriteButtons.forEach(function(favourite, idx) {
        favourite.addEventListener('click', async function() {
            // Check if button is disabled (user not logged in)
            if (this.hasAttribute('disabled')) {
                alert('Please log in to bookmark events');
                return;
            }

            const isActive = this.classList.toggle('is_active');
            const userID = <?php echo isset($_SESSION['id']) ? $_SESSION['id'] : 0; ?>;
            const eid = this.closest('article').querySelector('h3').getAttribute('onclick').match(/eid=(\d+)/)[1];

            try {
                const response = await fetch('../app/actions/bookmarks.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id: userID,
                        eid: eid,
                        liked: isActive
                    })
                });

                const result = await response.json();

                if (result && result.success) {
                    alert(isActive ? 'Added to favourite' : 'Removed from favourite');
                } else {
                    this.classList.toggle('is_active');
                    alert('ERROR ' + (result.message || 'database error'));
                }
            } catch (error) {
                this.classList.toggle('is_active');
                console.error('Network error', error)
            }
        });
    });
    </script>
    <script src="../assets/js/home_interactivity.js"></script>
   
</body>
</html>