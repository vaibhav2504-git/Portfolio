<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $servername = "localhost"; // Change as necessary
    $username = "root"; // Change as necessary
    $password = ""; // Change as necessary
    $dbname = "portfolio"; // Change as necessary

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
        // Set parameters and execute
        $name = $_POST['name'];
        $email = $_POST['email'];
        $tel = $_POST['tel'];
        $subject = $_POST['subject'];
        $message = $_POST['message'];

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO contact (name, email, tel, subject, message) VALUES (?, ?, ? ,? ,?)");
    $stmt->bind_param("sssss", $name, $email, $tel, $subject, $message);


    $stmt->execute();

    echo '<p style="color:red; font-size: 4rem;">Thank you for your message! we will get back to you shortly. redirecting...</p>';
        header("refresh:2;url=index.html");
    $stmt->close();
    $conn->close();
}
?>