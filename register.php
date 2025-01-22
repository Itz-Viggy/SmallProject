<?php
// Enable detailed error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$inData = getRequestInfo();

$login = $inData["login"];
$password = $inData["password"];
$firstName = $inData["firstName"];
$lastName = $inData["lastName"];

// Database connection
$conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "Project1DB");
if ($conn->connect_error) 
{
    returnWithError($conn->connect_error);
} 
else
{
    // Prepare and execute the SQL statement with first name and last name
    $stmt = $conn->prepare("INSERT INTO Users (FirstName,LastName, Login, Password  ) VALUES(?, ?, ?, ?)");
    if (!$stmt) {
        returnWithError($conn->error);
    } else {
        $stmt->bind_param("ssss", $firstName, $lastName, $login, $password);
        if (!$stmt->execute()) {
            returnWithError($stmt->error);
        } else {
            $stmt->close();
            $conn->close();
            returnWithError(""); // No error, success response
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
