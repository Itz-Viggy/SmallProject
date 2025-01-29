document.getElementById("registerForm").addEventListener("submit", function (event) {
    event.preventDefault(); 
    
    //registerResult.textContent = "test";
    //registerResult.classList.add("success");

  	// Get Elements from the form
  	let login = document.getElementById("login").value;
  	let password = document.getElementById("password").value;
  	let firstName = document.getElementById("firstName").value;
  	let lastName = document.getElementById("lastName").value;

  	let rePassword = document.getElementById("rePassword").value;
  
  	if (rePassword != password) {
  		registerResult.textContent = "Password and Re-Enter Password fields must match";
      registerResult.classList.remove("hidden");
      registerResult.classList.add("error")
  		return;
  	}
  
  	let tmp = {login:login,password:password,firstName:firstName,lastName:lastName};
    let jsonPayload = JSON.stringify( tmp );
  
    
    fetch("./LAMPAPI/register.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body:jsonPayload,
    })
  
  	.then((response) => response.json())
    .then((tmp) => {
        if (tmp.error != "") {
            if (tmp.error === "Login already exists") {
                registerResult.textContent = "This login is already taken. Please choose a different login.";
            } else {
                registerResult.textContent = tmp.error;
            }
            registerResult.classList.remove("hidden");
            registerResult.classList.add("error");
        } else {

          registerResult.textContent = "Account registered successfully. Login to continue.";
          registerResult.classList.remove("hidden");
          registerResult.classList.add("success");
          window.location.href = "login.html"; 
        }
      })
      .catch((error) => {
        registerResult.textContent = "An error occurred. Please try again.";
        registerResult.classList.remove("hidden");
        registerResult.classList.add("error");
    });
 });
