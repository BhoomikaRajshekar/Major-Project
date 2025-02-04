<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_POST['download'])) {
    // Get values from the form
    $department = $_POST['department'];
    $scheme = $_POST['scheme'];
    $year = $_POST['year'];

    // Define the full path to the Python executable
    $python_path = '"C:\\Users\\Fiza Naaz\\AppData\\Local\\Programs\\Python\\Python311\\python.exe"';  // Adjust this path if needed

    // Prepare the command to run the Python script with arguments
    $command = $python_path . ' "C:\\wamp64\\www\\project\\templates\\result.py" ' . escapeshellarg($department) . ' ' . escapeshellarg($scheme) . ' ' . escapeshellarg($year);

    // Log the output and error
    $python_output = shell_exec($command . ' 2>&1');  // Capture both stdout and stderr

    // Display the result of the Python script
    echo "<pre>" . htmlspecialchars($python_output) . "</pre>";  // Display output nicely
    // Redirect to dashboard after 1 minute (60 seconds) using JavaScript
    echo "<script>
            setTimeout(function() {
                window.location.href = 'dashboard.html';  // Adjust this path if necessary
            }, 30000);  // 30000 milliseconds = 0.5 minute
          </script>";
}
?>
