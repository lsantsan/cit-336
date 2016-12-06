<?php

function add_recipe($categoryId, $userId, $name, $ingredients, $portions, $directions, $pictureUrl) {
    global $db;
    $query = 'INSERT INTO recipe
                 (category_id, user_id, name, ingredient, portion, direction, picture_url)
              VALUES
                 (:categoryId, :userId, :name, :ingredient, :portion, :direction, :pictureUrl)';
    $statement = $db->prepare($query);
    $statement->bindValue(':categoryId', $categoryId);
    $statement->bindValue(':userId', $userId);
    $statement->bindValue(':name', $name);
    $statement->bindValue(':ingredient', $ingredients);
    $statement->bindValue(':portion', $portions);
    $statement->bindValue(':direction', $directions);
    $statement->bindValue(':pictureUrl', $pictureUrl);
    $statement->execute();
    $statement->closeCursor();
}

function update_recipe($recipeId, $categoryId, $recipeName, $ingredients, $portions, $directions, $pictureUrl = NULL) {
    global $db;
    if ($pictureUrl) {
        $query = 'UPDATE recipe '
                . 'SET category_id=:categoryId, name=:name'
                . ', ingredient=:ingredient, portion=:portion, direction=:direction, picture_url=:pictureUrl '
                . 'WHERE recipe_id=:recipe_id';
    } else {
        $query = 'UPDATE recipe '
                . 'SET category_id=:categoryId, name=:name'
                . ', ingredient=:ingredient, portion=:portion, direction=:direction '
                . 'WHERE recipe_id=:recipe_id';
    }

    $statement = $db->prepare($query);
    $statement->bindValue(':recipe_id', $recipeId);
    $statement->bindValue(':categoryId', $categoryId);
    $statement->bindValue(':name', $recipeName);
    $statement->bindValue(':ingredient', $ingredients);
    $statement->bindValue(':portion', $portions);
    $statement->bindValue(':direction', $directions);
    ($pictureUrl)?$statement->bindValue(':pictureUrl', $pictureUrl) : null ;
    $statement->execute();
    $statement->closeCursor();
}

function get_recipe_by_id($recipeId) {
    global $db;
    $query = 'SELECT * FROM recipe
              WHERE recipe_id = :recipeId';
    $statement = $db->prepare($query);
    $statement->bindValue(":recipeId", $recipeId);
    $statement->execute();
    $recipe = $statement->fetch();
    $statement->closeCursor();
    return $recipe;
}

function get_recipes_by_category($userId, $categoryId) {
    global $db;
    $query = 'SELECT * FROM recipe
              WHERE category_id = :categoryId
              AND user_id = :userId
              ORDER BY recipe_id';
    $statement = $db->prepare($query);
    $statement->bindValue(":categoryId", $categoryId);
    $statement->bindValue(":userId", $userId);
    $statement->execute();
    $recipes = $statement->fetchAll();
    $statement->closeCursor();
    return $recipes;
}

function get_all_recipes_by_user($userId) {
    global $db;
    $query = 'SELECT * FROM recipe
              WHERE user_id = :userId
              ORDER BY recipe_id';
    $statement = $db->prepare($query);
    $statement->bindValue(":userId", $userId);
    $statement->execute();
    $recipes = $statement->fetchAll();
    $statement->closeCursor();
    return $recipes;
}

function delete_recipe($recipeId) {
    global $db;
    $query = 'DELETE FROM recipe
              WHERE recipe_id = :recipeId';
    $statement = $db->prepare($query);
    $statement->bindValue(':recipeId', $recipeId);
    $statement->execute();
    $statement->closeCursor();
}
