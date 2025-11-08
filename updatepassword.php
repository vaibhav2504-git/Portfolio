<?php
require 'forgotpassword.php';

// Validate and sanitize inputs
$email = mysqli_real_escape_string($con, $_GET['email'] ?? '');
$reset_token = mysqli_real_escape_string($con, $_GET['reset_token'] ?? '');
$newPassword = mysqli_real_escape_string($con, $_POST['newPassword']?? '');
$confirmPassword = mysqli_real_escape_string($con, $_POST['confirmPassword'] ?? '');

// Initialize error message
$error_message = 'link is expired';
$success_message = 'link is expired';

// Validate reset token and its expiration
$tokenValidationQuery = "SELECT * FROM mydata WHERE email = ? AND resettoken = ? AND resettokenexpire > NOW()";
$tokenStmt = $con->prepare($tokenValidationQuery);
$tokenStmt->bind_param("ss", $email, $reset_token);
$tokenStmt->execute();
$tokenResult = $tokenStmt->get_result();

if($tokenResult->num_rows === 0) {
    $error_message = "Invalid or expired reset token. Please request a new password reset link.";
} else {
    if(isset($_POST['sb'])){
        // Validate password match
        if(empty($newPassword) || empty($confirmPassword)){
            $error_message = "Password fields cannot be empty!";
        } elseif($newPassword !== $confirmPassword){
            $error_message = "Passwords do not match!";
        } elseif(strlen($newPassword) < 8){
            $error_message = "Password must be at least 8 characters long!";
        } else {
            // Hash the password for security
            $newPassword = mysqli_real_escape_string($con, $_POST['newPassword'] ?? '');

            // Update password and clear reset token
            $updateQuery = "UPDATE mydata SET password = ?, resettoken = NULL, resettokenexpire = NULL WHERE email = ?";
            $updateStmt = $con->prepare($updateQuery);
            $updateStmt->bind_param("ss", $newPassword, $email);
            
            if($updateStmt->execute()){
                 echo "Password updated successfully! Redirecting to login...";
                header("refresh:2;url=login.html");
                exit();
            } else {
                $error_message = "Failed to update password. Please try again.";
            }
            $updateStmt->close();
        }
    }
}
$tokenStmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Password</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .update-password-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            padding: 30px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .update-password-container:hover {
            transform: scale(1.02);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 25px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: bold;
        }
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .form-group input:focus {
            outline: none;
            border-color: #4CAF50;
            box-shadow: 0 0 5px rgba(76, 175, 80, 0.5);
        }
        .update-btn {
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            opacity: 0.5;
            pointer-events: none;
        }
        .update-btn.active {
            opacity: 1;
            pointer-events: auto;
        }
        .update-btn:hover {
            background-color: #45a049;
            transform: translateY(-3px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .update-btn:active {
            transform: translateY(1px);
            box-shadow: 0 2px 3px rgba(0, 0, 0, 0.1);
        }
        .error-message {
            color: red;
            font-size: 0.8em;
            margin-top: 5px;
            display: none;
        }
        @media (max-width: 480px) {
            .update-password-container {
                width: 95%;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="update-password-container">
        <h2>Update Password</h2>
        <form id="updatePasswordForm" action="#" method="POST">
            <div class="form-group">
                <label for="new_password">Enter New Password</label>
                <input 
                    type="password" 
                    id="new_password" 
                    name="newPassword" 
                    required 
                    placeholder="Enter your new password (minimum 8 characters)" 
                    minlength="8"
                >
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>
                <input 
                    type="password" 
                    id="confirm_password" 
                    name="confirmPassword" 
                    required 
                    placeholder="Confirm your new password"
                >
                <div id="password-error" class="error-message">
                    Passwords do not match
                </div>
            </div>
          
            <button type="submit" id="update-btn" class="update-btn" name="sb">Update Password</button>
        </form>
        <?php if($error_message) { ?>
            <div class="error-message">
                <?php echo $error_message; ?>
            </div>
        <?php } elseif($success_message) { ?>
            <div class="success-message">
                <?php echo "Password Updated Successfully! Redirecting to login..."; ?>
            </div>
        <?php } ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const newPasswordInput = document.getElementById('new_password');
            const confirmPasswordInput = document.getElementById('confirm_password');
            const updateBtn = document.getElementById('update-btn');
            const passwordError = document.getElementById('password-error');
            const form = document.getElementById('updatePasswordForm');

            function validatePasswords() {
                const newPassword = newPasswordInput.value;
                const confirmPassword = confirmPasswordInput.value;

                if (newPassword && confirmPassword) {
                    if (newPassword === confirmPassword) {
                        updateBtn.classList.add('active');
                        passwordError.style.display = 'none';
                        return true;
                    } else {
                        updateBtn.classList.remove('active');
                        passwordError.style.display = 'block';
                        return false;
                    }
                } else {
                    updateBtn.classList.remove('active');
                    passwordError.style.display = 'none';
                    return false;
                }
            }

            // Add event listeners for real-time validation
            newPasswordInput.addEventListener('input', validatePasswords);
            confirmPasswordInput.addEventListener('input', validatePasswords);

            // Form submission handler
            form.addEventListener('submit', function(e) {
                if (!validatePasswords()) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>