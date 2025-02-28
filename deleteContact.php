<?php
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
		// Check if the user/contact combination actually exists.
		$stmt = $conn->prepare("select count(*) as count from Contacts where ID=? AND UserID=?");
		$stmt->bind_param("ii", $ID, $userID);
		if (!$stmt->execute())
            returnWithError($stmt->error);

		$result = $stmt->get_result();
		
		if ($result->fetch_assoc()['count'] != 1) {
			returnWithError("User and contact pair not found.");
			$stmt->close();
			$conn->close();
			return;
		}

		// Delete the contact only if the user actually owns it.
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