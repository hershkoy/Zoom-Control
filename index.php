<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

<? 

if (getenv("APP_CLIENT") !== false){
    $client=getenv('APP_CLIENT');
    $secret=getenv('APP_SECRET');
    
}else{
    $configs = include('config.php');
    $client=$configs['APP_CLIENT'];
    $secret=$configs['APP_SECRET'];
}

$auth_64 = base64_encode("$client:$secret");
$fullURL = 'https://'. $_SERVER['HTTP_HOST'] .parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

//echo "client:$client";

?>

<? if (!(isset($_GET['code']))) {?>
    <a href="https://zoom.us/oauth/authorize?response_type=code&client_id=<?=$client?>&redirect_uri=<?=$fullURL?>">
        <button>Zoom Control Authorize</button>
    </a>

<? }else{ ?>
<?

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