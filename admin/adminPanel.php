<?php 
include __DIR__ . '/../app/bootstrap.php';

// Dashboard Metrics
$total_events = $connection->query('select count(*) as total_events from event_details')->fetch_assoc()['total_events'];

$total_tickets_sold = $connection->query('select sum(sold_count) as total from ticket_type')->fetch_assoc()['total'] ?? 0;

$total_revenue = $connection->query('select sum(total_amount) as total from bookings where payment_status = "completed"')->fetch_assoc()['total'] ?? 0;

$active_events = $connection->query('select count(*) as total from event_details where event_status = "active"')->fetch_assoc()['total'] ?? 0;

// Additional Metrics
$total_bookings = $connection->query('select count(*) as total from bookings')->fetch_assoc()['total'];

$total_users = $connection->query('select count(*) as total from users where role = "user"')->fetch_assoc()['total'];

$sold_out_tickets = $connection->query('select count(*) as total from ticket_type where status = "sold_out"')->fetch_assoc()['total'];

$completed_bookings = $connection->query('select count(*) as total from bookings where payment_status = "completed"')->fetch_assoc()['total'];

$pending_bookings = $connection->query('select count(*) as total from bookings where payment_status = "pending"')->fetch_assoc()['total'];

$average_order_value = $connection->query('select avg(total_amount) as avg_value from bookings where payment_status = "completed"')->fetch_assoc()['avg_value'] ?? 0;

$recent_events_stmt = $connection->prepare('select * from event_details e inner join event_categories c on e.category_id=c.category_id order by entered_at desc limit 3;');
$recent_events_stmt->execute();
$recent_events_result = $recent_events_stmt->get_result();
$recent_events = [];
while ($row = $recent_events_result->fetch_assoc()) {
    $recent_events[] = $row;
}

$recent_purchases_stmt = $connection->prepare('SELECT 
    b.booking_id,
    b.booking_reference, 
    b.total_amount, 
    b.created_at, 
    u.email, 
    MAX(e.event_name) as event_name
FROM bookings b 
JOIN users u ON b.user_id = u.id 
JOIN booking_items bi ON b.booking_id = bi.booking_id
JOIN ticket_type tt ON bi.ticket_type_id = tt.ticket_id
JOIN event_details e ON tt.eid = e.eid 
WHERE b.payment_status = "completed"
GROUP BY b.booking_id
ORDER BY b.created_at DESC 
LIMIT 10;');
$recent_purchases_stmt->execute();
$recent_purchases_result = $recent_purchases_stmt->get_result();
$recent_purchases = [];
while ($row = $recent_purchases_result->fetch_assoc()) {
    $recent_purchases[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/adminstyle.css">
    <link rel="stylesheet" href="../../assets/css/table.css">
    <link rel="stylesheet" href="../../assets/css/admin_dashboard.css">
    <title>Admin Dashboard</title>
    <style>
        .sidebar {
            width: 250px;
            background: #f4f4f4;
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            overflow-y: auto;
            transform: translateX(-100%);
            transition: transform 0.3s;
            z-index: 100;
        }
        .sidebar.open {
            transform: translateX(0);
        }
        .toggle-btn {
            display: block;
            position: fixed;
            top: 10px;
            left: 10px;
            z-index: 1000;
            background: #333;
            color: #fff;
            border: none;
            padding: 10px;
            cursor: pointer;
        }
        .main-content {
            margin-left: 0;
            padding: 20px;
        }
        .table-container {
            overflow-x: auto;
        }
        @media (min-width: 768px) {
            .sidebar {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 250px;
            }
            .toggle-btn {
                display: none;
            }
        }
    </style>
</head>
<body>
    <?php include __DIR__.'/partial/adminheader.php';?>
    <main class="centered">
        <div class="dashboard-header">
            <h2>Admin Dashboard</h2>
            <p>Real-time overview of your BookIt platform</p>
        </div>

        <!-- Primary Metrics Grid -->
        <div class="stats-grid">
           
                <div class="component-content metric-card events">
                    <svg width="48" height="48" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect width="36" height="36" rx="5" fill="url(#paint0_linear_1177_339)" fill-opacity="0.4" />
                        <path
                            d="M26.1818 22.5455V14.3636H13.4545V22.5455H26.1818ZM26.1818 9.81818C26.664 9.81818 27.1265 10.0097 27.4675 10.3507C27.8084 10.6917 28 11.1542 28 11.6364V22.5455C28 23.0277 27.8084 23.4901 27.4675 23.8311C27.1265 24.1721 26.664 24.3636 26.1818 24.3636H13.4545C12.9723 24.3636 12.5099 24.1721 12.1689 23.8311C11.8279 23.4901 11.6364 23.0277 11.6364 22.5455V11.6364C11.6364 11.1542 11.8279 10.6917 12.1689 10.3507C12.5099 10.0097 12.9723 9.81818 13.4545 9.81818H14.3636V8H16.1818V9.81818H23.4545V8H25.2727V9.81818H26.1818ZM9.81818 26.1818H22.5455V28H9.81818C9.33597 28 8.87351 27.8084 8.53253 27.4675C8.19156 27.1265 8 26.664 8 26.1818V15.2727H9.81818V26.1818ZM24.3636 20.7273H20.7273V17.0909H24.3636V20.7273Z"
                            fill="#667eea" />
                        <defs>
                            <linearGradient id="paint0_linear_1177_339" x1="18" y1="0" x2="18" y2="36"
                                gradientUnits="userSpaceOnUse">
                                <stop stop-color="#FFF3C4" stop-opacity="0.8" />
                                <stop offset="1" stop-color="#FFD54F" />
                            </linearGradient>
                        </defs>
                    </svg>
                    <div class="metric-content-text">
                        <p class="metric-label">Total Events</p>
                        <span class="metric-value"><?php echo $total_events; ?></span>
                        <p class="metric-subtext"><?php echo $active_events; ?> active</p>
                    </div>
                </div>
           
                <div class="component-content metric-card bookings">
                    <svg width="48" height="48" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect width="36" height="36" rx="5" fill="url(#paint0_linear_1177_351)" fill-opacity="0.4" />
                        <path
                            d="M23.8496 20.3827C23.8496 18.2541 25.3648 16.9617 27 16.5858V15.2939H8V16.5839C8.66372 16.7361 9.30154 17.044 9.83301 17.5204C10.6466 18.2498 11.1504 19.3202 11.1504 20.6474C11.1503 21.9745 10.6466 23.045 9.83301 23.7743C9.30166 24.2505 8.66354 24.5567 8 24.7089V25.9999H27V24.6757C26.2726 24.4606 25.6168 24.0266 25.1055 23.4823C24.3621 22.691 23.8497 21.5929 23.8496 20.3827ZM14.7783 13.2939H23.3779L22.1904 10.3036L14.7783 13.2939ZM25.8496 20.3827C25.8497 21.0251 26.1259 21.6474 26.5635 22.1132C27.0074 22.5856 27.5449 22.8232 28 22.8232C28.5522 22.8232 28.9998 23.271 29 23.8232V26.9999C29 27.5522 28.5523 27.9999 28 27.9999H7C6.44772 27.9999 6 27.5522 6 26.9999V23.8232C6.00016 23.271 6.44781 22.8232 7 22.8232C7.57203 22.8232 8.11272 22.6303 8.49805 22.2851C8.86555 21.9557 9.15029 21.4375 9.15039 20.6474C9.15039 19.8571 8.86554 19.3391 8.49805 19.0097C8.11271 18.6643 7.57215 18.4706 7 18.4706C6.44775 18.4706 6.00006 18.0228 6 17.4706V14.2939C6.00009 13.7417 6.44777 13.2939 7 13.2939H9.43262L22.376 8.07218L22.4697 8.03995C22.6913 7.97525 22.9295 7.98929 23.1436 8.08097C23.3881 8.18579 23.5815 8.38346 23.6797 8.63077L25.5283 13.2939H28C28.5522 13.2939 28.9999 13.7417 29 14.2939V17.4706C28.9999 18.0228 28.5522 18.4706 28 18.4706C26.8072 18.4706 25.8496 19.238 25.8496 20.3827Z"
                            fill="#f5576c" />
                        <path
                            d="M21.1744 21.4392C21.7267 21.4392 22.1744 21.8869 22.1744 22.4392C22.1743 22.9914 21.7266 23.4392 21.1744 23.4392H13.8248C13.2726 23.4392 12.825 22.9914 12.8248 22.4392C12.8248 21.8869 13.2725 21.4392 13.8248 21.4392H21.1744ZM16.9752 18.2625C17.5273 18.2627 17.9752 18.7103 17.9752 19.2625C17.9752 19.8146 17.5273 20.2622 16.9752 20.2625H13.8248C13.2725 20.2625 12.8248 19.8147 12.8248 19.2625C12.8248 18.7102 13.2725 18.2625 13.8248 18.2625H16.9752Z"
                            fill="#f5576c" />
                        <defs>
                            <linearGradient id="paint0_linear_1177_351" x1="18" y1="0" x2="18" y2="36"
                                gradientUnits="userSpaceOnUse">
                                <stop stop-color="#EFF6FF" />
                                <stop offset="1" stop-color="#2563EB" />
                            </linearGradient>
                        </defs>
                    </svg>
                    <div class="metric-content-text">
                        <p class="metric-label">Total Bookings</p>
                        <span class="metric-value"><?php echo $total_bookings; ?></span>
                        <p class="metric-subtext"><?php echo $completed_bookings; ?> completed</p>
                    </div>
                </div>
            
           
                <div class="component-content metric-card tickets">
                    <svg width="48" height="48" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect width="36" height="36" rx="5" fill="url(#paint0_linear_1184_359)" fill-opacity="0.4" />
                        <path
                            d="M22.2632 29C20.9526 29 19.8357 28.5123 18.9123 27.537C17.9889 26.5617 17.5269 25.3827 17.5263 24C17.5257 22.6173 17.9877 21.4383 18.9123 20.463C19.8369 19.4877 20.9539 19 22.2632 19C23.5724 19 24.6897 19.4877 25.6149 20.463C26.5402 21.4383 27.0019 22.6173 27 24C26.9981 25.3827 26.5361 26.562 25.614 27.538C24.6919 28.514 23.5749 29.0013 22.2632 29ZM23.85 26.375L24.5132 25.675L22.7368 23.8V21H21.7895V24.2L23.85 26.375ZM10.8947 28C10.3737 28 9.92779 27.8043 9.55705 27.413C9.18632 27.0217 9.00063 26.5507 9 26V12C9 11.45 9.18568 10.9793 9.55705 10.588C9.92842 10.1967 10.3743 10.0007 10.8947 10H14.85C15.0237 9.41667 15.3632 8.93767 15.8684 8.563C16.3737 8.18833 16.9263 8.00067 17.5263 8C18.1579 8 18.7225 8.18767 19.2202 8.563C19.7179 8.93833 20.0533 9.41733 20.2263 10H24.1579C24.6789 10 25.1252 10.196 25.4965 10.588C25.8679 10.98 26.0533 11.4507 26.0526 12V18.25C25.7684 18.0333 25.4684 17.85 25.1526 17.7C24.8368 17.55 24.5053 17.4167 24.1579 17.3V12H22.2632V15H12.7895V12H10.8947V26H15.9158C16.0263 26.3667 16.1526 26.7167 16.2947 27.05C16.4368 27.3833 16.6105 27.7 16.8158 28H10.8947ZM17.5263 12C17.7947 12 18.0199 11.904 18.2018 11.712C18.3837 11.52 18.4743 11.2827 18.4737 11C18.4731 10.7173 18.3821 10.48 18.2008 10.288C18.0196 10.096 17.7947 10 17.5263 10C17.2579 10 17.0331 10.096 16.8518 10.288C16.6705 10.48 16.5796 10.7173 16.5789 11C16.5783 11.2827 16.6693 11.5203 16.8518 11.713C17.0343 11.9057 17.2592 12.0013 17.5263 12Z"
                            fill="#00a8e8" />
                        <defs>
                            <linearGradient id="paint0_linear_1184_359" x1="18" y1="0" x2="18" y2="36"
                                gradientUnits="userSpaceOnUse">
                                <stop stop-color="#FFF3C4" stop-opacity="0.8" />
                                <stop offset="1" stop-color="#FFD54F" />
                            </linearGradient>
                        </defs>
                    </svg>
                    <div class="metric-content-text">
                        <p class="metric-label">Tickets Sold</p>
                        <span class="metric-value"><?php echo $total_tickets_sold; ?></span>
                        <p class="metric-subtext"><?php echo $sold_out_tickets; ?> sold out</p>
                    </div>
                </div>
            
           
                <div class="component-content metric-card revenue">
                    <svg width="48" height="48" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect width="36" height="36" rx="5" fill="url(#paint0_linear_1182_356)" fill-opacity="0.2" />
                        <path
                            d="M19.5 14.2778H18V19.5555L22.28 22.2366L23 20.9594L19.5 18.7639V14.2778ZM19 9C16.6131 9 14.3239 10.0009 12.636 11.7825C10.9482 13.5641 10 15.9804 10 18.5H7L10.96 22.7538L15 18.5H12C12 16.5403 12.7375 14.6609 14.0503 13.2753C15.363 11.8896 17.1435 11.1111 19 11.1111C20.8565 11.1111 22.637 11.8896 23.9497 13.2753C25.2625 14.6609 26 16.5403 26 18.5C26 20.4596 25.2625 22.339 23.9497 23.7247C22.637 25.1104 20.8565 25.8888 19 25.8888C17.07 25.8888 15.32 25.0549 14.06 23.7144L12.64 25.2133C13.4716 26.1004 14.4623 26.8037 15.5543 27.2822C16.6463 27.7606 17.8177 28.0046 19 27.9999C21.3869 27.9999 23.6761 26.999 25.364 25.2175C27.0518 23.4359 28 21.0195 28 18.5C28 15.9804 27.0518 13.5641 25.364 11.7825C23.6761 10.0009 21.3869 9 19 9Z"
                            fill="#ff9500" fill-opacity="0.75" />
                        <defs>
                            <linearGradient id="paint0_linear_1182_356" x1="18" y1="0" x2="18" y2="36"
                                gradientUnits="userSpaceOnUse">
                                <stop stop-color="#BBF7D0" stop-opacity="0.75" />
                                <stop offset="1" stop-color="#4ADE80" />
                            </linearGradient>
                        </defs>
                    </svg>
                    <div class="metric-content-text">
                        <p class="metric-label">Total Revenue</p>
                        <span class="metric-value">Rs. <?php echo number_format((float)$total_revenue, 0); ?></span>
                        <p class="metric-subtext">Avg: Rs. <?php echo number_format((float)$average_order_value, 0); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Secondary Metrics Grid -->
        <div class="secondary-metrics">
            <div class="secondary-metric users">
                <p class="secondary-metric-label">Total Users</p>
                <span class="secondary-metric-value"><?php echo $total_users; ?></span>
            </div>
            <div class="secondary-metric pending">
                <p class="secondary-metric-label">Pending Orders</p>
                <span class="secondary-metric-value"><?php echo $pending_bookings; ?></span>
            </div>
            <div class="secondary-metric completion">
                <p class="secondary-metric-label">Completion Rate</p>
                <span class="secondary-metric-value"><?php echo ($total_bookings > 0) ? round(($completed_bookings / $total_bookings) * 100, 1) : 0; ?>%</span>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <a href="addEventForm.php" class="action-link add-event">+ Add New Event</a>
            <a href="ViewEventList.php" class="action-link view-events">View All Events</a>
        </div>

        <!-- Recent Purchases Table -->
        <div class="component">
            <div class="component-content">
                <h3 class="section-title">Recent Completed Bookings</h3>
            </div>
            <?php if (count($recent_purchases) > 0): ?>
                <div class="table-container">
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>Booking Ref</th>
                                <th>Customer</th>
                                <th>Event</th>
                                <th>Amount</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_purchases as $purchase): ?>
                                <tr>
                                    <td class="booking-ref"><?php echo htmlspecialchars($purchase['booking_reference']); ?></td>
                                    <td class="event-name"><?php echo htmlspecialchars(substr($purchase['email'], 0, 20)); ?></td>
                                    <td class="event-name"><?php echo htmlspecialchars(substr($purchase['event_name'], 0, 25)); ?></td>
                                    <td class="booking-amount">Rs. <?php echo number_format((float)$purchase['total_amount'], 2); ?></td>
                                    <td class="booking-date"><?php echo date('d M, Y', strtotime($purchase['created_at'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p style="padding: 20px; text-align: center; color: #999;">No completed bookings yet</p>
            <?php endif; ?>
        </div>

        <!-- Recent Events -->
        <div class="component recent-events-section">
            <div class="component-content">
                <h3 class="section-title">Latest Events Created</h3>
            </div>
            <div class="table-container">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>Event Name</th>
                            <th>Category</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($recent_events as $event) {
                            $status_class = ($event['event_status'] === 'active') ? 'active' : 'inactive';
                            echo "<tr>
                                <td><strong>" . htmlspecialchars(substr($event['event_name'], 0, 30)) . "</strong></td>
                                <td class='event-category'>" . htmlspecialchars($event['category_name']) . "</td>
                                <td class='event-date'>" . date('d M Y', strtotime($event['event_date'])) . "</td>
                                <td><span class='event-status $status_class'>" . ucfirst(str_replace('-', ' ', $event['event_status'])) . "</span></td>
                            </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>
    <?php include __DIR__.'/partial/adminFooter.php';?>
</body>

</html>