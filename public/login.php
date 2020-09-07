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

	<h1>Slim4API - Login</h1>

	<form method="post" action="/tokens" onsubmit="return verifForm([username,password],'Error : fields marked with an asterisk are mandatory!');">

		<p>

			<!-- forced login user/secret to be replaced with actual credentials cehck from database -->

			<label for="username">* Username or Email:</label><br>
			<input type="text" class="form" name="username" placeholder="userid or email" /><br/>

			<label for="password">* Password:</label><br>
			<input type="password" class="form" name="password" placeholder="password" /><br />

			<br />

			<button>Login</button>
            <button onclick="username.value='user';password.value='secret';">Autologin User</button>
            <button onclick="username.value='miked';password.value='password';">Autologin MikeD</button>
			<button><a href="/logout">Logout</a></button>
			<button><a href="/checkJWTForm.php">Check JWT</a></button>
			<button><a href="/hashPWDForm.php">Hash PWD</a></button>

			<br />

		</p>
	</form>
	</div>
	</div>
  </body>
</html>