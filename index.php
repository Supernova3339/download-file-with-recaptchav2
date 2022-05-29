<?php 

// generate password
 $comb = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
 $pass = array(); 
 $combLen = strlen($comb) - 1; 
 for ($i = 0; $i < 8; $i++) {
     $n = rand(0, $combLen);
     $pass[] = $comb[$n];
 }
 
// add password to file

$zip = new ZipArchive();
    $zip->open("file.zip", ZIPARCHIVE::CREATE);
        $zip->setPassword($pass);  
    for ($a = 0; $a < count($_FILES["files"]["name"]); $a++)
    {
        $content = file_get_contents($_FILES["files"]["tmp_name"][$a]);
        $zip->addFromString($_FILES["files"]["name"][$a], $content);
    }
    	
    $zip->close();
    
    
?>
<html>
    <head>
        <title>Download SuperPad</title>
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        
        <style>
.center {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
}
</style>
        
    </head>
    <body>
    <div class="center">
        <form action="index.php" method="POST">
        <div class="g-recaptcha" data-sitekey="site"></div>
        <button type="submit" class="btn btn-primary btn-lg btn-md" style="width:100%;">Download&nbsp;<i class="fa-solid fa-download"></i></button>
        </form>
    </div>
   </body>
</html>

<?php
if(isset($_POST['g-recaptcha-response'])){
    $secretkey = "secret";
    $ip = $_SERVER['REMOTE_ADDR'];
    $response = $_POST['g-recaptcha-response'];
    $url="https://www.google.com/recaptcha/api/siteverify?secret=$secretkey&response=$response&remoteip=$ip";
    $fire = file_get_contents($url);
    $data = json_decode($fire);
    if($data->success==true){
        // download file code
        header('Location: http://google.com/file.zip');
    }else{
        echo '<script type="text/javascript">';
        echo ' alert("Please Complete Bot Verification")';  //not showing an alert box.
        echo '</script>';

    }
}
else{
    // error
    echo 'Try Again Later';
}
