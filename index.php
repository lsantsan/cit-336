<?php

require('model/database.php');
require('model/category_db.php');
require('model/recipe_db.php');
require('model/user_db.php');

define('FILE_SIZE_LIMIT', 1024 * 1024 * 1); //File size limit: 1MB
define('IMG_URL', 'view' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'user_images'); //Location where userImages are saved on the server.
define('IMG_WIDTH', 250); //Default width for images
define('IMG_HEIGHT', 250); //Default height for images
define('DEFAULT_IMAGE', 'default_image.jpg'); //Default image's name

$lifetime = 60 * 60 * 4; //4 hours in secods
session_set_cookie_params($lifetime, '/');
session_start();
$activeUser = array();
if (isset($_SESSION['activeUser'])) {
    $activeUser = $_SESSION['activeUser']; //activeUser[0]=>userId;
}

$action = filter_input(INPUT_POST, 'action');
if ($action == NULL) {
    $action = filter_input(INPUT_GET, 'action');
    if ($action == NULL) {
        $action = 'show_login';
    }
}

switch ($action) {
    case 'show_class_work':
        include('../class_work/index.php');
        break;
    case 'show_teaching_presentation':
        include('../class_work/teaching_presentation/index.php');
        break;
    case 'show_new_recipe':
        if (!isUserLogged()) {
            header("Location: ?action=show_login");
        }

        $categories = get_categories();
        $categoryId = 0;
        include('view/new_recipe.php');
        break;
    case 'show_edit_recipe':
        $isEditRecipe = TRUE;
        if (!isUserLogged()) {
            header("Location: ?action=show_login");
        }

        $recipeId = filter_input(INPUT_GET, 'recipe_id', FILTER_VALIDATE_INT);

        if ($recipeId == NULL || $recipeId == FALSE) {
            $error_message = "Invalid recipe.";
        }
        if (isset($error_message)) {
            include('view/new_recipe.php');
        } else {
            $categories = get_categories();
            $recipe = get_recipe_by_id($recipeId);
            if ($recipe['user_id'] != $activeUser[0]) { //Checks if current user can edit recipe
                $error_message = "Invalid recipe.";
                include('view/new_recipe.php');
            } else {
                $categoryId = $recipe['category_id'];
                $recipeName = $recipe['name'];
                $ingredients = $recipe['ingredient'];
                $portions = $recipe['portion'];
                $directions = $recipe['direction'];
                include('view/new_recipe.php');
            }
        }

        break;
    case 'show_signup':
        $userId = filter_input(INPUT_GET, 'user_id', FILTER_VALIDATE_INT);
        if ($userId != NULL && $userId != FALSE && isset($activeUser[0]) && $activeUser[0] == $userId) {
            $isUpdateUser = TRUE;
            $user = get_user_by_id($userId);
            $firstName = $user['first_name'];
            $lastName = $user['last_name'];
            $email = $user['email_address'];
            $username = $user['username'];
        }
        include('view/signup.php');
        break;
    case 'show_login':
        if (isUserLogged()) {
            header("Location: ?action=show_home&user_id=$activeUser[0]");
        } else {
            include('view/login.php');
        }
        break;
    case 'show_home':
        $userId = filter_input(INPUT_GET, 'user_id', FILTER_VALIDATE_INT);
        if ($userId == NULL || $userId == FALSE) {
            $error_message = "Invalid user.";
            include('view/login.php');
        } else if (!isset($activeUser[0]) || $activeUser[0] != $userId) {
            $error_message = "Login again.";
            include('view/login.php');
        } else {
            $user = get_user_by_id($userId);
            $categories = get_categories();
            $allRecipes = get_all_recipes_by_user($userId);
            include('view/home.php');
        }
        break;
    case 'show_recipe':
        if (!isUserLogged()) {
            header("Location: ?action=show_login");
        }

        $recipeId = filter_input(INPUT_GET, 'recipe_id', FILTER_VALIDATE_INT);

        if ($recipeId == NULL || $recipeId == FALSE) {
            $error_message = "Invalid recipe.";
        } else {
            $recipe = get_recipe_by_id($recipeId);
            if ($recipe['user_id'] != $activeUser[0]) { //Checks if current user can open recipe
                $error_message = "Invalid recipe id.";
            }
        }
        if (isset($error_message)) {
            $errorTitle = "View Recipe Error";
            include('errors/generic.php');
        } else {
            $recipe = get_recipe_by_id($recipeId);
            $directionList = explode("\n", $recipe['direction']);
            $ingredientList = split('[;]', $recipe['ingredient']);
            array_pop($ingredientList); //Remove the last element, which is an empty value    

            include('view/recipe.php');
        }
        break;
    case 'logout':
        session_destroy();
        header("Location: ?action=show_login");
        break;
    case 'login_user':
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

        if ($username == NULL || $username == FALSE || ctype_space($username)) {
            $error_message = "Invalid username.";
        } else if ($password == NULL || $password == FALSE || ctype_space($password)) {
            $error_message = "Invalid password.";
        }
        if (isset($error_message)) {
            include('view/login.php');
        } else {

            $password_encrypted = encryptPassword($password);
            $userId = check_user($username, $password_encrypted);
            if (!isset($userId)) {
                $error_message = "Invalid username or password.";
                include('view/login.php');
            } else {
                $activeUser[0] = $userId;
                $_SESSION['activeUser'] = $activeUser;
                header("Location: ?action=show_home&user_id=$userId");
            }
        }

        break;
    case 'save_recipe':
        $categories = get_categories();
        if (!isUserLogged()) {
            header("Location: ?action=show_login");
        }

        $isCancel = filter_input(INPUT_POST, 'cancel', FILTER_SANITIZE_STRING);
        $categoryId = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT);
        $recipeName = filter_input(INPUT_POST, 'recipe_name', FILTER_SANITIZE_STRING);
        $ingredients = filter_input(INPUT_POST, 'ingredients', FILTER_SANITIZE_STRING);
        $portions = filter_input(INPUT_POST, 'portions', FILTER_VALIDATE_INT);
        $directions = filter_input(INPUT_POST, 'directions', FILTER_SANITIZE_STRING);
        $tmpFile = $_FILES['recipe_image'];
                
         if ($isCancel != NULL) {
            header("Location: ?action=show_home&user_id=$activeUser[0]");
            break;
        }
        
        $error_message = validateRecipeInputs($categoryId, $recipeName, $ingredients, $portions, $directions, $tmpFile);
        if (isset($error_message)) {
            include('view/new_recipe.php');
            break;
        }
        if (isset($tmpFile) && !empty($tmpFile['name'])) { //WITH IMAGE
            $uploadResult = uploadImage($tmpFile);
            if (isset($uploadResult['error'])) {
                $error_message = $uploadResult['error'];
                include('view/new_recipe.php');
                break;
            }
            $newImagePath = $uploadResult['success'];
        } else { //WITHOUT IMAGE
            $newImagePath = IMG_URL . DIRECTORY_SEPARATOR . DEFAULT_IMAGE;
        }
        //$ingredients = preg_replace('#\n+#',';',trim($ingredientsDirty));         
        IF(substr($ingredients, -1) != ';'){$ingredients.= ';';}
        add_recipe($categoryId, $activeUser[0], $recipeName, $ingredients, $portions, $directions, $newImagePath);
        $success_message = "Sucess! Recipe created.";
        $recipeName = $ingredients = $portions = $directions = "";
        include('view/new_recipe.php');
        break;

    case 'update_recipe':
        $isEditRecipe = TRUE;
        $newImagePath = NULL;
        $categories = get_categories();
        if (!isUserLogged()) {
            header("Location: ?action=show_login");
        }

        $isCancel = filter_input(INPUT_POST, 'cancel', FILTER_SANITIZE_STRING);
        $categoryId = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT);
        $recipeName = filter_input(INPUT_POST, 'recipe_name', FILTER_SANITIZE_STRING);
        $ingredients = filter_input(INPUT_POST, 'ingredients', FILTER_SANITIZE_STRING);
        $portions = filter_input(INPUT_POST, 'portions', FILTER_VALIDATE_INT);
        $directions = filter_input(INPUT_POST, 'directions', FILTER_SANITIZE_STRING);
        $recipeId = filter_input(INPUT_POST, 'recipe_id', FILTER_VALIDATE_INT);
        $tmpFile = $_FILES['recipe_image'];

        if ($isCancel != NULL) {
            header("Location: ?action=show_home&user_id=$activeUser[0]");
            break;
        }

        $error_message = validateRecipeInputs($categoryId, $recipeName, $ingredients, $portions, $directions, $tmpFile);
        if ($recipeId == NULL || $recipeId == FALSE) {
            $error_message = "Invalid recipe id.";
        }

        if (isset($error_message)) {
            include('view/new_recipe.php');
            break;
        }
        if (isset($tmpFile) && !empty($tmpFile['name'])) { //WITH IMAGE
            $uploadResult = uploadImage($tmpFile);
            if (isset($uploadResult['error'])) {
                $error_message = $uploadResult['error'];
                include('view/new_recipe.php');
                break;
            }
            $newImagePath = $uploadResult['success'];
        }
        //$ingredients = preg_replace('#\n+#',';',trim($ingredientsDirty));         
        IF(substr($ingredients, -1) != ';'){$ingredients.= ';';}
        update_recipe($recipeId, $categoryId, $recipeName, $ingredients, $portions, $directions, $newImagePath);
        $success_message = "Sucess! Recipe Updated.";
        include('view/new_recipe.php');
        break;
    case 'delete_recipe':
        if (!isUserLogged()) {
            header("Location: ?action=show_login");
        }

        $recipeId = filter_input(INPUT_GET, 'recipe_id', FILTER_VALIDATE_INT);

        if ($recipeId == NULL || $recipeId == FALSE) {
            $error_message = "Invalid recipe id.";
        } else {
            $recipe = get_recipe_by_id($recipeId);
            if ($recipe['user_id'] != $activeUser[0]) { //Checks if current user can delete recipe
                $error_message = "Invalid recipe id.";
            }
        }
        if (isset($error_message)) {
            $errorTitle = "Delete Error";
            include('errors/generic.php');
        } else {
            delete_recipe($recipeId);
            header("Location: ?action=show_home&user_id=$activeUser[0]");
        }

        break;
    case 'update_user':
        $isUpdateUser = TRUE;
        if (!isUserLogged()) {
            header("Location: ?action=show_login");
        }

        $firstName = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING);
        $lastName = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
        $confirmPassword = filter_input(INPUT_POST, 'confirm_password', FILTER_SANITIZE_STRING);
        $userId = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);

        $error_message = validateUserInputs($firstName, $lastName, $email, $username, $password, $confirmPassword, $userId);
        if (isset($error_message)) {
            include('view/signup.php');
        } else {
            if ($password != NULL || $password != FALSE || ctype_space($password)) {
                $password_encrypted = encryptPassword($password);
                update_user($userId, $firstName, $lastName, $email, $username, $password_encrypted);
            } else {
                update_user($userId, $firstName, $lastName, $email, $username);
            }
            $success_message = "Sucess! User updated.";
            include('view/signup.php');
        }

        break;
    case 'signup':
        $firstName = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING);
        $lastName = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
        $confirmPassword = filter_input(INPUT_POST, 'confirm_password', FILTER_SANITIZE_STRING);
        $userId = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_STRING);

        $error_message = validateUserInputs($firstName, $lastName, $email, $username, $password,$confirmPassword );
        if (isset($error_message)) {
            include('view/signup.php');
        } else {
            $password_encrypted = encryptPassword($password);
            add_user($firstName, $lastName, $email, $username, $password_encrypted);
            $firstName = $lastName = $email = $username = $password = $confirmPassword = "";
            $success_message = "Sucess! User created.";
            include('view/signup.php');
        }
        break;
}

function encryptPassword($string) {
    $salt = "$31n@t3";
    $hash = crypt($string, $salt);
    return $hash;
}

function isUserLogged() {
    global $activeUser;

    if (isset($activeUser[0])) {
        return TRUE;
    }
    return FALSE;
}

function validateRecipeInputs($categoryId, $recipeName, $ingredients, $portions, $directions, $tmpFile) {

    if ($categoryId == NULL || $categoryId == FALSE) {
        return "Invalid category id.";
    } else if ($recipeName == NULL || $recipeName == FALSE || ctype_space($recipeName)) {
        return "Invalid recipe name.";
    } else if ($ingredients == NULL || $ingredients == FALSE || ctype_space($ingredients)) {
        return "Invalid ingredients.";
    } else if ($portions == NULL || $portions == FALSE || $portions <= 0) {
        return "Invalid portions.";
    } else if ($directions == NULL || $directions == FALSE || ctype_space($directions)) {
        return "Invalid directions.";
    }else if (strpos($ingredients, ';') == FALSE) {
        return "Separate each ingredient with a semicolon(;).<br>i.e. Milk; Eggs;";
    } else if (isset($tmpFile) && !empty($tmpFile['name'])) {
        $imageInfo = getimagesize($tmpFile['tmp_name']);
        if (!isset($imageInfo) || empty($imageInfo)) { //It's not an image;
            return "The file has to be an image.";
        }
        $imageType = $imageInfo[2];
        if ($imageType != IMAGETYPE_JPEG && $imageType != IMAGETYPE_PNG) {
            return "Image's format has to be either JPEG or PNG.";
        } else if ($tmpFile['size'] > FILE_SIZE_LIMIT) {
            return "Image is too large. The limit is 1 MB.";
        }
    }

    return NULL;
}

function validateUserInputs($firstName, $lastName, $email, $username, $password, $confirmPassword, $userId = 0) {
    global $activeUser;
    if ($firstName == NULL || $firstName == FALSE || ctype_space($firstName)) {
        return "Invalid first name.";
    } else if ($lastName == NULL || $lastName == FALSE || ctype_space($lastName)) {
        return "Invalid last name.";
    } else if ($email == NULL || $email == FALSE || ctype_space($email)) {
        return "Invalid email address.";
    } else if ($username == NULL || $username == FALSE || ctype_space($username)) {
        return "Invalid username.";
    } else if ($userId == 0 && ($password == NULL || $password == FALSE || ctype_space($password))) {
        return "Invalid password.";
    }else if ($userId == 0 && ($confirmPassword == NULL || $confirmPassword == FALSE || ctype_space($confirmPassword))) {
        return "Invalid password.";
    }else if ($userId == 0 && ($password != $confirmPassword)) {
        return "Passwords do not match.";
    }
    //Password REGEX
    $uppercase_match = preg_match('/[A-Z]/', $password);
    $lowercase_match = preg_match('/[a-z]/', $password);
    $number_match = preg_match('/[0-9]/', $password);
    if ($userId == 0 && (!$uppercase_match || !$lowercase_match || !$number_match)) {
        return "Invalid password.";
    }else if ($userId != 0 && ($password != $confirmPassword)) {
        return "Passwords do not match.";
    }  else if ($userId != 0 && ($password != NULL || $password != FALSE || ctype_space($password)) && (!$uppercase_match || !$lowercase_match || !$number_match)) {
        return "Invalid password.";
    } else if ($userId != 0 && (isset($activeUser[0]) && $activeUser[0] != $userId)) {
        return "Invalid user id.";
    }
    return NULL;
}

function uploadImage($tmpFile) {
    global $activeUser;
    //VALIDATE IMAGE
    $imageInfo = getimagesize($tmpFile['tmp_name']);
    if (!isset($imageInfo) || empty($imageInfo)) { //It's not an image;
        $result = array("error" => "The file has to be an image.");
        return $result;
    }
    $imageTypeCode = $imageInfo[2];
    if ($imageTypeCode != IMAGETYPE_JPEG && $imageTypeCode != IMAGETYPE_PNG) {
        $result = array("error" => "Image's format has to be either JPEG or PNG.");
        return $result;
    }if ($tmpFile['size'] > FILE_SIZE_LIMIT) {
        $result = array("error" => "Image is too large. The limit is 1 MB.");
        return $result;
    }
    //UPLOAD IMAGE
    $tmpImageName = $tmpFile['name'];
    $imageName = explode('.', $tmpImageName);
    $imageType = end($imageName); //Extract image's type to be used later.            
    array_pop($imageName); //Removes the image type. i.e. test.jpg => test
    $newImageName = strtolower(implode('_', $imageName));
    $newImageName .= "_$activeUser[0].$imageType";
    $newImagePath = IMG_URL . DIRECTORY_SEPARATOR . $newImageName;

    /* if (file_exists($newImagePath)) {
      $result = array("error" => "Duplicated image. Change file's name.");
      return $result;
      } */
    resizeImage($tmpFile, $imageTypeCode);
    $isImageSaved = move_uploaded_file($tmpFile['tmp_name'], $newImagePath);
    if ($isImageSaved) {
        $result = array("success" => $newImagePath);
        return $result;
    } else {
        $result = array("error" => "Problem uploading the image. Try again later.");
        return $result;
    }
}

function resizeImage($tmpFile, $imageTypeCode) {
    switch ($imageTypeCode) {
        case IMAGETYPE_JPEG:
            $oldImage = imagecreatefromjpeg($tmpFile['tmp_name']);
            break;
        case IMAGETYPE_PNG:
            $oldImage = imagecreatefrompng($tmpFile['tmp_name']);
            break;
        default:
            $oldImage = imagecreatefromjpeg(IMG_URL . DIRECTORY_SEPARATOR . DEFAULT_IMAGE);
            break;
    }
    $oldWidth = imagesx($oldImage);
    $oldHeight = imagesy($oldImage);
    $newImage = imagecreatetruecolor(IMG_WIDTH, IMG_HEIGHT);
    $newX = $newY = $oldX = $oldY = 0;
    imagecopyresampled($newImage, $oldImage, $newX, $newY, $oldX, $oldY, IMG_WIDTH, IMG_HEIGHT, $oldWidth, $oldHeight);
    switch ($imageTypeCode) {
        case IMAGETYPE_JPEG:
            imagejpeg($newImage, $tmpFile['tmp_name']);
            break;
        case IMAGETYPE_PNG:
            imagepng($newImage, $tmpFile['tmp_name']);
            break;
        default:
            imagejpeg($newImage, IMG_URL . DIRECTORY_SEPARATOR . DEFAULT_IMAGE);
            break;
    }
    imagedestroy($newImage);
    imagedestroy($oldImage);
}
