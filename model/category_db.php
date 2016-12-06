<?php

function get_categories() {
    global $db;
    $query = 'SELECT * FROM category
              ORDER BY category_id';
    $statement = $db->prepare($query);
    $statement->execute();
    $categories = $statement->fetchAll();
    $statement->closeCursor();   
    return $categories;
}
