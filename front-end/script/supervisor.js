
//create schedule
function create_schedule() {
  const data = JSON.stringify({
    supervisorId: sessionStorage.getItem("id"),
    studentLimit: document.getElementById("num_of_students").value,
    day: document.getElementById("daySelector").value,
    timeStart: document.getElementById("input1").value,
    timeEnd: document.getElementById("input2").value,
    semesterId: document.getElementById("semesterSelector").value,
    message: document.getElementById("message").value,
    name: document.getElementById("meeting_name").value,
  });

  //   console.log(data);
  xhr.open("POST", `http://localhost:80/Web Project/api/meeting/schedule.php`);
  xhr.send(data);

  xhr.onload = function () {
    let resp = JSON.parse(xhr.response);
    if (xhr.status == 200) {
      alert(resp.message);
      window.location.replace(
        "http://127.0.0.1:5500/front-end/supervisor-meetings.html"
      );
    } else {
      // handle error
      // get the response from xhr.response
      console.log(resp.message);
      alert("Error: " + resp.message);
    }
  };
}

//get meetings
function get_schedules(data = null) {
  if (data == 1) {
    xhr.open(
      "GET",
      `http://localhost:80/Web Project/api/meeting/schedule_history.php?supervisorId=${sessionStorage.getItem(
        "id"
      )}&semesterId=${document.getElementById("semesterSelector").value}`
    );
    xhr.send();

    xhr.onload = function () {
      let resp = JSON.parse(xhr.response);
      console.log(resp);

      let table = document.getElementById("supervisor_history");

      const status = data.status ? "Success" : "Cancelled";

      resp.data.forEach((data) => {
        let tr = document.createElement("tr");

        tr.innerHTML =
          "<td>" +
          data.name +
          "</td>" +
          "<td>" +
          data.studentCount +
          "</td>" +
          "<td>" +
          data.timeStart +
          " - " +
          data.timeEnd +
          "</td>" +
          "<td>" +
          data.day +
          "</td>" +
          "<td>" +
          status +
          "</td>" +
          "<td>" +
          `<a href="/front-end/supervisor-scheduleattendance.html?id=${data.id}"> Attendance </a> ` + //put attendance here
          "</td>";

        table.appendChild(tr);
      });
    };
  } else {
    xhr.open(
      "GET",
      `http://localhost:80/Web Project/api/meeting/schedule.php?supervisorId=${sessionStorage.getItem(
        "id"
      )}&semesterId=${document.getElementById("semesterSelector").value}`
    );
    xhr.send();

    xhr.onload = function () {
      let resp = JSON.parse(xhr.response);
      console.log(resp);

      let table = document.getElementById("supervisor_meetings");

      resp.data.forEach((data) => {
        let tr = document.createElement("tr");

        tr.innerHTML =
          "<td>" +
          data.name +
          "</td>" +
          "<td>" +
          data.timeStart +
          " - " +
          data.timeEnd +
          "</td>" +
          "<td>" +
          data.day +
          "</td>" +
          "<td>" +
          data.studentLimit +
          "</td>" +
          "<td>" +
          data.studentCount +
          "</td>" +
          "<td>" +
          "<button " +
          "class = btn-btn-black  " +
          "type = submit " +
          "name = button " +
          "onclick = " +
          `cancel_meeting(${data.id})` +
          ">" +
          "Cancel meeting" +
          "</button>" +
          "</td>";

        table.appendChild(tr);
      });
    };
  }
}

function cancel_meeting(id) {
  const data = JSON.stringify({
    id: id,
    supervisorId: sessionStorage.getItem("id"),
    status: false,
    message: "test",
  });

  xhr.open("POST", `http://localhost:80/Web Project/api/meeting/schedule.php`);
  xhr.send(data);
  xhr.onload = function () {
    let resp = JSON.parse(xhr.response);

    console.log(resp);

    alert(resp.message);
  };
}

// functon get_attendance(){

//     xhr.open("GET", `http://localhost:80/Web Project/api/meeting/schedule.php`);
//     xhr.send(data);
//     xhr.onload = function () {
//       let resp = JSON.parse(xhr.response);
// }
