<!DOCTYPE html>
<html>
  <head>
    <title>Slim4API</title>
	<link href="css/favicon.ico" rel="icon" />
      <link href="css/slim4api.css" rel="stylesheet" type="text/css"/>
      <script type="text/javascript" src="js/veriform.js"></script>
  </head>
  <body>
  <div class="flex-container">
      <div class="container">

          <h1>Slim4API - Login</h1>

          <form method="post" action="/tokens"
                onsubmit="return verifForm(['username','password'],'Erreur : les champs marqués d\'une astérisque sont obligatoires !');">

              <p>

                  <!-- forced login user/secret to be replaced with actual credentials cehck from database -->

                  <label for="username">* Username:</label><br>
                  <input type="text" class="form" name="username" placeholder="userid"/><br/>

                  <label for="password">* Password:</label><br>
                  <input type="password" class="form" name="password" placeholder="password"/><br/>

                  <br/>

                  <button>Login</button>
                  <button onclick="username.value='user';password.value='secret';">Autologin</button>
                  <button><a href="/logout">Logout</button>
                  <button><a href="/checkJWTForm.php">Check JWT</button>
                  <button><a href="/hashPWDForm.php">Hash PWD</button>

                  <br/>

              </p>
          </form>
      </div>
  </div>
  </body>
</html>