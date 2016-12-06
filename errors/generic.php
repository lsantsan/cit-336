<?php include 'view/header.php'; ?>
<div id="main">
    <h1><?php
        if (isset($errorTitle)) {
            echo $errorTitle;
        } else {
            echo "Error";
        }
        ?></h1>
    <p><?php
        if (isset($error_message)) {
            echo $error_message;
        } else {
            echo "Sorry, an internal error happened. Try again later.";
        }
        ?></p>        
</div><!-- end main -->
<?php include 'view/footer.php'; ?>