<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- SweetAlert CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10">

    <!-- FullCalendar CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.10.1/main.min.css" rel="stylesheet">
  <title>Lecturer Dashboard | Lab-Track</title>
  <link rel="stylesheet" href="../css/dashboard.css">
</head>
<body>
  <header>
    <div class="container">
    <?php 
      session_start(); // Start the session
      // Check if the user is logged in
      if(isset($_SESSION['Username'])) {
          echo "<h1>Welcome, ".$_SESSION['Username']."</h1>";
      } else {
          echo "<h1>Welcome, Guest</h1>";
      }
    ?>

      <nav>
        <ul>
          <li><a href="../view/dashboard.php">Dashboard</a></li>
          <li><a href="../view/calendar.php">Calendar</a></li>
          <li><a href="../view/reminders.php">Reminders</a></li>
          <li><a href="../view/schedule.php">Schedule </a></li>
          <li><a href="../view/loginpage.html">Logout</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <main>
    <section class="assignments">
      <div class="container">
        <h2>Assignments</h2>
        <div class="assignment-list">
          <!-- Dynamically generated assignment cards -->
          
          <!-- More assignment cards -->
        </div>
        <button class="add-assignment-btn">Add Assignment</button>
      </div>
    </section>
    <section class="notifications">
      <div class="container">
        <h2>Notifications</h2>
        <div class="notification-list">
          <!-- Dynamically generated notification items -->
          <!-- <div class="notification-item">
            <p>New assignment added: Assignment 1</p>
            <span class="close-btn">×</span>
          </div> -->
          <!-- More notification items -->
        </div>
      </div>
    </section>
  </main>

  <div id="popup-overlay" class="popup-overlay">
    <div id="popup-box" class="popup-box">
      <form id="add-assignment-form"  action="../action/add_assignment_action_action.php" method="post" >
        <label for="task_name">Task Name:</label>
        <input type="text" id="task_name" name="task_name" required />
  
        <label for="task_due_date">Task Due Date:</label>
        <input type="date" id="task_due_date" name="task_due_date" required />
  
        <button type="submit">Add Assignment</button>
        <button class="close-btn">Close</button>
      </form>
    </div>
  </div>


<!-- Popup form for editing tasks -->
<div id="edit-popup-overlay" class="popup-overlay">
  <div id="edit-popup-box" class="popup-box">
    <form id="edit-assignment-form" action="../action/edit_assignment_action.php" method="post">
      <input type="hidden" id="edit-task-id" name="task_id">
      <label for="edit-task-name">Task Name:</label>
      <input type="text" id="edit-task-name" name="task_name" required />

      <label for="edit-task-due-date">Task Due Date:</label>
      <input type="date" id="edit-task-due-date" name="task_due_date" required />
      <button type="submit">Save Changes</button>
      <button class="close-btn" onclick="closeEditPopup()">Close</button>
    </form>
  </div>
</div>



 
  

  <script src="../js/dljavascript.js"></script>

  <!-- SweetAlert JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>


<script>
    document.addEventListener('DOMContentLoaded', function() {   

// Add event listener for the "Add Assignment" button
const addAssignmentBtn = document.querySelector('.add-assignment-btn');
addAssignmentBtn.addEventListener('click', function () {
  openPopup();
});

// Function to open the popup
function openPopup() {
  const popupOverlay = document.getElementById('popup-overlay');
  popupOverlay.classList.add('visible');
}

// Function to close the popup
function closePopup() {
  const popupOverlay = document.getElementById('popup-overlay');
  popupOverlay.classList.remove('visible');
}

// Add event listener for the form submission
const addAssignmentForm = document.getElementById('add-assignment-form');
addAssignmentForm.addEventListener('submit', function (event) {
  event.preventDefault();

  // Get input values
  const taskName = document.getElementById('task_name').value;
  const taskDueDate = document.getElementById('task_due_date').value;

  // Create new assignment object
  const newAssignment = {
    taskName,
    taskDueDate,
  };

  const xhr = new XMLHttpRequest();
  xhr.open('POST', '../action/add_assignment_action_action.php', true);
  xhr.setRequestHeader('Content-Type', 'application/json');

  xhr.onload = function () {
    if (xhr.status >= 200 && xhr.status < 400) {
      // Success - parse response
      const response = JSON.parse(xhr.responseText);
      console.log('Server response:', response);
      // Display SweetAlert for successful assignment addition
      Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: response.message,
      });
      // Assuming the server responded with the added assignment data, you can update the UI accordingly
      const assignmentData = response.data;
      console.log('New assignment:', assignmentData);
      updateAssignmentsTable(assignmentData);
    } else {
      // Error - handle accordingly
      console.error('Server error:', xhr.status, xhr.statusText);
      // Display SweetAlert for failed assignment addition
      Swal.fire({
        icon: 'error',
        title: 'Error!',
        text: 'Failed to add assignment. Please try again.',
      });
    }
  };

  xhr.onerror = function () {
    console.error('Request failed');
    // Display SweetAlert for failed assignment addition due to network error
    Swal.fire({
      icon: 'error',
      title: 'Error!',
      text: 'Failed to add assignment. Please check your internet connection and try again.',
    });
  };

  xhr.send(JSON.stringify(newAssignment));

  // Close popup
  closePopup();
});

// Function to update assignments table with new assignment
function updateAssignmentsTable(newAssignment) {
  const assignmentList = document.querySelector('.assignment-list');
  const card = generateAssignmentCard(newAssignment);
  assignmentList.appendChild(card);
  addNotification(newAssignment.task_name + ' has been added successfully.');
}

// Add event listener for the close button
const closeBtn = document.querySelector('.close-btn');
closeBtn.addEventListener('click', closePopup);

const assignmentsData = [];

function generateAssignmentCard(assignment) {
  
  const assignmentList = document.querySelector('.assignment-list');
  const card = document.createElement('div');
  card.classList.add('assignment-card');
  card.innerHTML = `
    <h3 i>${assignment.task_name}</h3>
    <p>Due Date: ${assignment.TaskDueDate}</p>
    <button class="edit-btn">Edit</button>
    <button class="delete-btn">Delete</button>
  `;
  return card;
}

function generateNotificationItem(notification) {
  
  const item = document.createElement('div');
  item.classList.add('notification-item');
  item.innerHTML = `
    <p>${notification}</p>
    <span class="close-btn">×</span>
  `;
  return item;
}

function addNotification(notification) {
  const notificationList = document.querySelector('.notification-list');
  const notificationItem = generateNotificationItem(notification);
  notificationList.appendChild(notificationItem);
}


});

</script>

 <!-- FullCalendar JavaScript -->
 <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.10.1/main.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.10.1/locales-all.min.js"></script>

</body>
</html>