 <?php
       $connection = new mysqli("localhost", "root", "", "bookit");
       if($connection->connect_error) {
            die("connection failed :".$connection->connect_error);  
       }
?> 