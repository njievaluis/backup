<?php
// Include database configuration
require_once 'config.php';

// Check if ID is set and valid
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: students.php');
    exit;
}

$id = $_GET['id'];

// Handle file upload
$profile_picture_update = "";
if(isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
    $allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png");
    $filename = $_FILES["profile_picture"]["name"];
    $filetype = $_FILES["profile_picture"]["type"];
    $filesize = $_FILES["profile_picture"]["size"];
    
    // Verify file extension
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    if(!array_key_exists($ext, $allowed)) {
        $update_error = "Error: Please select a valid file format.";
    }
    
    // Verify file size - 5MB maximum
    $maxsize = 5 * 1024 * 1024;
    if($filesize > $maxsize) {
        $update_error = "Error: File size is larger than the allowed limit (5MB).";
    }
    
    // If no errors, process and move the file
    if(!isset($update_error)) {
        // Create unique filename
        $new_filename = uniqid() . '.' . $ext;
        $upload_dir = "uploads/";
        
        // Create directory if it doesn't exist
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        if(move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $upload_dir . $new_filename)) {
            // Get the current profile picture path
            $get_current_pic = "SELECT profile_picture FROM students WHERE id = $id";
            $current_pic_result = $conn->query($get_current_pic);
            if($current_pic_result->num_rows > 0) {
                $current_pic = $current_pic_result->fetch_assoc()['profile_picture'];
                // Delete the old file if it exists
                if($current_pic && file_exists($current_pic)) {
                    unlink($current_pic);
                }
            }
            
            // Save the new path to the database
            $profile_picture_update = ", profile_picture = '" . $upload_dir . $new_filename . "'";
        } else {
            $update_error = "Error: There was a problem uploading your file. Please try again.";
        }
    }
}
// Process form submission for update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and sanitize inputs
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $course = mysqli_real_escape_string($conn, $_POST['course']);
    
    // Update data in database

 $sql = "UPDATE students SET 
      name = '$name', 
      email = '$email', 
      phone = '$phone', 
      dob = '$dob', 
      course = '$course'
      $profile_picture_update
      WHERE id = $id";
    
    if ($conn->query($sql) === TRUE) {
        $update_success = "Student record updated successfully!";
    } else {
        $update_error = "Error updating record: " . $conn->error;
    }
}

// Get student data
$sql = "SELECT * FROM students WHERE id = $id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    header('Location: students.php');
    exit;
}

$student = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container">
                <a class="navbar-brand logo" href="#">EvaLuis</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item"><a class="nav-link" href="index.php">HOME</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">ABOUT</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">SERVICE</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">DESIGN</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">CONTACT</a></li>
                    </ul>
                    <form class="d-flex search-box">
                        <input class="form-control me-2" type="search" placeholder="Type to Search" aria-label="Search">
                        <button class="btn search-btn" type="submit">Search</button>
                    </form>
                </div>
            </div>
        </nav>
    </header>

    <main>
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="login-form">
                        <h2 class="text-center mb-4">Edit Student Information</h2>
                        
                        <?php if (isset($update_success)): ?>
                            <div class="alert alert-success" role="alert">
                                <?php echo $update_success; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (isset($update_error)): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo $update_error; ?>
                            </div>
                        <?php endif; ?>
                        
                        <form action="edit_student.php?id=<?php echo $id; ?>" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?php echo $student['name']; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo $student['email']; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo $student['phone']; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="dob" class="form-label">Date of Birth</label>
                                <input type="date" class="form-control" id="dob" name="dob" value="<?php echo $student['dob']; ?>" required>
                            </div>
                           <div class="mb-3">
                                <label for="profile_picture" class="form-label">Profile Picture</label>
                                <?php if(!empty($student['profile_picture']) && file_exists($student['profile_picture'])): ?>
                                   <div class="mb-2">
                                      <img src="<?php echo $student['profile_picture']; ?>" class="img-thumbnail" alt="Profile Picture" style="max-width: 150px;">
                                   </div>
                                <?php endif; ?>
                                 <input type="file" class="form-control" id="profile_picture" name="profile_picture" accept="image/*">
                                <small class="form-text text-muted">Leave empty to keep current picture. Max size: 5MB</small>
                           </div>
                            <div class="mb-3">
                                <label for="course" class="form-label">Course</label>
                                <select class="form-select" id="course" name="course" required>
                                    <option value="Web Development" <?php echo ($student['course'] == 'Web Development') ? 'selected' : ''; ?>>Web Development</option>
                                    <option value="Graphic Design" <?php echo ($student['course'] == 'Graphic Design') ? 'selected' : ''; ?>>Graphic Design</option>
                                    <option value="Data Science" <?php echo ($student['course'] == 'Data Science') ? 'selected' : ''; ?>>Data Science</option>
                                    <option value="Mobile App Development" <?php echo ($student['course'] == 'Mobile App Development') ? 'selected' : ''; ?>>Mobile App Development</option>
                                </select>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <a href="students.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Back to List</a>
                                <button type="submit" class="btn login-btn"><i class="fas fa-save me-2"></i>Update Student</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
// Close connection
$conn->close();
?>