<?php
session_start();

$errors=[
  'login'=>$_SESSION['login_error'] ??'',
  'register'=>$_SESSION['register_error']??''
];
$activeForm=$_SESSION['active_form']??'login';

session_unset();

function showError($error){
  return !empty($error)?"<p class='error-message'>$error</p>":'';
}

function isActiveFrom($formName,$activeForm){
  return $formName === $activeForm ?'active':'';
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>Registration Form</title>
    <link rel="stylesheet" href="../assets/css/formstyle.css" />
  </head>
  <body>
   <div id="login-form" class="form-box <?=isActiveFrom('login',$activeForm)?>">
    

    <form method="post" action="../../app/auth/login_register.php" class="centered" >
      <a href="index.php"><svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
      <rect width="36" height="36" rx="18" fill="white"/>
        <path d="M27 17.2857V19.7143H12.75L17.5 24.5714L16.3125 27L8 18.5L16.3125 10L17.5 12.4286L12.75 17.2857H27Z" fill="black"/>
    </svg></a>
      <h1>Log In</h1>
        <?=showError($errors['login']);?>
        <fieldset>
          <label for="email">Email:</label> <input id="login-email" name="email" type="email" required />
          <label for="password">Password:</label> <input id="login-password" name="password" type="password"  required />
        </fieldset>
        <input type="submit"  name="login"onclick=""/>
        <p>Don't have an account? <a href="#register-form" onclick="showForm('register-form')">Sign up</a></p>
      </form> 
   </div>
   <div id="register-form" class="form-box <?=isActiveFrom('register',$activeForm)?>">
    <form method="post" action="../../app/auth/login_register.php" class="centered">
       <a href="index.php"><svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
      <rect width="36" height="36" rx="18" fill="white"/>
        <path d="M27 17.2857V19.7143H12.75L17.5 24.5714L16.3125 27L8 18.5L16.3125 10L17.5 12.4286L12.75 17.2857H27Z" fill="black"/>
    </svg></a>
      <h1>Sign Up</h1>
       <?=showError($errors['register']);?>
        <fieldset>
          <label for="name">Name:</label> <input id="name" name="name" type="text" required />
          <label for="email">Email:</label> <input id="register-email" name="email" type="email" required />
          <label for="password">Create New Password:</label><input id="register-password" name="password" type="password" oninput="checkPasswordStrength()" required />
          <span id="password_strength"></span>
        </fieldset>
        <input type="submit"  name="register"onclick=""/>
        <p>Already have an account? <a href="#login-form" onclick="showForm('login-form')">Log in</a></p>
      </form> 
   </div>

    <script  src="../assets/js/form_interaction.js"></script>
  </body>
</html>