

document.getElementById("loginForm").addEventListener("submit", function (event) {
    event.preventDefault(); 
  
  
    const username = document.getElementById("username").value;
    const password = document.getElementById("password").value;

    const data = {
      login: username,
      password: password,
    };
  
    
    fetch("./LAMPAPI/login.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(data),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.error) {
       
          document.getElementById("errorMessage").textContent = data.error;
        } else {


          window.location.href = "home.html"; 
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        document.getElementById("errorMessage").textContent =
          "An error occurred. Please try again.";
      });
  });