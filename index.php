<?php
// Include the database connection file
include 'db.php';

// Fetch types for the dropdown from the database
$typeSql = "SELECT * FROM types";
$typeResult = $conn->query($typeSql);

// SQL query to fetch tasks and their types
$taskSql = "SELECT tasks.*, types.type_name FROM tasks LEFT JOIN types ON tasks.type_id = types.id";
$taskResult = $conn->query($taskSql);
        
        
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta tags necessary for Bootstrap and responsive design -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Day To-Do List</title>
    <!-- Bootstrap CSS for styling and responsiveness -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <!-- Custom CSS file for additional styles -->
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<!-- Main container for the to-do app -->
<div class="todo-app-container">
    <!-- Header section with logo and date -->
    <div class="app-header">
        <img src="logo.png" alt="App Logo">
        <h2>My Day</h2>
        <div class="app-date"><?php echo date("l, F j"); ?></div>
    </div>
    
    <!-- Form for adding a new task -->
    <form  class="row">
        <!-- Input field for entering a new task -->
        <div class="col-8">
            <input type="text" class="form-control task-input" id="task" placeholder="Enter a new task" required>
        </div>
        <!-- Dropdown for selecting task type -->
        <div class="col-2">
            <select id="type_id" class="form-control" required>
                <option value="" disabled selected>Type</option>
                <?php
                // Loop through each type and create a dropdown option
                if ($typeResult->num_rows > 0) {
                    while($typeRow = $typeResult->fetch_assoc()) {
                        echo "<option value='" . $typeRow["id"] . "'>" . $typeRow["type_name"] . "</option>";
                    }
                }
                ?>
            </select>
        </div>
        <!-- Button to add the new task -->
        <div class="col-2">
            <div class="btn btn-primary w-100 add-task-btn">Add Task</div>
        </div>
    </form>

    <!-- List to display tasks -->
    <ul id="taskList">
        <?php
        // Display each task with its type
        if ($taskResult->num_rows > 0) {
            while($taskRow = $taskResult->fetch_assoc()) {
                echo "<li>" . htmlspecialchars($taskRow["task"]) . " - " . htmlspecialchars($taskRow["type_name"]);
                // Link to delete the task
                echo "<a href='#' class='delete-task-btn' data-task-id=$taskRow[id]>Delete</a></li>";
            }
        } else {
            echo "<li class='empty_row'>No tasks yet</li>";
        }
        ?>
    </ul>
</div>



<!-- Bootstrap scripts for interactivity and responsiveness -->

 <script
      src="https://code.jquery.com/jquery-3.6.0.min.js"
      integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
      crossorigin="anonymous"
    ></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

<script>
$(document).ready(function() { 
   
  $('.add-task-btn').click(function(e) {


    e.preventDefault();
    var task = $('#task').val();
    var type_id = $('#type_id').val();
    
      if (task && type_id) {
      $.ajax({
        type: 'POST',
        url: 'task.php',
        data: {
          add: true,
          task: task,
          type_id: type_id
        },
        success: function(response) {
            
            var jsonResponse = JSON.parse(response);
      
         if (jsonResponse.status === 'success') {
  $('#task').val('');
  $('#type_id').val('');
  
  $('.empty_row').remove();
  
  var newTaskHtml = "<li>" + task + " - " + jsonResponse.data.type_name + " <a href='#' class='delete-task-btn' data-task-id="+ jsonResponse.data.id +">Delete</a></li>";
        $('#taskList').append(newTaskHtml);
        
        
  //location.reload();
} else {
  alert('Error adding task');
}
        
            
            
        }
      });
    } else {
      alert('Please enter a task and select a type');
    }



 
  });
  
  
  $('#taskList').on('click', 'a.delete-task-btn', function(e) {
     
        e.preventDefault();
        var taskId = $(this).data('task-id');

        // TODO: Add proper validation and error handling here

        $.ajax({
            type: 'POST',
            url: 'task.php',
            data: {
                delete: true,
                delete_id: taskId
            },
            success: function(response) {
                var jsonResponse = JSON.parse(response);

                if (jsonResponse.status === 'success') {
                    // Remove the deleted task from the list
                    $(e.target).closest('li').remove();

                    if ($('#taskList li').length === 0) {
                        // If no tasks left, add an empty row
                        $('#taskList').append("<li class='empty_row'>No tasks yet</li>");
                    }
                } else {
                    alert('Error deleting task');
                }
            }
        });
    });
});
</script>

</body>
</html>
