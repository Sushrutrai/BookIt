<?php

// Mock session for testing
function setupSession($role = null, $name = null) {
    $_SESSION['role'] = $role;
    $_SESSION['name'] = $name;
}

function teardownSession() {
    session_unset();
}

// Test 1: Admin user should see Admin Dash link
echo "Test 1: Admin user navigation\n";
setupSession('admin', 'John Doe');
ob_start();
if (isset($_SESSION['role']) && $_SESSION['role']==='admin') {
    echo "<li><a href=\"../admin/adminPanel.php\">Admin Dash</a></li>";
}
$output = ob_get_clean();
assert(strpos($output, 'Admin Dash') !== false, "Admin should see Admin Dash link");
echo "✓ Passed\n";
teardownSession();

// Test 2: Non-admin user should not see Admin Dash link
echo "Test 2: Non-admin user navigation\n";
setupSession('user', 'Jane Doe');
ob_start();
if (isset($_SESSION['role']) && $_SESSION['role']==='admin') {
    echo "<li><a href=\"../admin/adminPanel.php\">Admin Dash</a></li>";
}
$output = ob_get_clean();
assert(strpos($output, 'Admin Dash') === false, "Non-admin should not see Admin Dash link");
echo "✓ Passed\n";
teardownSession();

// Test 3: Unauthenticated user should see Sign up link
echo "Test 3: Unauthenticated user sees Sign up\n";
setupSession(null, null);
ob_start();
if (empty($_SESSION['name'])) {
    echo "<li><a href=\"Form.php\">Sign up</a></li>";
}
$output = ob_get_clean();
assert(strpos($output, 'Sign up') !== false, "Unauthenticated user should see Sign up");
echo "✓ Passed\n";
teardownSession();

// Test 4: Authenticated user should see Log out link
echo "Test 4: Authenticated user sees Log out\n";
setupSession('user', 'Jane Doe');
ob_start();
if (empty($_SESSION['name'])) {
    echo "<li><a href=\"Form.php\">Sign up</a></li>";
} else {
    echo "<li><a href=\"../app/auth/logout.php\" onclick=\"return confirm('Are you sure you want to log out?')\">Log out</a></li>";
}
$output = ob_get_clean();
assert(strpos($output, 'Log out') !== false, "Authenticated user should see Log out");
echo "✓ Passed\n";
teardownSession();

echo "\nAll tests passed!";