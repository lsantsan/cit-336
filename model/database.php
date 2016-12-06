<?php
    $dsn = 'mysql:host=localhost;dbname=mybrazi1_recipe_app';
    $db_username = 'mybrazi1_iClient';
    $db_password = 'VZ26BK3KCGTU';

    try {
        $db = new PDO($dsn, $db_username, $db_password);
    } catch (PDOException $e) {
        $db_error_message = $e->getMessage();
        $errorTitle = "Database Error";
        $error_message = "Sorry, there was an error connecting to the database. Try again later.";
        include('errors/generic.php');
        exit();
    }
