let xhr = new XMLHttpRequest();
// xhr.setRequestHeader("Content-type", "application/json; charset=utf-8");
const baseUrl = "https://awesome.coconuthead.dev";
let Adminautho;
let examinerautho;
let studentautho;
//Function to register a user
function Register() {
  console.log('I have been called');
  let type;
  var ele = document.getElementsByName("type");
  // window.location.replace("http://127.0.0.1:5500/front-end/Signin.html");
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
  xhr.open("POST", `${baseUrl}/admin/signup`);
  //send the request with params
  xhr.send(data);
//get the data in the console
  xhr.onload = function () {
    // let resp = JSON.parse(xhr.response);
    if (xhr.status == 200) {
      // alert(resp.message);
      window.location.replace("http://127.0.0.1:5500/Signin.html");
    } else {
      // handle error
      // get the response from xhr.response
      console.log(resp.message);
      alert("Error: " + resp.message);
    }
  };
}

//sign in
function adminSignIn() {
  const data = JSON.stringify({
    //email: document.getElementById("Email").value,
    identification_no: document.getElementById("aID").value,
    password: document.getElementById("aPassword").value,
  });

  xhr.open("POST", `${baseUrl}/admin/signin`);
  xhr.send(data);

  xhr.onload = function () {
    let resp = JSON.parse(xhr.response);
    if (xhr.status == 200) {
    
      window.localStorage.setItem('Adminautho',resp.authorization );
      //  autho=resp.authorization;
        window.location.replace(
          "http://127.0.0.1:5500/admin-accounts.html"
        );
      
       
      
    } else {

      alert("Error: " + resp.message);
    }
  };
}

function examinerSignIn() {
  const data = JSON.stringify({
    //email: document.getElementById("Email").value,
    identification_no: document.getElementById("eID").value,
    password: document.getElementById("ePassword").value,
  });

  xhr.open("POST", `${baseUrl}/examiner/signin`);
  xhr.send(data);

  xhr.onload = function () {
    let resp = JSON.parse(xhr.response);
    if (xhr.status == 200) {
    
      window.localStorage.setItem('examinerautho',resp.authorization );
      //  autho=resp.authorization;
        window.location.replace(
          "http://127.0.0.1:5500/examiner-dashboard.html"
        );
      
       
      
    } else {

      alert("Error: " + resp.message);
    }
  };
}

function studentSignIn() {
  const data = JSON.stringify({
    //email: document.getElementById("Email").value,
    identification_no: document.getElementById("sID").value,
    password: document.getElementById("sPassword").value,
  });

  xhr.open("POST", `${baseUrl}/student/signin`);
  xhr.send(data);

  xhr.onload = function () {
    let resp = JSON.parse(xhr.response);
    if (xhr.status == 200) {
    
      window.localStorage.setItem('studentautho',resp.authorization );
      //  autho=resp.authorization;
        window.location.replace(
          "http://127.0.0.1:5500/Student-dashboard.html"
        );
      
       
      
    } else {

      alert("Error: " + resp.message);
    }
  };
}

//create account
function createAcc() {
  const data = JSON.stringify({
    identification_no: document.getElementById("ID").value,
    password: document.getElementById("Password").value,
    full_name: document.getElementById("FullName").value,
    account_type: document.getElementById("AccType").value
  });
  
  xhr.open("POST", `${baseUrl}/admin/createAccount`);
  xhr.setRequestHeader('authorization', window.localStorage.getItem('Adminautho'));
  xhr.send(data);

  xhr.onload = function () {
    // let resp = JSON.parse(xhr.response);
    if (xhr.status == 200) {
      
      alert(`Account of type ${document.getElementById("AccType").value} created`);
      document.getElementById("ID").value="";
      document.getElementById("Password").value="";
      document.getElementById("FullName").value="";
      document.getElementById("AccType").value="";
      
    } else {
    }
  };
}


//submit project
function submitProj() {
  const data = JSON.stringify({
    title: document.getElementById("title").value,
    sections:[
      {
      1:document.getElementById("abstract").value,
    },
  {
    2:document.getElementById("litReview").value,
  },
  {
    3:document.getElementById("methodology").value,
  },
  {
    4:document.getElementById("analysis").value,
  },
  {
    5:document.getElementById("conclusion").value,
  },
  ],
 
  });
  
  xhr.open("POST", `${baseUrl}/student/submit`);
  xhr.setRequestHeader('authorization', window.localStorage.getItem('studentautho'));
  xhr.send(data);

  xhr.onload = function () {
    // let resp = JSON.parse(xhr.response);
    if (xhr.status == 200) {
      
      alert(`Project: ${document.getElementById("title").value} Submitted`);
      document.getElementById("title").value="";
      document.getElementById("abstract").value="";
      document.getElementById("litReview").value="";
      document.getElementById("methodology").value="";
      document.getElementById("analysis").value="";
      document.getElementById("conclusion").value="";
    } else {
    }
  };
}


function displayProj() {
  xhr.open("GET", `${baseUrl}/submission`);

  xhr.send();
  
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
  xhr.open("POST", `${baseUrl}/signout`);

  xhr.send();
  window.location.replace("http://127.0.0.1:5500/Signin.html");
}






