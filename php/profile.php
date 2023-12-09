<?php
require_once 'dir /s autoload.php';
$mongoClient = new MongoDB\Client("mongodb://localhost:27017/");
$database = $mongoClient->selectDatabase('myDB'); 
$collection = $database->selectCollection('customprof');


if ($_SERVER["REQUEST_METHOD"] == "GET") {

    $username = $_GET["username"];
   
    $mongoQuery = array("username" => $username);
    $result = $collection->findOne($mongoQuery);
    
    echo json_encode($result);
   
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents('php://input'), true);
    $dob = $data["dob"];
    $contactnumber = $data["contactnumber"];
    $age = $data["age"]; 
    $username=$data["username"];
    $email=$data["email"];
    $existingDoc = $collection->findOne(['username' => $username]);
    if ($existingDoc) {
       
        $updateResult = $collection->updateOne(
            ['username' => $username],
            [
                '$set' => [
                    'dob' => $dob,
                    'age' => $age,
                    'contact' => $contactnumber
                ]
            ]
        );

        if ($updateResult->getModifiedCount() > 0) {
            $response = array("status" => "success", "message" => "Profile updated successfully!");
        } else {
            $response = array("status" => "error", "message" => "Failed to update profile!");
        }
    } else {
       
        $insertResult = $collection->insertOne([
            'username' => $username,
            'dob' => $dob,
            'age' => $age,
            'contact' => $contactnumber
        ]);
    }

    
    $response = array("status" => "success", "message" => "Profile Saved Successful!!","age"=>$age,"contact"=>$contactnumber,"dob"=>$dob,"username"=>$username,"email"=>$email);
    echo json_encode($response);
}
?>
