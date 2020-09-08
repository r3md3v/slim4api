<?php

    $jwtex1 = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzY290Y2guaW8iLCJleHAiOjEzMDA4MTkzODAsIm5hbWUiOiJDaHJpcyBTZXZpbGxlamEiLCJhZG1pbiI6dHJ1ZX0.03f329983b86f7d9a9f5fef85305880101d5e302afafa20154d094b229f75773';
    $jwt = (isset($_POST['jwt'])) ? $_POST['jwt'] : $jwtex1;

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

    <h1>Slim4API - Check JWT</h1>

    <form method="post" action="/checkJWTForm.php" 
        onsubmit="return verifForm([jwt],'Error : fields marked with an asterisk are mandatory!'); return false;">

        <p>
            <label for="jwt">* JWT/JSON Web Token:</label><br />
            <input name="jwt" class="form" style="width:800px" size="100" value="<?php echo $jwt; ?>" placeholder="Token" style="bottom: -1em; padding: 0px; outline: currentcolor none medium;" autocorrect="off" autocapitalize="off" spellcheck="false" wrap="off">
            <br />

            <label for="jwt">Token details:</label><br />
            <textarea class="none" rows="20" cols="100" name="jwtd" placeholder="Token" style="bottom: -1em; padding: 0px; outline: currentcolor none medium;" autocorrect="off" autocapitalize="off" spellcheck="false" wrap="off">

            <?php
                if ('' != $jwt) {
                    $splitJwt = explode('.', $jwt);
                    if (3 != count($splitJwt)) {
                        echo '** Alert : The JWT string must have two dots **'."\n";
                    }
                    foreach ($splitJwt as $key => $chunk) {
                        if ($remainder = strlen($chunk) % 4) {
                            $chunk .= str_repeat('=', 4 - $remainder);
                        }
                        $decoded = base64_decode(strtr($chunk, '-_', '+/'));
                        $data = json_decode($decoded);
                        if (JSON_ERROR_NONE != json_last_error()) {
                            echo "\nError in json part {$key}";
                        } else {
                            echo print_r($data);
                        }
                    }
                }
            ?>
            
            </textarea>
            <br />
            
            <br />
            <button>Check</button>
            <button><a target="jwtio" href="https://jwt.io/?value=<?php echo $jwt; ?>">Check from jwt.io</a></button>
            <button onclick="javascript:var d = new Date(); var l = 4; d.setTime(d.getTime() + (l * 60 * 60 *  60)); var expires = 'expires='+d.toUTCString();document.cookie = 'Authorization=<?php echo $jwt; ?>; Max-Age='+expires+'; HttpOnly; Secure; SameSite=Strict; path=/;'; alert('Cookie created for '+l+' hours');">Forge Cookie</button>
            <button onclick="javascript:document.cookie = 'Authorization=;expires=Thu, 01 Jan 1970 00:00:00 UTC; HttpOnly; Secure; SameSite=Strict; path=/;'">Delete Cookie</button>
        
        </p>
    </form>
    </div>
    </div>
  </body>
</html>