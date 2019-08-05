<?php
include_once  "utils/Caterpillar.php";
$cat = new Caterpillar();
$word = "Message to encrypt";
$encrypt = $cat->encrypt($word);
$encrypt = $encrypt;
$decrypt = $cat->decrypt($encrypt);
$array = array("word"=> $word,"encrypt"=>$encrypt, "decrypt"=> $decrypt);
//var_dump($array);

$result = json_encode($array);
echo $result;
?>