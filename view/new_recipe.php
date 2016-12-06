<?php include('header.php'); ?>
<main>
    <h1 id="recipes_title"><?php
        if (isset($isEditRecipe) && $isEditRecipe) {
            echo "Edit Recipe";
        } else {
            echo "New Recipe";
        }
        ?></h1>

    <div class="recipes_container">

        <div class="signup_container">
            <?php
            if (isset($error_message)) {
                echo "<p class='error_message'>$error_message</p>";
            } else if (isset($success_message)) {
                echo "<p class='success_message'>$success_message</p>";
            }
            ?>
            <div class="form">
                <form action="index.php" method="post" id="new_recipe_form" enctype="multipart/form-data">
                    <?php if (isset($isEditRecipe) && $isEditRecipe) { ?>
                        <input type="hidden" name="action" value="update_recipe">
                        <input type="hidden" name="recipe_id" value="<?php echo $recipeId ?>">
                    <?php } else { ?>
                        <input type="hidden" name="action" value="save_recipe">   
                    <?php } ?>   
                                             
                    <label>Category:</label>
                    <select name="category_id">
                        <?php
                         $optList = "";
                        foreach ($categories as $category) {
                            if ($categoryId == $category['category_id']) {
                                $optList .= "<option selected value='{$category['category_id']}'>{$category['name']}</option>";
                            } else {
                                $optList .= "<option value='{$category['category_id']}'>{$category['name']}</option>";
                            }
                        }
                        echo $optList;
                        ?>
                    </select>
                    <br>
                    <label>Name:</label>
                    <input type="text" name="recipe_name" required value="<?php
                    if (isset($recipeName)) {
                        echo $recipeName;
                    }
                    ?>"/>  
                    <br>
                    <div>
                        <label>Ingredients*:</label>
                        <textarea rows="5" cols="25" name="ingredients" required form="new_recipe_form"><?php
                            if (isset($ingredients)) {
                                echo $ingredients;
                            }
                            ?></textarea>
                    </div>
                    <br>
                    <label>Portions:</label>
                    <input type="number" name="portions" min="1" required value="<?php
                    if (isset($portions)) {
                        echo $portions;
                    }
                    ?>"/>  
                    <br>
                    <div>
                        <label>Directions:</label>
                        <textarea rows="5" cols="25" name="directions" required form="new_recipe_form"><?php
                            if (isset($directions)) {
                                echo $directions;
                            }
                            ?></textarea>
                    </div>
                    <br>
                    <label>Upload Image:</label>
                    <input type="file" name="recipe_image" />
                    <br>
                    <div>
                        <label>&nbsp;</label>
                        <input type="submit" name="save" value="Save" class="save_button" />
                        <input type="submit" name="cancel" value="Cancel" formnovalidate="formnovalidate" class="save_button" />
                       
                    </div>
                    <br>
                </form>
                <p class="observation">* When listing the ingredients, separate each ingredient with a semicolon(;)</p>
            </div>
        </div>    


    </div>

</main>
<?php include('footer.php'); ?>