<?php
$command = sprintf("python email_handler.py %s %s %s %s",
        $_POST["firstname"], $_POST["lastname"], $_POST["gender"], $_POST["email"]);
$output = shell_exec("$command");
echo $output;
sleep(5);
header("Location:index.php");
?>
