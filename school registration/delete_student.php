<?php
require_once 'config.php';

// Check if ID is passed
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // First, fetch the profile picture path
    $result = $conn->query("SELECT profile_picture FROM students WHERE id = $id");
    $row = $result->fetch_assoc();
    if ($row && !empty($row['profile_picture']) && file_exists($row['profile_picture'])) {
        unlink($row['profile_picture']); // Delete the image file
    }

    // Then, delete the student from database
    $sql = "DELETE FROM students WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        header("Location: students.php?deleted=1");
        exit();
    } else {
        echo "Error deleting student: " . $conn->error;
    }
} else {
    echo "Invalid request.";
}
?>
