<?php
	$inData = getRequestInfo();
	
	$searchResults = "";
	$searchCount = 0;

	$conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "Project1DB");
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
		// Check if the user actually exists.
		$stmt = $conn->prepare("select count(*) as count from Users where ID=?");
		$stmt->bind_param("i", $inData["userID"]);
		if (!$stmt->execute())
            returnWithError($stmt->error);

		$result = $stmt->get_result();
		
		if ($result->fetch_assoc()['count'] != 1) {
			returnWithError("User not found.");
			$stmt->close();
			$conn->close();
			return;
		}

		// Search for contacts.
		$stmt = $conn->prepare("select ID, FirstName, LastName, Email, Phone from Contacts where 
		(FirstName like ? or LastName like ?) and UserID=?");
		$targetName = "%" . $inData["search"] . "%";
		$stmt->bind_param("ssi", $targetName, $targetName, $inData["userID"]);

		if (!$stmt->execute())
            returnWithError($stmt->error);
		
		$result = $stmt->get_result();
		
		while($row = $result->fetch_assoc())
		{
			if( $searchCount > 0 )
			{
				$searchResults .= ",";
			}
			$searchCount++;
			$searchResults .= '{"ID": "' . $row["ID"] . '",';
			$searchResults .= '"firstName": "' . $row["FirstName"] . '",';
			$searchResults .= '"lastName": "' . $row["LastName"] . '",';
			$searchResults .= '"email": "' . $row["Email"] . '",';
			$searchResults .= '"phone": "' . $row["Phone"] . '"}';
		}
		
		if( $searchCount == 0 )
		{
			returnWithError( "No records found." );
		}
		else
		{
			returnWithInfo( $searchResults );
		}
		
		$stmt->close();
		$conn->close();
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
		$retValue = '{"id":0,"firstName":"","lastName":"","error":"' . $err . '"}';
		sendResultInfoAsJson( $retValue );
	}
	
	function returnWithInfo( $searchResults )
	{
		$retValue = '{"results":[' . $searchResults . '],"error":""}';
		sendResultInfoAsJson( $retValue );
	}
	
?>