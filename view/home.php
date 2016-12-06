<?php include('header.php'); ?>    
<main>
    <div>
        <div id="username_container">
            <h1 id="recipes_title">Hello <?php
                if (isset($user['first_name'])) {
                    echo $user['first_name'];
                } else {
                    echo "";
                }
                ?></h1>
        </div>

        <div id="options_container">
            <a href="?action=show_signup&user_id=<?php echo $user['user_id'] ?>" title="Go to update user page">
                <div class="new_recipe" id="update_user_container">
                    <div id="update_user_icon_container">

                        <img alt ="Update User" title="Update User" src="view/images/gear.png"/>

                    </div>
                    <div>
                        <h3>Update User</h3>
                    </div>
                </div>
            </a>
            <a href="?action=show_new_recipe" title="Go to new recipe page">
                <div class="new_recipe">
                    <div id="new_recipe_icon_container">
                        <img alt ="Create New Recipe" title="Create New Recipe" src="view/images/plus.png"/> 
                    </div>
                    <div>
                        <h3>New Recipe</h3>
                    </div>
                </div>
            </a>
        </div>
    </div>



    <?php foreach ($categories as $category) : ?>
        <div class="recipes_container">
            <div>                
                <h2><?php echo $category['name']; ?></h2>
            </div>

            <?php
            $isCategoryEmpty = TRUE;
            foreach ($allRecipes as $recipe) :
                if ($recipe['category_id'] == $category['category_id']) {
                    $isCategoryEmpty = FALSE;
                    ?>

                    <div class="recipe_container">
                        <div>
                            <div>
                                <figure class="img-bottom-indent"> 
                                    <a href="?action=show_recipe&recipe_id=<?php echo $recipe['recipe_id']; ?>" title="Go to recipe page">                                    
                                        <img alt ="<?php echo $recipe['name']; ?>" title="<?php echo $recipe['name']; ?>" src="<?php echo $recipe['picture_url']; ?>"/>
                                    </a>
                                </figure>
                            </div>                    
                            <div>
                                <a href="?action=show_recipe&recipe_id=<?php echo $recipe['recipe_id']; ?>" title="Go to recipe page">                                    
                                    <p><b><?php echo $recipe['name']; ?></b></p>
                                </a>
                            </div>
                            <div class="recipe_edit_option">
                                <a href="?action=show_edit_recipe&recipe_id=<?php echo $recipe['recipe_id']; ?>" title="Go to edit recipe page">
                                    <img alt ="Edit Recipe Rice" title="Edit Recipe" src="view/images/edit.png"/>
                                </a>
                            </div>
                            <div class="recipe_remove_option">
                                <a href="?action=delete_recipe&recipe_id=<?php echo $recipe['recipe_id']; ?>" title="Delete Recipe">
                                    <img alt ="Delete Recipe" title="Delete Recipe" src="view/images/remove.png"/>
                                </a>
                            </div>
                        </div>
                    </div>    

                    <?php
                }
            endforeach;
            if ($isCategoryEmpty) {
                echo "<p class='empty_category'>No recipe for this category</p>";
            }
            ?>
        </div>   
    <?php endforeach; ?>
</main>
<?php include('footer.php'); ?>