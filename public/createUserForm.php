<?php

require_once '../vendor/autoload.php';
require '../config/settings.php';

// https://weichie.com/blog/curl-api-calls-with-php/
function CallAPI($method, $url, $data = false)
{
    $curl = curl_init();

    switch ($method) {
        case 'POST':
            curl_setopt($curl, CURLOPT_POST, 1);
            if ($data) {
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            }

            break;
        case 'PUT':
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
            if ($data) {
                curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
            }

            break;
        case 'DELETE':
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');

            break;
        default:
            if ($data) {
                $url = sprintf('%s?%s', $url, http_build_query($data));
            }
    }

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    if (isset($_COOKIE['Authorization'])) {
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Authorization: Bearer '.$_COOKIE['Authorization']]);
    }
    $result = curl_exec($curl);

    if (!$result) {
        die('Connection Failure');
    }

    curl_close($curl);

    return $result;
}

$n = 0; // $val counter
$url = $settings['api']['url'];
$id = isset($_POST['id']) ? $_POST['id'] : ''; // post if from form field
if ('' == $id) {
    $id = isset($_GET['id']) ? $_GET['id'] : ''; // get if from form action
}
$action = isset($_GET['action']) ? $_GET['action'] : '';
$val = ['', '', '', '', '', ''];
//$val = ['john.doe', '1234567', 'John', 'Doe', 'john@doe.com', 'users, customers']

// 'Create', 'POST','/users', $_POST, 'Error', 'Ok - id #','Create' // retireve id
// 'Update', 'PUT', '/users/id/{$id}', $_POST, 'Error', 'Ok - id #', 'Create'
// 'Delete', 'DELETE', '/users/id/{$id}', '', 'Error', 'Ok - id #', 'Create'
// 'Read', 'GET', '/users/id/{$id}', '', 'Error', 'Ok - id #', 'Create'

if ('Create' == $action) {
    $endpoint = '/users';
    $response = json_decode(CallAPI('POST', $url.$endpoint, $_POST));
    if (isset($response->message)) {
        $msg = 'Create Error '.$response->message;
    } else {
        $msg = 'Create Ok - id '.$response->user_id;
        $id = $response->user_id;
    }
    $action = 'Create'; // Next action
}
if ('Update' == $action && is_numeric($id)) {
    $endpoint = "/users/id/{$id}";
    $response = json_decode(CallAPI('PUT', $url.$endpoint, $_POST));
    if (isset($response->message)) {
        $msg = 'Update Error '.$response->message;
    } else {
        $msg = 'Update Ok - id '.$response->user_id;
    }
    $action = 'Create'; // Next action
}
if ('Delete' == $action && is_numeric($id)) {
    $endpoint = "/users/id/{$id}";
    $response = json_decode(CallAPI('DELETE', $url.$endpoint));
    if (isset($response->message)) {
        $msg = 'Delete Error '.$response->message;
    } else {
        $msg = 'Delete Ok - id '.$response->user_id;
    }
    $action = 'Create'; // Next action
}
if ('Read' == $action && is_numeric($id)) {
    $endpoint = "/users/id/{$id}";
    $response = json_decode(CallAPI('GET', $url.$endpoint));
    if (!empty($response)) {
        if (isset($response->message) || 'Unauthorized' == $response) {
            $msg = 'Read error '.(isset($response->message) ? $response->message : $response);
            $action = 'Create'; // Next action
        } else {
            unset($val);
            foreach ($response as $key => $value) {
                $val[] = $value;
            }
            array_shift($val);
            $msg = 'Read Ok';
            $action = 'Update'; // Next action
        }
    }
}
if (!in_array($action, ['Create', 'Read', 'Update', 'Delete']) || !is_numeric($id)) {
    $endpoint = '/users';
    $msg = 'Select action Create (Post), Read (Get), Update (Put) or Delete';
    $action = 'Create';
}

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

    <h1>Slim4API - CRUD endpoint /users</h1>

    <form class="form" method="post" action="createUserForm.php?action=<?php echo $action."&amp;id={$id}"; ?>"
        onsubmit="return verifForm([username,password,first_name,last_name,email,profile],'Error : fields marked with an asterisk are mandatory!') && validateEmail(email,'Error : email format invalid!');">

        <p>
            <label for="username">* Username:</label><br />
            <input type="text" class="form" name="username" placeholder="Username" value="<?php echo $val[$n]; ++$n; ?>"/><br />

            <label for="password">* Password:</label><br />
            <input type="password" class="form" name="password" placeholder="Password" value="<?php echo $val[$n]; ++$n; ?>"/><br />

            <label for="first_name">* First name:</label><br />
            <input type="text" class="form" name="first_name" placeholder="First name" value="<?php echo $val[$n]; ++$n; ?>"/><br />

            <label for="last_name">* Last name:</label><br />
            <input type="text" class="form" name="last_name" placeholder="Last name" value="<?php echo $val[$n]; ++$n; ?>"/><br />

            <label for="email">* Email:</label><br />
            <input type="email" class="form" name="email" placeholder="email.me@domain.com" value="<?php echo $val[$n]; ++$n; ?>"/><br />

            <label for="profile">* Profile:</label><br />
            <input type="text" class="form" name="profile" placeholder="customers users" value="<?php echo $val[$n]; ++$n; ?>"/><br />

            <br />

            <button><?php echo $action; ?></button>
            <button><a href="createUserForm.php?action=Cancel">Cancel</a></button>

            <?php
                if ('' != $id) {
                    echo "<button><a onclick=\"return confirm('Delete user #{$id}');\" href=\"createUserForm.php?action=Delete&amp;id={$id}\">Delete</a></button>";
                }
            ?>
        </p>
    </form>

    <form method="post" class="form" action="createUserForm.php?action=Read"
            onsubmit="return verifForm([id],'Error : fields marked with an asterisk are mandatory!');">

        <p>
            <label for="id">* Id:</label><br />
            <input type="text" class="form" name="id" placeholder="Id" value="<?php echo $id; ?>"/><br />

            <br />
        
            <button>Read</button>

        </p>
    </form>

    <?php
        echo '' != $msg ? "Info : {$msg}" : '';
        if (!isset($_COOKIE['Authorization'])) {
            echo '<br /><font color="red">Error : unauthorized - no JWT found</font>';
        }
    ?>

    </div>
    </div>
  </body>
</html>