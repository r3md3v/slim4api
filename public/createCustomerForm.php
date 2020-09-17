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
$val = ['', '', '', '', ''];
//$val = ['John Doe', 'Doe Street', 'Metropolis', '+1 234 567 899', 'john@doe.com']

if ('Create' == $action) {
    $endpoint = '/customers';
    $response = json_decode(CallAPI('POST', $url.$endpoint, $_POST));
    if (isset($response->message)) {
        $msg = 'Create Error '.$response->message;
    } else {
        $msg = 'Create Ok - id '.$response->customer_id;
        $id = $response->customer_id;
    }
    $action = 'Create'; // Next action
}
if ('Update' == $action && is_numeric($id)) {
    $endpoint = "/customers/id/{$id}";
    $response = json_decode(CallAPI('PUT', $url.$endpoint, $_POST));
    if (isset($response->message)) {
        $msg = 'Update Error '.$response->message;
    } else {
        $msg = 'Update Ok - id '.$response->customer_id;
    }
    $action = 'Create'; // Next action
}
if ('Delete' == $action && is_numeric($id)) {
    $endpoint = "/customers/id/{$id}";
    $response = json_decode(CallAPI('DELETE', $url.$endpoint));
    if (isset($response->message)) {
        $msg = 'Delete Error '.$response->message;
    } else {
        $msg = 'Delete Ok - id '.$response->customer_id;
    }
    $action = 'Create'; // Next action
}
if ('Read' == $action && is_numeric($id)) {
    $endpoint = "/customers/id/{$id}";
    $response = json_decode(CallAPI('GET', $url.$endpoint));
    if (!empty($response)) {
        if (isset($response->message)) {
            $msg = 'Read error '.$response->message;
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
if ('' == $action || 'Cancel' == $action) {
    $endpoint = '/customers';
    $msg = 'Select CRUD action Create/Post Read/Get Update/Post Delete';
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

    <h1>Slim4API - Customer API CRUD</h1>

    <form method="post" class="form" action="createCustomerForm.php?action=<?php echo $action."&amp;id={$id}"; ?>"
        onsubmit="return verifForm([cusname,address,city,email],'Error : fields marked with an asterisk are mandatory!') && validateEmail(email,'Error : email format invalid!');">

        <p>
            <label for="cusname">* Name:</label><br />
            <input type="text" class="form" name="cusname" placeholder="Name" value="<?php echo $val[$n]; ++$n; ?>"/><br />

            <label for="address">* Address:</label><br />
            <input type="text" class="form" name="address" placeholder="Address" value="<?php echo $val[$n]; ++$n; ?>"/><br />

            <label for="city">* City:</label><br />
            <input type="text" class="form" name="city" placeholder="City" value="<?php echo $val[$n]; ++$n; ?>"/><br />

            <label for="phone">Phone:</label><br />
            <input type="text" class="form" name="phone" placeholder="Phone number" value="<?php echo $val[$n]; ++$n; ?>" /><br />

            <label for="email">* Email:</label><br />
            <input type="email" class="form" name="email" placeholder="email.me@domain.com" value="<?php echo $val[$n]; ++$n; ?>" /><br />

            <br />
        
            <button><?php echo $action; ?></button>
            <button><a href="createCustomerForm.php?action=Cancel">Cancel</a></button>
            
            <?php
                if ('' != $id) {
                    echo "<button><a onclick=\"return confirm('Delete customer #{$id}');\" href=\"createCustomerForm.php?action=Delete&amp;id={$id}\">Delete</a></button>";
                }
            ?>
        </p>
    </form>

    <form method="post" class="form" action="createCustomerForm.php?action=Read"
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
    ?>

    </div>
    </div>
  </body>
</html>