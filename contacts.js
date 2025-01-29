const urlBase = 'http://scuba2havefun.xyz/LAMPAPI';
const extension = 'php';

let userId = 0;
let firstName = "";
let lastName = "";


function saveCookie()
{
	let minutes = 20;
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
                }
		else
                {
                    document.getElementById("searchResult").innerHTML = "Contact(s) has been retrieved";
                    
		    
                    for( let i=0; i<jsonObject.results.length; i++ )
                    {
			foundContact = jsonObject.results[i];
                        contactList += foundContact.firstName + " " + foundContact.lastName + ";  " + foundContact.email + ", " + foundContact.phone;
                        if( i < jsonObject.results.length - 1 )
                        {
                            contactList += "<br />\r\n";
                        }
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