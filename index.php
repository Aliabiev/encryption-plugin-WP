<?php

/*
Plugin Name: and_plugin_encryption
Description: Плагин для шифрования и дешифрования текстовых данных.
Author: Andrii Aliabiev
*/

$pub = <<<SOMEDATA777
-----BEGIN PUBLIC KEY-----
MFwwDQYJKoZIhvcNAQEBBQADSwAwSAJBAM4/qJTXAq0YLBOxN9zPVbsEpo4Kulsr
X9UaXSNGyJa3J2u98uyV1b8/5F8S7BIzW4tRmc+abyFcvb+MkkiwgP0CAwEAAQ==
-----END PUBLIC KEY-----
SOMEDATA777;

$key = <<<SOMEDATA777
-----BEGIN RSA PRIVATE KEY-----
MIIBOwIBAAJBAM4/qJTXAq0YLBOxN9zPVbsEpo4KulsrX9UaXSNGyJa3J2u98uyV
1b8/5F8S7BIzW4tRmc+abyFcvb+MkkiwgP0CAwEAAQJANgDlG1PRF5GkuONGRULk
p7toAPk+InEQ/rOQf5QhIZULHNUqsYqUaTAnprPrXpYxUh7zJPyJIkXEUcoutYUG
AQIhAOk9WVI6I8kPb2/FuufLI1sQ0yL+mAB60piTrkzgImaRAiEA4mAJkqIittHh
Js005c5/l85kIRD8A/jJTQo8Ehfwoa0CIQDlCHREtYjccAbKqE0QPr76NrxOOdlD
Z1iTsTlQjivZsQIgQYyvmO8sACY7/QFUvOqTlcCky9JgN0I2AAHjrRWTjy0CIQCo
0pviBH8kbAxR1ZdYKlhkgbFP/PTe4D9YAO+18PUMfg==
-----END RSA PRIVATE KEY-----
SOMEDATA777;

add_filter('the_content', 'and_function2');
function and_function2($content)
{
	global $pub;
	global $key;
	?>

	<h2>Введите текст для шифрования:</h2><p>
	<form  method="POST">
		<input type="text" name="text"><br><br>
		<button>encode</button>
	</form><br><br>

	<?php 
    if( isset($_POST['text']) )
    {
        $host = 'localhost';
		$user = 'root';
		$pswd = '';
		$db = 'code';
		$pdo = new PDO("mysql:host={$host}; dbname={$db}; charset=utf8", $user, $pswd);

	    $text = $_POST['text'];
	    $pk  = openssl_get_publickey($pub);
	    openssl_public_encrypt($text, $encrypted, $pk);
	    $code = chunk_split(base64_encode($encrypted));
	    $sql = "INSERT INTO code (encode) VALUES (?)";
	    $pz = $pdo->prepare($sql); 
	    $pz->execute([$code]);  

	    echo 'Шифр:   <br><h3>'.$code.'</h3>';
    }
    ?>
    <br> <br> 
	<h2>Введите результат шифрования:</h2><p>
	<form  method="POST">
		<input type="text" name="data"><br><br>
		<button>decode</button>
	</form>

    <?php
    if( isset($_POST['data']) )
	{
	    $data = $_POST['data'];
	    $pk  = openssl_get_privatekey($key);
	    openssl_private_decrypt(base64_decode($data), $out, $pk);
	    echo 'Расшифрованный текст: <br><h3>'.$out.'</h3>';
	}



}
