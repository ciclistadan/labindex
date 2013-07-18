<?php session_start(); 
header('Content-type: text/json');

$token = $_POST['token'];
require '../../secret/janrain_apikey.php';
require_once '../../secret/database2.php';


    //call the janrain authentication server and send them the user's token and the application's secret api key
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, 'https://rpxnow.com/api/v2/auth_info');
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS,
  array('token' =>  $token,
    'apiKey' => $apiKey));
curl_setopt($curl, CURLOPT_FAILONERROR, true);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($curl);

if (!$response){
  echo '{"stat": "bad response from janrain"';
  echo '{"Curl error": "' . curl_error($curl). '",';
  echo '"HTTP code": "' . curl_errno($curl) . '"}';
} else {
      //parse the response and set some session variables before returning the ajax response

      //example response from google
      // {
      //   "stat": "ok",
      //   "profile": {
      //     "providerName": "Google",
      //     "identifier": "https://www.google.com/profiles/115385328358587715642",
      //     "verifiedEmail": "dkrspam@gmail.com",
      //     "preferredUsername": "dkrspam",
      //     "displayName": "Dan Rozelle",
      //     "name": {
      //       "formatted": "Dan Rozelle",
      //       "givenName": "Dan",
      //       "familyName": "Rozelle"
      //     },
      //     "email": "dkrspam@gmail.com",
      //     "url": "https://www.google.com/profiles/115385328358587715642",
      //     "googleUserId": "115385328358587715642",
      //     "providerSpecifier": "google"
      //   }
      // }
  $obj = json_decode($response, true);
  
  if($obj['profile']['providerName']==="Google"){
          //verify that a valid user email has been authenticated
    $email = mysqli_real_escape_string($link, $obj['profile']['verifiedEmail']);

    $query = "SELECT * FROM people WHERE p_email='".$email."'";
    $result = mysqli_query($link, $query);
    if (mysqli_affected_rows($link)==1) {
      
            // /////////////////////////////////////////////////
            //you have been verified
      $row = mysqli_fetch_assoc($result);
      $_SESSION['userid'] = $row['p_userid'];
      $_SESSION['name'] = $obj['profile']['name']['givenName'];
      echo $response;

            //TODO store user information in our database for use on the lab website
            //TODO add linked data mining
    }
    else{
          //not a valid user, request addition to validation table
      echo '{"stat": "you are not a valid user, ask Dan to add you to the list"}';
      
    }

  }
  else{
          // received an alternative response
    echo '{"stat": "please try logging in with Google, other log-in options are planned"}';
  }

}
curl_close($curl);
?>
