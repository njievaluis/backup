<?php
require_once 'config.php';

// File upload handling
$profile_picture = NULL;
if(isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
    $allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png");
    $filename = $_FILES["profile_picture"]["name"];
    $filetype = $_FILES["profile_picture"]["type"];
    $filesize = $_FILES["profile_picture"]["size"];
    
    // Verify file extension
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    if(!array_key_exists($ext, $allowed)) {
        $error = "Error: Please select a valid file format.";
    }
    
    // Verify file size - 5MB maximum
    $maxsize = 5 * 1024 * 1024;
    if($filesize > $maxsize) {
        $error = "Error: File size is larger than the allowed limit (5MB).";
    }
    
    // If no errors, process and move the file
    if(!isset($error)) {
        // Create unique filename
        $new_filename = uniqid() . '.' . $ext;
        $upload_dir = "uploads/";
        
        // Create directory if it doesn't exist
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        if(move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $upload_dir . $new_filename)) {
            // Save the path to the database
            $profile_picture = $upload_dir . $new_filename;
        } else {
            $error = "Error: There was a problem uploading your file. Please try again.";
        }
    }
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $course = mysqli_real_escape_string($conn, $_POST['course']);
    

    $sql = "INSERT INTO students (name, email, phone, dob, course, profile_picture) 
    VALUES ('$name', '$email', '$phone', '$dob', '$course', " . ($profile_picture ? "'$profile_picture'" : "NULL") . ")";
    
    if ($conn->query($sql) === TRUE) {
    
        $success = true;
        $student_id = $conn->insert_id;
    } else {
    
        $error = $conn->error;
    }
    

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Result</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="style.css">
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
                <div class="col-md-8 col-lg-6">
                    <?php if (isset($success) && $success): ?>
                        <div class="login-form">
                            <div class="text-center mb-4">
                                <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                                <h2 class="mt-3 text-success">Registration Successful!</h2>
                            </div>
                            <?php if(!empty($profile_picture) && file_exists($profile_picture)): ?>
                               <div class="text-center mb-3">
                                    <img src="<?php echo $profile_picture; ?>" class="img-thumbnail rounded-circle" alt="Profile Picture" style="max-width: 150px;">
                               </div>
                            <?php endif; ?>
                            
                            <div class="card bg-dark text-white mb-4">
                                <div class="card-header bg-dark">
                                    <h5 class="mb-0">Student Details</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Name:</strong> <?php echo $name; ?></p>
                                    <p><strong>Email:</strong> <?php echo $email; ?></p>
                                    <p><strong>Phone:</strong> <?php echo $phone; ?></p>
                                    <p><strong>Date of Birth:</strong> <?php echo $dob; ?></p>
                                    <p class="mb-0"><strong>Course:</strong> <?php echo $course; ?></p>
                                </div>
                            </div>
                            
                            <p class="text-center">Thank you for registering. We will contact you soon with more information about your course.</p>
                            
                            <div class="d-grid gap-2 mt-4">
                                <div class="row">
                                    <div class="col-6">
                                        <a href="index.php" class="btn login-btn d-block">Register Another</a>
                                    </div>
                                    <div class="col-6">
                                        <a href="students.php" class="btn btn-outline-light d-block">View All Students</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php elseif (isset($error)): ?>
                        <div class="login-form">
                            <div class="text-center mb-4">
                                <i class="fas fa-exclamation-triangle text-danger" style="font-size: 4rem;"></i>
                                <h2 class="mt-3 text-danger">Registration Failed</h2>
                            </div>
                            <p class="text-center">We encountered an error while processing your registration.</p>
                            <p class="text-center">Error details: <?php echo $error; ?></p>
                            <div class="d-grid gap-2 mt-4">
                                <a href="index.php" class="btn login-btn">Try Again</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="login-form">
                            <h2 class="text-center">Invalid Request</h2>
                            <p class="text-center">Please fill out the registration form to continue.</p>
                            <div class="d-grid gap-2 mt-4">
                                <a href="index.php" class="btn login-btn">Go to Registration</a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>