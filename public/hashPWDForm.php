<?php

    $password = (isset($_POST['password'])) ? $_POST['password'] : '';

?>
<!DOCTYPE html>
<html>
  <head>
    <title>Slim4API</title>
    <link href="css/favicon.ico" rel="icon" />
    <link href="css/slim4api.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="js/veriform.js"></script>
  </head>
  <body>
    <div class="flex-container">
    <div class="container">

    <h1>Slim4API - PHP/BCRYPT password hash</h1>
    <small>Not advised to run MySQL ENCRYPT() that relies on OS crypt() and could leave info in MySQL logfiles</small><br /><br />
    
    <form method="post" action="/hashPWDForm.php" onsubmit="return verifForm([password],'Error : fields marked with an asterisk are mandatory!');">

        <p>
            <label for="password">* Password:</label><br />
            <input name="password" class="form" style="width:800px" size="100" value="<?php echo $password; ?>" placeholder="Password" autocorrect="off" autocapitalize="off" spellcheck="false" wrap="off">
            <br />
            
            <label for="passwordh">Password hash:</label><br />
            <input name="passwordh" class="form" style="width:800px" size="100" value="
            <?php
                if ('' != $password) {
                    echo password_hash(htmlspecialchars(strip_tags($password)), PASSWORD_BCRYPT);
                }
            ?>
            " placeholder="Password" autocorrect="off" autocapitalize="off" spellcheck="false" wrap="off">            
            <br />
            
            <br />
            <button>Hash</button>

        </p>
    </form>
    </div>
    </div>
  </body>
</html>