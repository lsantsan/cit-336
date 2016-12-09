<?php include('header.php'); ?>
<main>
    <h1 id="recipes_title"><?php
        if (isset($isUpdateUser)) {
            echo "Update User";
        } else {
            echo "Sign Up";
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
                <form action="index.php" method="post" id="signup_form">
                    <?php if (isset($isUpdateUser) && $isUpdateUser) { ?>
                        <input type="hidden" name="action" value="update_user">
                        <input type="hidden" name="user_id" value="<?php echo $userId ?>">
                    <?php } else { ?>
                        <input type="hidden" name="action" value="signup">   
                    <?php } ?>   

                    <label>First Name:</label>
                    <input type="text" name="first_name" required <?php
                    if (isset($firstName)) {
                        echo "value='$firstName'";
                    }
                    ?>/>
                    <br>
                    <label>Last Name:</label>
                    <input type="text" name="last_name" required <?php
                    if (isset($lastName)) {
                        echo "value='$lastName'";
                    }
                    ?>/>
                    <br>
                    <label>Email Address:</label>
                    <input type="email" name="email" required <?php
                    if (isset($email)) {
                        echo "value='$email'";
                    }
                    ?>/>
                    <br>
                    <label>Username:</label>
                    <input type="text" name="username" required <?php
                    if (isset($username)) {
                        echo "value='$username'";
                    }
                    ?>/>
                    <br>
                    <label>Password*:</label>
                    <input type="password" name="password" <?php
                    if (isset($password)) {
                        echo "value='$password'";
                    }
                    ?>/>
                    <br>
                     <label>Confirm Password:</label>
                    <input type="password" name="confirm_password" <?php
                    if (isset($confirmPassword)) {
                        echo "value='$confirmPassword'";
                    }
                    ?>/>
                    <br>
                    <label>&nbsp;</label>
                    <input type="submit" name="type" value="<?php
                    if (isset($isUpdateUser)) {
                        echo "Update";
                    } else {
                        echo "Create";
                    }
                    ?>"/>
                    <br>
                </form>
                <p class="observation">* Password has to have at least one uppercase, lowercase, and number character.</p>
            </div>
        </div>    


    </div>

</main>
<?php include('footer.php'); ?>