<?php include('header.php'); ?>

<main>
    <h1 id="recipes_title">Log In</h1>

    <div class="recipes_container">

        <div id="login_container">
            <?php            
            if (isset($error_message)) {
                echo "<p class='error_message'>$error_message</p>";
            }
            ?>
            <div class="form_container">
                <form action="index.php" method="post" id="login_form" >
                    <input type="hidden" name="action" value="login_user">                                
                    <label>Username:</label>
                    <input type="text" name="username" required/>
                    <br>
                    <label>Password:</label>
                    <input type="password" name="password" required />
                    <br>
                    <label>&nbsp;</label>
                    <input type="submit" value="Login" />
                    <br>
                </form>
            </div>
        </div>    


    </div>

</main>
<?php include('footer.php'); ?>