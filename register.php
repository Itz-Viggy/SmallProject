<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$inData = getRequestInfo();

$login = $inData["login"];
$password = $inData["password"];
$firstName = $inData["firstName"];
$lastName = $inData["lastName"];

$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

// Database connection
$conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "Project1DB");
if ($conn->connect_error) 
{
    returnWithError($conn->connect_error);
} 
else
{
    // Check if the login already exists
    $checkStmt = $conn->prepare("SELECT Login FROM Users WHERE Login = ?");
    $checkStmt->bind_param("s", $login);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows > 0) {
        $checkStmt->close();
        $conn->close();
        returnWithError("Login already exists");
        exit();
    }
    $checkStmt->close();

    // Insert the new user
    $stmt = $conn->prepare("INSERT INTO Users (FirstName, LastName, Login, Password) VALUES(?, ?, ?, ?)");
    if (!$stmt) {
        returnWithError($conn->error);
    } else
    {
        $stmt->bind_param("ssss", $firstName, $lastName, $login, $hashedPassword);
        if (!$stmt->execute()) {
            if ($conn->errno == 1062) { 
                returnWithError("Login already exists");
            } else {
                returnWithError($stmt->error);
            }
        } else {
            $stmt->close();
            $conn->close();
            returnWithError(""); 
        }
    }
    
}

// Function to get JSON input
function getRequestInfo()
{
    $inputData = file_get_contents('php://input');
    if ($inputData) {
        return json_decode($inputData, true);
    } else {
        returnWithError("No input received");
        exit();
    }
}

// Function to send JSON response
function sendResultInfoAsJson($obj)
{
    header('Content-type: application/json');
    echo $obj;
}

// Function to return error messages
function returnWithError($err)
{
    $retValue = '{"error":"' . $err . '"}';
    sendResultInfoAsJson($retValue);
}
?>
