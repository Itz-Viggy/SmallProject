const urlBase = 'http://scuba2havefun.xyz/LAMPAPI';
const extension = 'php';

let userId = 0;
let firstName = "";
let lastName = "";


function saveCookie()
{
	let minutes = 30;
	let date = new Date();
	date.setTime(date.getTime()+(minutes*60*1000));	
	document.cookie = "firstName=" + firstName + ",lastName=" + lastName + ",userId=" + userId + ";expires=" + date.toGMTString();
}

function readCookie()
{
	userId = -1;
	let data = document.cookie;
	let splits = data.split(",");
	for(var i = 0; i < splits.length; i++) 
	{
		let thisOne = splits[i].trim();
		let tokens = thisOne.split("=");
		if( tokens[0] == "firstName" )
		{
			firstName = tokens[1];
		}
		else if( tokens[0] == "lastName" )
		{
			lastName = tokens[1];
		}
		else if( tokens[0] == "userId" )
		{
			userId = parseInt( tokens[1].trim() );
		}
	}
	
	if( userId <= 0 )
	{
		console.log("invalid cookie");
		window.location.href = "index.html";
	}
	else
	{
		document.getElementById("username").innerHTML = firstName + " " + lastName;
	}
}

function logout()
{
	userId = 0;
	firstName = "";
	lastName = "";
	document.cookie = "firstName= ; expires = Thu, 01 Jan 1970 00:00:00 GMT";
	window.location.href = "index.html";
}

function searchContact()
{
    let srch = document.getElementById("searchText").value;
    document.getElementById("searchResult").innerHTML = "";

    let contactList = "";

    let tmp = {search:srch,userID:userId.toString()};
    let jsonPayload = JSON.stringify( tmp );

    console.log(jsonPayload);
    let url = urlBase + '/searchContacts.' + extension;

    let xhr = new XMLHttpRequest();
    xhr.open("POST", url, true);
    xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
    try
    {
        xhr.onreadystatechange = function() 
        {
            if (this.readyState == 4 && this.status == 200) 
            {
                let jsonObject = JSON.parse( xhr.responseText );
                console.log(jsonObject.error);
                if (jsonObject.error === "No records found.")
                {
                    document.getElementById("searchResult").innerHTML = "No Contacts found";
                    document.getElementById("contactList").innerHTML = contactList;
                }
		else
                {
                    document.getElementById("searchResult").innerHTML = "Contact(s) has been retrieved";
                    
		    
                    for( let i=0; i<jsonObject.results.length; i++ )
                    {
			foundContact = jsonObject.results[i];
                        
                        let toAdd = '<div class="contactBox" id="' + foundContact.ID.toString() +'">';
                        //left flex box, for contact
                          toAdd += '<div class = "contactInfo">';
                            toAdd += '<p class = "firstname">' + foundContact.firstName + '</p>';
                            toAdd += '<p class = "lastname">' + foundContact.lastName + '</p>';
                            toAdd += '<p class = "email">' + foundContact.email + '</p>';
                            toAdd += '<p class = "phone">' + foundContact.phone +'</p>';
                            
                          toAdd += '</div>';
                        //right flex box, for buttons
                          toAdd += '<div>';
                            toAdd += '<button type="button" class="delete-btn" onclick="deleteContact(' + foundContact.ID + ')">Delete</button>';
                            toAdd += '<button type="button" class="edit-btn" onclick="editContact(' + foundContact.ID + ')">Edit</button>';
                          toAdd += '</div>';
                        toAdd += '</div>';
                        
                        contactList += toAdd;
                    }

                    document.getElementById("contactList").innerHTML = contactList;
                }
            }
        };
        xhr.send(jsonPayload);
    }
    catch(err)
    {
        document.getElementById("searchResult").innerHTML = err.message;
    }

}

function addContact() {
  let tmpFirstName = document.getElementById("firstName").value;
  let tmpLastName = document.getElementById("lastName").value;
  let email = document.getElementById("email").value;
  let phone = document.getElementById("phone").value;
  
  let tmp = {firstName:tmpFirstName,
              lastName:tmpLastName,
                 email:email,
                 phone:phone,
                userID:userId};
                 
  let jsonPayload = JSON.stringify(tmp);
  
  fetch("./LAMPAPI/addContact.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body:jsonPayload,
    })
  
  	.then((response) => response.json())
    .then((tmp) => {
        if (tmp.error != "") {
            addResult.textContent = tmp.error;
            addResult.classList.remove("hidden");
            addResult.classList.remove("success");
            addResult.classList.add("error");
        } else {

          addResult.textContent = "New Contact Added";
          addResult.classList.remove("hidden");
          addResult.classList.remove("error");
          addResult.classList.add("success");
          document.getElementById("addForm").reset();
        }
      })
      .catch((error) => {
        addResult.textContent = "An error occurred. Please try again.";
        addResult.classList.remove("hidden");
        addResult.classList.remove("success");
        addResult.classList.add("error");
    });
  
}

function deleteContact(ID) {
  if (!confirm("Are you sure you want to delete this contact?"))
    return;
    
  let tmp = {ID:ID, userID:userId}
  
  let jsonPayload = JSON.stringify(tmp);
  
  fetch("./LAMPAPI/deleteContact.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body:jsonPayload,
    })
  
  	.then((response) => response.json())
    .then((tmp) => {
        if (tmp.error != "") {
            alert("An error occurred. Please try again.");
        } else {

          document.getElementById(ID.toString()).remove();
        }
      })
      .catch((error) => {
        alert("An error occurred. Please try again.");
    });
}

function editContact(ID) {

}