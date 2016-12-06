<?php include('header.php'); ?>    
<main>
    <h1><?php echo $recipe['name']; ?></h1>

    <div id="main_recipe_container" class="main_container">

        <div id="recipe_picture_container" class="recipe_picture_container">
            <figure class="img-bottom-indent">                                     
                <img alt ="<?php echo $recipe['name']; ?>" title="<?php echo $recipe['name']; ?>" src="<?php echo $recipe['picture_url']; ?>"/>                
            </figure>
        </div>

        <div id="recipe_ingredient_container" class="recipe_ingredient_container">
            <h2>Ingredients</h2>
            <ul>
                <?php foreach ($ingredientList as $ingredientItem) : ?>
                    <li><?php echo $ingredientItem ?></li>
                <?php endforeach; ?>                
            </ul>
            <p><b>Portion: </b><?php echo $recipe['portion']; ?></p>
        </div>

        <div id="recipe_directions_container" class="recipe_directions_container">
            <h2>Directions</h2>
            <ol class="exercises">
                <?php foreach ($directionList as $directionItem) : ?>
                    <li><?php echo $directionItem; ?></li>
                <?php endforeach; ?>  
            </ol>
        </div>


    </div>
</main>
<?php include('footer.php'); ?>