<?php
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "trial";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get parameters safely
$department = isset($_GET['department']) ? $_GET['department'] : '';
$scheme = isset($_GET['scheme']) ? $_GET['scheme'] : '';
$semester = isset($_GET['semester']) ? $_GET['semester'] : '';
$usn = isset($_GET['usn']) ? $_GET['usn'] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View SGPA</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <?php if (empty($usn)) { ?>
            <h2>Department: <?php echo htmlspecialchars($department); ?></h2>
            <h3>Scheme: <?php echo htmlspecialchars($scheme); ?></h3>
            <h4>Semester: <?php echo htmlspecialchars($semester); ?></h4>

            <h5 class="mt-4">Select a USN:</h5>
            <ul class="list-group">
                <?php
                $dir = "C:/wamp64/www/project/templates/uploads/$department/$scheme/$semester";
                if (is_dir($dir)) {
                    $files = scandir($dir);
                    $pdfs = array_filter($files, function ($file) {
                        return pathinfo($file, PATHINFO_EXTENSION) === 'pdf';
                    });

                    if (!empty($pdfs)) {
                        foreach ($pdfs as $pdf) {
                            $filename = pathinfo($pdf, PATHINFO_FILENAME);
                            $usn = explode('_', $filename)[0]; ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><?php echo $usn; ?></span>
                                <div>
									<a href="uploads/<?php echo urlencode($department); ?>/<?php echo urlencode($scheme); ?>/<?php echo urlencode($semester); ?>/<?php echo urlencode($pdf); ?>" class="btn btn-info btn-sm" target="_blank">View PDF</a>
                             
                                    <a href="view_sgpa.php?usn=<?php echo urlencode($usn); ?>&department=<?php echo urlencode($department); ?>&scheme=<?php echo urlencode($scheme); ?>&semester=<?php echo urlencode($semester); ?>" class="btn btn-primary btn-sm">View SGPAs</a>
                                </div>
                            </li>
                        <?php }
                    } else {
                        echo "<li class='list-group-item'>No PDFs uploaded.</li>";
                    }
                } else {
                    echo "<li class='list-group-item text-danger'>Invalid directory.</li>";
                }
                ?>
            </ul>
        <?php } else { 
            // Fetch SGPA data 
            $sql = "SELECT DISTINCT StudentName, Semester, SGPA 
                    FROM sgpa_data 
                    WHERE Usn = ? AND Semester <= ? 
                    ORDER BY Semester ASC";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $usn, $semester);
			$stmt->execute();
            $result = $stmt->get_result();

            $student_name = "";
            $sgpa_data = [];

            while ($row = $result->fetch_assoc()) {
                if (isset($row['StudentName']) && !$student_name) {
                    $student_name = $row['StudentName'];
                }
                if (isset($row['Semester']) && isset($row['SGPA'])) {
                    $sgpa_data[] = $row;
                }
            }
            $stmt->close();

        ?>
            <h2 class="text-center"><?php echo htmlspecialchars($department) . " : " . htmlspecialchars($scheme); ?></h2>
            <h3 class="text-center">Student Name: <?php echo htmlspecialchars($student_name); ?></h3>
            <h4 class="text-center">USN: <?php echo htmlspecialchars($usn); ?></h4>

            <table class="table table-bordered mt-3">
                <thead class="thead-dark">
                    <tr>
                        <th>Semester</th>
                        <th>SGPA</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($sgpa_data)) {
                        foreach ($sgpa_data as $row) { ?>
                            <tr>
                                <td><?php echo $row['Semester']; ?></td> <!-- Fixed Case Sensitivity -->
                                <td><?php echo $row['SGPA']; ?></td> <!-- Fixed Case Sensitivity -->
                            </tr>
                        <?php }
                    } else { ?>
                        <tr>
                            <td colspan="2" class="text-center">No SGPA data found.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <div class="text-center mt-3">
                <a href="view_sgpa.php?department=<?php echo urlencode($department); ?>&scheme=<?php echo urlencode($scheme); ?>&semester=<?php echo urlencode($semester); ?>" class="btn btn-primary">Back to USN List</a>
            </div>
        <?php } ?>
    </div>
</body>
</html>

<?php
$conn->close();
?>
