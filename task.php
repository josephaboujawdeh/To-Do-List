<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db.php';


if (isset($_POST['add'])) {
  $task = $_POST['task'];
  $type_id = $_POST['type_id'];
 

  $addSql = $conn->prepare("INSERT INTO tasks (task, type_id) VALUES (?, ?)");
    $addSql->bind_param("si", $task, $type_id);
    $addSql->execute();
    $lastInsertId = $conn->insert_id;
    
    $addedTaskSql = "SELECT tasks.*, types.type_name FROM tasks LEFT JOIN types ON tasks.type_id = types.id WHERE tasks.id = ?";
    $addedTaskStmt = $conn->prepare($addedTaskSql);
    $addedTaskStmt->bind_param("i", $lastInsertId);
    $addedTaskStmt->execute();
    //$addedTaskResult = $addedTaskStmt->get_result();
    
    $result = $addedTaskStmt->get_result();

    // Fetch the data as an associative array
    $taskData = $result->fetch_assoc();
    
    $addedTaskStmt->close();
    $addSql->close();

 //print_r($addedTaskRow);
 
  if ($addSql) {
    echo json_encode(['status' => 'success', 'data' => $taskData]);
  } else {
    echo json_encode(['status' => 'error']);
  }

  // Close the prepared statement and the database connection
  
  $conn->close();

  exit;
  
}


// Delete task
if (isset($_POST['delete'])) {
    $id = $_POST['delete_id'];

   $deleteSql = $conn->prepare("DELETE FROM tasks WHERE id = ?");
   $deleteSql->bind_param("i", $id);
   $deleteSql->execute();
   $deleteSql->close();
   
   
   $addedTaskSql = "SELECT tasks.*, types.type_name FROM tasks LEFT JOIN types ON tasks.type_id = types.id";
    $addedTaskStmt = $conn->prepare($addedTaskSql);
    $addedTaskStmt->execute();
    //$addedTaskResult = $addedTaskStmt->get_result();
    
    $result = $addedTaskStmt->get_result();

    // Fetch the data as an associative array
    $taskData = $result->fetch_assoc();
    
    
   
   
    if ($deleteSql) {
    echo json_encode(['status' => 'success', 'data' => $taskData]);
  } else {
    echo json_encode(['status' => 'error']);
  }

  //  header("Location: index.php");
}



$conn->close();
?>
