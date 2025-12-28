<?php
session_start();
require_once '../backend/config.php';

if(isset($_POST['register'])){
    $name=$_POST['name'];
    $email=$_POST['email'];
    $password= password_hash($_POST['password'],PASSWORD_DEFAULT);
    $role="user";

    $checkEmail=$connection->query("select email from users where email='$email'");
    if($checkEmail->num_rows >0){
        $_SESSION['register_error']="Email is already registered!";
        $_SESSION['active_form']="register";
    }else{
        $connection->query("insert into users(name,email,password,role) values('$name','$email','$password','$role')");
    }
    header("Location:../src/Form.php");
    exit();
}
if(isset($_POST['login'])){
    $email=$_POST['email'];
    $password=$_POST['password'];

    $result=$connection->query("select * from users where email='$email'");
    if($result->num_rows > 0){
        $user=$result->fetch_assoc();
        if(password_verify($password,$user['password'])){
            $_SESSION['name']=$user['name'];
            $_SESSION['email']=$user['email'];
            $_SESSION['role']=$user['role'];

            if($user['role']==='admin'){
                header("Location:../admin/src/adminPanel.php");
            }else{
                header("Location:../src/index.php");
            }
            exit();
        }
    }
    $_SESSION['login_error']='Incorrect email or password';
    $_SESSION['active_form']='login';
    header("Location:../src/Form.php");
    exit();
}
?>