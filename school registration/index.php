<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Registration System</title>
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
                        <li class="nav-item"><a class="nav-link active" href="#">HOME</a></li>
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
            <div class="row">
                <div class="col-lg-6 left-content">
                <h2>Database &</h2>
                    <h1>Development</h1>
                    <h3>Project</h3>
                    <p>The student registration database project streamlines the process of capturing and managing student information, enabling efficient tracking of enrollment, course selections, and academic status while ensuring data integrity and accessibility for both administrators and students.</p>
                    <div class="d-flex gap-3">
                        <button class="btn join-btn btn-lg">JOIN US</button>
                        <a href="students.php" class="btn btn-outline-light btn-lg">View Students</a>
                    </div>
                </div>
                <div class="col-lg-5 offset-lg-1 right-content">
                    <div class="login-form">
                        <h2 class="text-center mb-4">Student Registration</h2>
                        <form action="register.php" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <input type="text" class="form-control" name="name" placeholder="Enter Full Name" required>
                            </div>
                            <div class="mb-3">
                                <input type="email" class="form-control" name="email" placeholder="Enter Email" required>
                            </div>
                            <div class="mb-3">
                                <input type="tel" class="form-control" name="phone" placeholder="Enter Phone Number" required>
                            </div>
                            <div class="mb-3">
                                <input type="date" class="form-control" name="dob" required>
                            </div>
                            <div class="mb-3">
                                <select class="form-select" name="course" required>
                                    <option value="" disabled selected>Select Course</option>
                                    <option value="Database">Database</option>
                                    <option value="Web Programming">Web Programming</option>
                                    <option value="Mathematics">Mathematics</option>
                                    <option value="Operation System">Operation System</option>
                                </select>
                            </div> 
                        <div class="mb-3">
                            <label for="profile_picture" class="form-label">Profile Picture</label>
                            <input type="file" class="form-control" name="profile_picture" id="profile_picture" accept="image/*">
                            <small class="form-text text-muted">Max size: 5MB</small>
                        </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn login-btn">Register</button>
                            </div>
                        </form>
                        <div class="form-footer mt-4 text-center">
                            <p>Already have an account? <a href="#">Sign in here</a></p>
                            <div class="social-icons mt-3">
                                <a href="#" class="me-2"><i class="fab fa-facebook-f"></i></a>
                                <a href="#" class="me-2"><i class="fab fa-instagram"></i></a>
                                <a href="#" class="me-2"><i class="fab fa-twitter"></i></a>
                                <a href="#" class="me-2"><i class="fab fa-google"></i></a>
                                <a href="#"><i class="fab fa-skype"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>