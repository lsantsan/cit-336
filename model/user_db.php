<?php

function add_user($firstName, $lastName, $email, $username, $password) {
    global $db;
    $query = 'INSERT INTO user
                 (first_name, last_name, email_address, username, password)
              VALUES
                 (:firstName, :lastName, :email, :username, :password)';
    $statement = $db->prepare($query);
    $statement->bindValue(':firstName', $firstName);
    $statement->bindValue(':lastName', $lastName);
    $statement->bindValue(':email', $email);
    $statement->bindValue(':username', $username);
    $statement->bindValue(':password', $password);
    $statement->execute();
    $statement->closeCursor();
}

function update_user($userId, $firstName, $lastName, $email, $username, $password = NULL) {
    global $db;
    if ($password) {
        $query = 'UPDATE user '
                . 'SET first_name=:firstName, last_name=:lastName, email_address=:email, username=:username, password=:password '
                . 'WHERE user_id=:userId';
    } else {
        $query = 'UPDATE user '
                . 'SET first_name=:firstName, last_name=:lastName, email_address=:email, username=:username '
                . 'WHERE user_id=:userId';
    }



    $statement = $db->prepare($query);
    $statement->bindValue(':userId', $userId);
    $statement->bindValue(':firstName', $firstName);
    $statement->bindValue(':lastName', $lastName);
    $statement->bindValue(':email', $email);
    $statement->bindValue(':username', $username);
    ($password) ? $statement->bindValue(':password', $password) : null;
    $statement->execute();
    $statement->closeCursor();
}

function get_user_by_id($userId) {
    global $db;
    $query = 'SELECT * FROM user
              WHERE user_id = :userId';
    $statement = $db->prepare($query);
    $statement->bindValue(":userId", $userId);
    $statement->execute();
    $user = $statement->fetch();
    $statement->closeCursor();
    return $user;
}

function check_user($username, $password) {
    global $db;
    $query = 'SELECT user_id FROM user
              WHERE username = :username 
              AND password = :password';
    $statement = $db->prepare($query);
    $statement->bindValue(":username", $username);
    $statement->bindValue(":password", $password);
    $statement->execute();
    $result = $statement->fetch();
    $statement->closeCursor();
    return $result['user_id'];
}
