<?

ini_set("session.cookie_domain", $_SERVER['SERVER_NAME']);
session_set_cookie_params(0, '/', $_SERVER['SERVER_NAME']);
if(!isset($_SESSION)) {
	session_start();
}

//echo "P:".print_r($_POST,true);


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

<? 

/*f (getenv("APP_CLIENT") !== false){
    $client=getenv('APP_CLIENT');
    $secret=getenv('APP_SECRET');
    
}else{
    $configs = include('config.php');
    $client=$configs['APP_CLIENT'];
    $secret=$configs['APP_SECRET'];
}*/

$fullURL = 'https://'. $_SERVER['HTTP_HOST'] .parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

//echo "client:$client";

?>

<? if ((isset($_POST['client']))) {
    $client=$_POST['client'];
    $secret=$_POST['secret'];
    $auth_64 = base64_encode("$client:$secret");
    $_SESSION['auth_64']=$auth_64;

    header("Location: https://zoom.us/oauth/authorize?response_type=code&client_id=$client&redirect_uri=$fullURL");
    exit();

 }else if (!(isset($_GET['code']))) { ?>
    
    <form action="" method="post">
        <div>client: <input type="text" name="client" id="client"></div>
        <div>secret: <input type="text" name="secret" id="secret"></div>
        <div><input type="submit" value="GO"></div>
    </form>
<?}else{ ?>
<?

$auth_64 = $_SESSION['auth_64'];
$code = $_GET['code'];
$req_url = "https://zoom.us/oauth/token?grant_type=authorization_code&code=$code&redirect_uri=$fullURL";

$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => $req_url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => "",
  CURLOPT_HTTPHEADER => array(
    "Authorization: Basic $auth_64", 
    "cache-control: no-cache"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
    echo "cURL Error #:" . $err;
} else {
    //echo "req_url:$req_url<br>";
    //echo $response;

    $res_ar = json_decode($response, true);
    ?>access token: <div><?=$res_ar['access_token']?></div><?
    ?>refresh token: <div><?=$res_ar['refresh_token']?></div><?
    
}


?>
<? }?>

</body>
</html>