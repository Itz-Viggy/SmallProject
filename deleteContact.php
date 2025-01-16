<?php
	// Enable detailed error reporting for debugging
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	$inData = getRequestInfo();
	
	$ID = $inData["ID"];
	$userID = $inData["userID"];

	$conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "Project1DB");
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
		//Delete the contact only if the user actually owns it.
        $stmt = $conn->prepare("delete from Contacts where ID=? AND UserID=?");
        $stmt->bind_param("ii", $ID, $userID);
		if (!$stmt->execute())
            returnWithError($stmt->error);

        $stmt->close();
        $conn->close();
        returnWithError(""); // No error, success response
	}


function getRequestInfo()
{
	return json_decode(file_get_contents('php://input'), true);
}

function sendResultInfoAsJson( $obj )
{
	header('Content-type: application/json');
	echo $obj;
}

function returnWithError( $err )
{
	$retValue = '{"error":"' . $err . '"}';
	sendResultInfoAsJson( $retValue );
}
?>