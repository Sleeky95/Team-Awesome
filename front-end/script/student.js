//for all user tables to get the schedules
function get_schedules(data = null) {
  if (data == 1) {
    xhr.open(
      "GET",
      ` http://localhost:80/Web Project/api/meeting/schedule.php?semesterId=${
        document.getElementById("semesterSelector").value
      }`
    );
    xhr.send();

    xhr.onload = function () {
      let resp = JSON.parse(xhr.response);
      console.log(resp);

      let table = document.getElementById("student_new_meetings");

      resp.data.forEach((data) => {
        let tr = document.createElement("tr");

        tr.innerHTML =
          "<td>" +
          data.scheduleName +
          "</td>" +
          "<td>" +
          data.owner +
          "</td>" +
          "<td>" +
          data.timeSpan +
          "</td>" +
          "<td>" +
          data.day +
          "</td>" +
          "<td>" +
          "<button " +
          "class = btn-btn-black  " +
          "type = submit " +
          "name = button " +
          "onclick = " +
          `join_meeting(${data.id})` + // put data.id here when bolu has added id
          ">" +
          "Join meeting" +
          "</button>" +
          "</td>";

        table.appendChild(tr);
      });
    };
  } else if (data == 2) {
    xhr.open(
      "GET",
      `http://localhost:80/Web Project/api/meeting/appointment_history.php?userId=${sessionStorage.getItem(
        "id"
      )}&semesterId=${document.getElementById("semesterSelector").value}`
    );
    xhr.send();

    xhr.onload = function () {
      let resp = JSON.parse(xhr.response);
      console.log(resp);

      let table = document.getElementById("student_history");

      resp.data.forEach((data) => {
        let tr = document.createElement("tr");

        tr.innerHTML =
          "<td>" +
          data.scheduleName +
          "</td>" +
          "<td>" +
          data.owner +
          "</td>" +
          "<td>" +
          data.timeSpan +
          "</td>" +
          "<td>" +
          data.day +
          "</td>" +
          "<td>" +
          data.status +
          "</td>";
        table.appendChild(tr);
      });
    };
  } else {
    xhr.open(
      "GET",
      `http://localhost:80/Web Project/api/meeting/appointment.php?userId=${sessionStorage.getItem(
        "id"
      )}&semesterId=${document.getElementById("semesterSelector").value}`
    );
    xhr.send();

    xhr.onload = function () {
      let resp = JSON.parse(xhr.response);
      console.log(resp);

      if (!resp) {
        resp = [];
      }

      let table = document.getElementById("student_meetings");

      resp.data.forEach((data) => {
        let tr = document.createElement("tr");

        tr.innerHTML =
          "<td>" +
          data.scheduleName +
          "</td>" +
          "<td>" +
          data.name +
          "</td>" +
          "<td>" +
          data.timeSpan;
        "</td>" +
          "<td>" +
          data.day +
          "<td>" +
          "<button " +
          "class = btn-btn-black" +
          "type = submit " +
          "name = button " +
          "onclick = " +
          `cancel_meeting(${data.id})` +
          ">" +
          "Join meeting" +
          "</button>" +
          "</td>";

        table.appendChild(tr);
      });
    };
  }
}

//user cancel a meeting
function cancel_meeting() {
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

//user join a meeting
function join_meeting(id) {
  const data = JSON.stringify({
    scheduleId: id,
    userId: sessionStorage.getItem("id"),
  });

  xhr.open(
    "POST",
    `http://localhost:80/Web Project/api/meeting/appointment.php`
  );
  xhr.send(data);
  xhr.onload = function () {
    let resp = JSON.parse(xhr.response);

    console.log(resp);

    alert(resp.message);
  };
}
