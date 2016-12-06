<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Recipe App | MyBrazilianRecipes.com</title>   
        <meta name="author" content="Lucas Santana">
        <meta name="description" content="This page presents different categories of Brazilian recipes to faciliate navigation.">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <link rel="stylesheet" href="/recipe_app/view/css/screen.css" type="text/css" media="screen">
    </head>
    <body><div>
            <header>                

                <div id="logo">
                    <div>
                        <a href="/" title="Go to home page">
                            <img alt ="Logo" title="Logo Picture" src="/recipe_app/view/images/logo.jpg"/> 
                        </a>
                    </div>

                    <h1><a href="/" title="Go to home page">MyBrazilianRecipes.com</a></h1>



                </div>
                <nav class="navigation_menu"> 
                    <div>
                        <ul id="navlist">  
                           <?php if (isset($activeUser[0])) { ?>
                                 <li><a href="?action=show_home&user_id=<?php echo $activeUser[0];?>" title="Go to recipes page">Home</a></li>   
                            <?php } else { ?>
                                <li><a href="?action=show_login" title="Go to recipes page">Home</a></li>   
                            <?php } ?>                                                        
                            <li><a href="?action=show_signup" title="Go to brazilian restaurant page">Sign Up</a></li>
                            <?php if (isset($activeUser[0])) { ?>
                                <li><a href="?action=logout" title="Logout user">Log Out</a></li> 
                            <?php } else { ?>
                                <li><a href="?action=show_login" title="Go to login page">Log In</a></li>   
                            <?php } ?>
                        </ul>
                    </div>
                    <div id="menu_button_div">
                        <a href="#navlist" title="Jump to Navigation"><hr><hr><hr></a>
                    </div>


                </nav>
            </header>  













