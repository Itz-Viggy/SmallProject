<?php
	$inData = getRequestInfo();
	
	$firstName = $inData["firstName"];
	$lastName = $inData["lastName"];
	$email = $inData["email"];
	$phone = $inData["phone"];
	$userID = $inData["userID"];

	$conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "Project1DB");
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
		// Check if the user actually exists.
		$stmt = $conn->prepare("select count(*) as count from Users where ID=?");
		$stmt->bind_param("i", $userID);
		if (!$stmt->execute())
            returnWithError($stmt->error);

		$result = $stmt->get_result();
		
		if ($result->fetch_assoc()['count'] != 1) {
			returnWithError("User not found.");
			$stmt->close();
			$conn->close();
			return;
		}

        $stmt = $conn->prepare("INSERT into Contacts (FirstName, LastName, Email, Phone, UserID) VALUES(?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $firstName, $lastName, $email, $phone, $userID);
		if (!$stmt->execute()) {
            returnWithError($stmt->error);
        } else {
            $stmt->close();
            $conn->close();
            returnWithError(""); // No error, success response
        }
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