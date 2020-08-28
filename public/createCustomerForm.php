<!DOCTYPE html>
<html>
  <head>
	<title>Slim4API</title>
	<link href="favicon.ico" rel="icon" />
    <link href="css/slim4api.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="js/veriform.js"></script>
  </head>
  <body>
    <div class="flex-container">
    <div class="container">

	<h1>Slim4API - Customer Create</h1>

	<form method="post" class="form" action="/customers"
		onsubmit="if(verifForm([cusname,address,city,email],'Error : fields marked with an asterisk are mandatory!'))
			return validateEmail(email,'Error : email format invalid!');
			return false;">

		<p>
			<label for="cusname">* Name:</label><br />
			<input type="text" class="form" name="cusname" placeholder="Name" /><br />

			<label for="address">* Address:</label><br />
			<input type="text" class="form" name="address" placeholder="Address" /><br />

			<label for="city">* City:</label><br />
			<input type="text" class="form" name="city" placeholder="City" /><br />

			<label for="phone">Phone:</label><br />
			<input type="text" class="form" name="phone" placeholder="phone" /><br />

			<label for="email">* Email:</label><br />
			<input type="email" class="form" name="email" placeholder="user1@domain.com" /><br />

			<br />

			<input name="save" type="submit" value="save">
		</p>
	</form>
	</div>
	</div>
  </body>
</html>