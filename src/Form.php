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
    <link rel="stylesheet" href="../css/formstyle.css" />
  </head>
  <body>
   <div id="login-form" class="form-box <?=isActiveFrom('login',$activeForm)?>">
    <form method="post" action="../backend/login_register.php" class="centered" >
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
    <form method="post" action="../backend/login_register.php" class="centered">
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

    <script  src="../js/form_interaction.js"></script>
  </body>
</html>