<?php
require_once 'config.php';

$query = isset($_GET['query']) ? $_GET['query'] : '';
$students = [];

if (!empty($query)) {
    $search_query = mysqli_real_escape_string($conn, $query);
    $sql = "SELECT * FROM students WHERE name LIKE '%$search_query%' OR email LIKE '%$search_query%' ORDER BY name ASC";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $students[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Students</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
    <h2 class="mb-4 text-center">Search Students</h2>
    <form class="d-flex mb-4" method="GET" action="search.php">
        <input class="form-control me-2" type="text" name="query" placeholder="Enter name or email" value="<?php echo htmlspecialchars($query); ?>">
        <button class="btn btn-primary" type="submit">Search</button>
    </form>

    <?php if (!empty($query)): ?>
        <h5 class="mb-3">Search Results for: <strong><?php echo htmlspecialchars($query); ?></strong></h5>

        <?php if (count($students) > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Profile</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>DOB</th>
                            <th>Course</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $index => $student): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td>
                                    <?php if (!empty($student['profile_picture']) && file_exists($student['profile_picture'])): ?>
                                        <img src="<?php echo $student['profile_picture']; ?>" alt="Profile" width="50" class="rounded-circle">
                                    <?php else: ?>
                                        <img src="default.png" alt="Default" width="50" class="rounded-circle">
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($student['name']); ?></td>
                                <td><?php echo htmlspecialchars($student['email']); ?></td>
                                <td><?php echo htmlspecialchars($student['phone']); ?></td>
                                <td><?php echo htmlspecialchars($student['dob']); ?></td>
                                <td><?php echo htmlspecialchars($student['course']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">No results found for "<strong><?php echo htmlspecialchars($query); ?></strong>"</div>
        <?php endif; ?>
    <?php endif; ?>
    
    <a href="students.php" class="btn btn-secondary mt-3">Back to Student List</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>
