use PHPUnit\Framework\TestCase;

<?php
// Tests for header.php navigation and session handling


class HeaderTest extends TestCase
{
    protected function setUp(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
        session_destroy();
    }

    public function testAdminLinkDisplayedForAdminRole(): void
    {
        $_SESSION['role'] = 'admin';
        ob_start();
        include '../public/header.php';
        $output = ob_get_clean();
        
        $this->assertStringContainsString('Admin Dash', $output);
        $this->assertStringContainsString('adminPanel.php', $output);
    }

    public function testAdminLinkNotDisplayedForNonAdmin(): void
    {
        $_SESSION['role'] = 'user';
        ob_start();
        include '../public/header.php';
        $output = ob_get_clean();
        
        $this->assertStringNotContainsString('Admin Dash', $output);
    }

    public function testSignUpLinkDisplayedWhenNotLoggedIn(): void
    {
        $_SESSION['name'] = '';
        ob_start();
        include '../public/header.php';
        $output = ob_get_clean();
        
        $this->assertStringContainsString('Sign up', $output);
    }

    public function testLogOutLinkDisplayedWhenLoggedIn(): void
    {
        $_SESSION['name'] = 'John Doe';
        ob_start();
        include '../public/header.php';
        $output = ob_get_clean();
        
        $this->assertStringContainsString('Log out', $output);
    }

    public function testAboutUsLinkPresent(): void
    {
        ob_start();
        include '../public/header.php';
        $output = ob_get_clean();
        
        $this->assertStringContainsString('About us', $output);
    }
}
?>