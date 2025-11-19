<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>Registration Form</title>
    <link rel="stylesheet" href="../css/formstyle.css" />
  </head>
  <body>
    <?php
    include "../backend/connect_db.php"
    ?>
    <h1>Sign Up</h1>
    <form method="post" action=''class="centered">
      <fieldset>
        <label for="email">Email:</label> <input id="email" name="email" type="email" required />
        <label for="new-password">Create New Password:</label> <input id="new-password" name="new-password" type="password" pattern="[a-z0-5]{8,}" required /><br><span id="validate_pass"></span>
      </fieldset>
      <fieldset>
      <input type="submit" value="Submit" onclick=""/>
    </form> 
    <script  src=""></script>
  </body>
</html>