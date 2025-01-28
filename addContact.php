<?php
	$inData = getRequestInfo();
	
	$firstName = $inData["firstName"];
	$lastName = $inData["lastName"];
	$email = $inData["email"];
	$phone = $inData["phone"];
	$userID = $inData["userID"];

	// This technically isn't the true pattern for email addresses, but it is more than enough.
	$emailPattern = "~[\.!#$%&'*+\-\/=?^_`{|}\~0-9A-z]{1,64}@[A-z]{1,63}(\.[A-z]{1,63})+~";
	$phonePattern = "~\([0-9]{3}\) [0-9]{3}-[0-9]{4}~";
	$canadaPattern = "~[0-9]{3}-[0-9]{3}-[0-9]{4}~";
	$basicPattern = "~[0-9]{10}~";

	// Check if any fields are empty.
	if ($firstName == "" || $lastName == "" || $email == "" ||  $phone == "") {
		returnWithError("A contact field was not filled in.");
		return;
	}

	// Check if the email is in the proper format.
	if (preg_match($emailPattern, $email) != 1) {
		returnWithError("The email was not in the correct format.");
		return;
	}

	// Check if the phone is in the proper format. If it is in a close format, convert it to the proper one.
	if (preg_match($phonePattern, $phone) != 1) {
		if (preg_match($canadaPattern, $phone) == 1)
			$phone = "(" . substr($phone, 0, 3) . ") " . substr($phone, 4);
		else if (preg_match($basicPattern, $phone) == 1)
			$phone = "(" . substr($phone, 0, 3) . ") " . substr($phone, 3, 3) . "-" . substr($phone, 6);
		else {
			returnWithError("The phone number was not in a recognized format.");
			return;
		}
	}

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