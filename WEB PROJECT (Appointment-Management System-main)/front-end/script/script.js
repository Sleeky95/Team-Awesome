let xhr = new XMLHttpRequest();
// xhr.setRequestHeader("Content-type", "application/json; charset=utf-8");
const baseUrl = "https://awesome.coconut.dev";
let autho;

//Function to register a user
function Register() {
  let type;
  var ele = document.getElementsByName("type");
  window.location.replace("http://127.0.0.1:5500/front-end/Signin.html");
  for (i = 0; i < ele.length; i++) {
    if (ele[i].checked) {
      type = ele[i].value;
    }
  }
  const data = JSON.stringify({
    identification_no: document.getElementById("ID").value,
    password:document.getElementById("Password").value,
    full_name: document.getElementById("Fullname").value,
    account_type: "ADMIN",
    email:document.getElementById("Email").value,

  });
  console.log(data);
//make request 
  xhr.open("POST", `{{baseUrl}}/admin/signup`);
  //send the request with params
  xhr.send(data);
//get the data in the console
  xhr.onload = function () {
    let resp = JSON.parse(xhr.response);
    if (xhr.status == 200) {
      alert(resp.message);
      window.location.replace("http://127.0.0.1:5500/front-end/signin.html");
    } else {
      // handle error
      // get the response from xhr.response
      console.log(resp.message);
      alert("Error: " + resp.message);
    }
  };
}

//sign in
function SignIn() {
  const data = JSON.stringify({
    //email: document.getElementById("Email").value,
    identification_no: document.getElementById("Email").value,
    password: document.getElementById("Password").value,
  });

  xhr.open("POST", `${baseUrl}/admin/signin`);
  xhr.send(data);

  xhr.onload = function () {
    let resp = JSON.parse(xhr.response);
    if (xhr.status == 200) {
      //   alert(resp.message);

      //   save user's details in a session
      // sessionStorage.setItem("id", resp.data.id);
      // sessionStorage.setItem("firstName", resp.data.firstName);
      // sessionStorage.setItem("email", resp.data.email);
      // sessionStorage.setItem("surname", resp.data.surname);
      // sessionStorage.setItem("type", resp.data.type);

       autho=resp.authorisation;
        window.location.replace(
          "http://127.0.0.1:5500/front-end/admin-accounts.html"
        );
      
       
      
    } else {
      // handle error
      // get the response from xhr.response

      alert("Error: " + resp.message);
    }
  };
}

//authentication
function auth() {
  console.log(sessionStorage.getItem("id"));
  if (!sessionStorage.getItem("id")) {
    window.location.replace("http://127.0.0.1:5500/front-end/signin.html");
  }
}

//logout
function logout() {
  sessionStorage.clear();
  console.log(sessionStorage.getItem("type"));
  window.location.replace("http://127.0.0.1:5500/front-end/signin.html");
}






