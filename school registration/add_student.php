<?php
// Include database configuration
require_once 'config.php';

// Initialize variables
$add_success = "";
$add_error = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and sanitize
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $course = mysqli_real_escape_string($conn, $_POST['course']);

    $profile_picture_path = "";

    // Handle profile picture upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png");
        $filename = $_FILES["profile_picture"]["name"];
        $filetype = $_FILES["profile_picture"]["type"];
        $filesize = $_FILES["profile_picture"]["size"];

        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        if (!array_key_exists($ext, $allowed)) {
            $add_error = "Error: Please select a valid image format.";
        } elseif ($filesize > 5 * 1024 * 1024) { // 5MB limit
            $add_error = "Error: File size exceeds 5MB.";
        } else {
            $upload_dir = "uploads/";
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $new_filename = uniqid() . "." . $ext;
            if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $upload_dir . $new_filename)) {
                $profile_picture_path = $upload_dir . $new_filename;
            } else {
                $add_error = "Error: Failed to upload image.";
            }
        }
    }

    // If no upload errors, insert into database
    if (empty($add_error)) {
        $sql = "INSERT INTO students (name, email, phone, dob, course, profile_picture)
                VALUES ('$name', '$email', '$phone', '$dob', '$course', '$profile_picture_path')";

        if ($conn->query($sql) === TRUE) {
            $add_success = "Student added successfully!";
        } else {
            $add_error = "Error adding student: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Student</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<header>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand logo" href="#">EvaLuis</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
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
                    <input class="form-control me-2" type="search" placeholder="Type to Search">
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
                    <h2 class="text-center mb-4">Add New Student</h2>

                    <?php if (!empty($add_success)): ?>
                        <div class="alert alert-success" role="alert">
                            <?php echo $add_success; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($add_error)): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo $add_error; ?>
                        </div>
                    <?php endif; ?>

                    <form action="add_student.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" required>
                        </div>
                        <div class="mb-3">
                            <label for="dob" class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" id="dob" name="dob" required>
                        </div>
                        <div class="mb-3">
                            <label for="profile_picture" class="form-label">Profile Picture</label>
                            <input type="file" class="form-control" id="profile_picture" name="profile_picture" accept="image/*">
                            <small class="form-text text-muted">Optional. Max size 5MB.</small>
                        </div>
                        <div class="mb-3">
                            <label for="course" class="form-label">Course</label>
                            <select class="form-select" id="course" name="course" required>
                                <option value="">-- Select Course --</option>
                                <option value="Web Development">Factual Programmaing</option>
                                <option value="Graphic Design">Digital Electronics</option>
                                <option value="Data Science">English</option>
                                <option value="Mobile App Development">French</option>
                            </select>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="students.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Back to List</a>
                            <button type="submit" class="btn login-btn"><i class="fas fa-save me-2"></i>Add Student</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</main>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Close connection
$conn->close();
?>
