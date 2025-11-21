<?php
// Include database configuration
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: cc_form.html");
    exit();
}

// Get caterer information
$caterer_id = $_SESSION['caterer_id'];
$caterer_name = $_SESSION['caterer_name'];

// Handle client basic info submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['step']) && $_POST['step'] == '1') {
    $client_first_name = sanitize_input($_POST['client_first_name']);
    $client_last_name = sanitize_input($_POST['client_last_name']);
    $client_id = sanitize_input($_POST['client_id']);
    
    // Validate input
    if (empty($client_first_name) || empty($client_last_name) || empty($client_id)) {
        echo "<script>alert('All fields are required. Please fill out the form completely.'); window.history.back();</script>";
        exit();
    }
    
    // Name validation
    if (!/^[a-zA-Z\s]+$/.test($client_first_name) || strlen($client_first_name) < 2) {
        echo "<script>alert('Client First Name must be at least 2 characters and contain only letters and spaces'); window.history.back();</script>";
        exit();
    }
    
    if (!/^[a-zA-Z\s]+$/.test($client_last_name) || strlen($client_last_name) < 2) {
        echo "<script>alert('Client Last Name must be at least 2 characters and contain only letters and spaces'); window.history.back();</script>";
        exit();
    }
    
    // Client ID validation
    if (strlen($client_id) < 3) {
        echo "<script>alert('Client ID must be at least 3 characters long'); window.history.back();</script>";
        exit();
    }
    
    // Check if client already exists
    $check_sql = "SELECT * FROM clients WHERE ClientIdentification = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("s", $client_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo "<script>alert('An account already exists for this client. The Caterer should be alerted that an account already exists for the client and the user should be redirected back to the Create A New Client Account page.'); window.location.href='client_create.php';</script>";
        exit();
    }
    
    // Store client data in session for next step
    $_SESSION['temp_client'] = array(
        'first_name' => $client_first_name,
        'last_name' => $client_last_name,
        'id' => $client_id
    );
    
    // Redirect to client information page
    header("Location: client_information.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Client Account - Culinary Connoisseurs</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <h2>Culinary Connoisseurs</h2>
            </div>
            <div class="nav-links">
                <a href="dashboard.php" class="nav-link">Dashboard</a>
                <a href="search_caterer.php" class="nav-link">Search Account</a>
                <a href="booking_form.php" class="nav-link">Book Event</a>
                <a href="booking_cancel.php" class="nav-link">Cancel Event</a>
                <a href="additional_service_request.php" class="nav-link">Request Services</a>
                <a href="additional_service_update.php" class="nav-link">Update Services</a>
                <a href="client_create.php" class="nav-link active">Create Client</a>
                <a href="logout.php" class="nav-link">Logout</a>
            </div>
            <div class="nav-user">
                <span>Welcome, <?php echo htmlspecialchars($caterer_name); ?></span>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <h1>Create a New Client Account</h1>
        <p>Enter the client's basic information to create their account</p>

        <form id="clientCreateForm" method="post" action="client_create.php" onsubmit="return validateClientCreateForm()">
            <input type="hidden" name="step" value="1">
            
            <div class="form-group">
                <label for="client_first_name">Client First Name:</label>
                <input type="text" id="client_first_name" name="client_first_name" required>
                <small>Enter the client's first name (minimum 2 characters, letters only)</small>
            </div>

            <div class="form-group">
                <label for="client_last_name">Client Last Name:</label>
                <input type="text" id="client_last_name" name="client_last_name" required>
                <small>Enter the client's last name (minimum 2 characters, letters only)</small>
            </div>

            <div class="form-group">
                <label for="client_id">Client ID:</label>
                <input type="text" id="client_id" name="client_id" required>
                <small>Enter a unique client identification number (minimum 3 characters)</small>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Create Client Account</button>
                <button type="button" class="btn btn-secondary" onclick="window.location.href='dashboard.php'">Cancel</button>
            </div>
        </form>

        <div class="help-section">
            <h3>Important Information</h3>
            <ul>
                <li>All client data must be validated before submission</li>
                <li>If a client already exists, you will be notified and can choose another option</li>
                <li>After creating the basic account, you will be prompted to enter client personal information</li>
                <li>Client ID should be unique and easy to remember</li>
            </ul>
        </div>

        <div class="recent-clients">
            <h3>Recently Created Clients</h3>
            <?php
            // Show recently created clients
            $recent_sql = "SELECT * FROM clients ORDER BY created_at DESC LIMIT 5";
            $result = $conn->query($recent_sql);
            
            if ($result->num_rows > 0) {
                echo '<div class="clients-table">';
                echo '<table>';
                echo '<thead>';
                echo '<tr><th>Client ID</th><th>First Name</th><th>Last Name</th><th>Created</th></tr>';
                echo '</thead>';
                echo '<tbody>';
                
                while ($row = $result->fetch_assoc()) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($row['ClientIdentification']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['ClientFirstName']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['ClientLastName']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['created_at']) . '</td>';
                    echo '</tr>';
                }
                
                echo '</tbody>';
                echo '</table>';
                echo '</div>';
            } else {
                echo '<p>No clients have been created yet.</p>';
            }
            ?>
        </div>
    </div>

    <script>
        function validateClientCreateForm() {
            const firstName = document.getElementById('client_first_name').value.trim();
            const lastName = document.getElementById('client_last_name').value.trim();
            const clientId = document.getElementById('client_id').value.trim();

            // First Name validation
            if (!/^[a-zA-Z\s]+$/.test(firstName) || firstName.length < 2) {
                alert('Client First Name must be at least 2 characters and contain only letters and spaces');
                return false;
            }

            // Last Name validation
            if (!/^[a-zA-Z\s]+$/.test(lastName) || lastName.length < 2) {
                alert('Client Last Name must be at least 2 characters and contain only letters and spaces');
                return false;
            }

            // Client ID validation
            if (clientId.length < 3) {
                alert('Client ID must be at least 3 characters long');
                return false;
            }

            // Check for special characters in Client ID
            if (!/^[a-zA-Z0-9]+$/.test(clientId)) {
                alert('Client ID should contain only letters and numbers');
                return false;
            }

            // Confirmation dialog
            const confirmMessage = 'Are you sure you want to create a client account with the following information?\n\n' +
                                 'First Name: ' + firstName + '\n' +
                                 'Last Name: ' + lastName + '\n' +
                                 'Client ID: ' + clientId + '\n\n' +
                                 'After creation, you will need to enter the client\'s personal information.';

            if (confirm(confirmMessage)) {
                return true;
            } else {
                return false;
            }
        }

        // Auto-save form data to localStorage
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('clientCreateForm');
            const inputs = form.querySelectorAll('input[type="text"]');

            // Load saved data
            inputs.forEach(input => {
                const savedValue = localStorage.getItem('client_create_' + input.name);
                if (savedValue) {
                    input.value = savedValue;
                }
            });

            // Save data on input
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    localStorage.setItem('client_create_' + input.name, this.value);
                });
            });

            // Clear saved data on successful submit
            form.addEventListener('submit', function() {
                inputs.forEach(input => {
                    localStorage.removeItem('client_create_' + input.name);
                });
            });
        });

        // Format names as user types (capitalize first letter)
        document.getElementById('client_first_name').addEventListener('input', function(e) {
            const words = e.target.value.split(' ');
            const capitalizedWords = words.map(word => {
                return word.charAt(0).toUpperCase() + word.slice(1).toLowerCase();
            });
            e.target.value = capitalizedWords.join(' ');
        });

        document.getElementById('client_last_name').addEventListener('input', function(e) {
            const words = e.target.value.split(' ');
            const capitalizedWords = words.map(word => {
                return word.charAt(0).toUpperCase() + word.slice(1).toLowerCase();
            });
            e.target.value = capitalizedWords.join(' ');
        });

        // Format Client ID to uppercase
        document.getElementById('client_id').addEventListener('input', function(e) {
            e.target.value = e.target.value.toUpperCase();
        });
    </script>

    <style>
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-group small {
            display: block;
            margin-top: 5px;
            color: #666;
            font-size: 14px;
        }

        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .help-section {
            margin-top: 40px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .help-section h3 {
            margin-bottom: 15px;
            color: #333;
        }

        .help-section ul {
            list-style: none;
            padding: 0;
        }

        .help-section li {
            margin-bottom: 8px;
            padding-left: 20px;
            position: relative;
        }

        .help-section li:before {
            content: "•";
            position: absolute;
            left: 0;
            color: #667eea;
            font-weight: bold;
        }

        .recent-clients {
            margin-top: 40px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .recent-clients h3 {
            margin-bottom: 20px;
            color: #333;
        }

        .clients-table {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #6c757d;
            color: white;
            font-weight: bold;
        }

        tr:hover {
            background-color: #f5f5f5;
        }

        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 1rem 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav-brand h2 {
            color: white;
            margin: 0;
        }

        .nav-links {
            display: flex;
            gap: 1rem;
        }

        .nav-link {
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .nav-link:hover, .nav-link.active {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .nav-user {
            color: white;
            font-weight: bold;
        }

        @media (max-width: 768px) {
            .nav-container {
                flex-direction: column;
                gap: 1rem;
            }

            .nav-links {
                flex-wrap: wrap;
                justify-content: center;
            }

            .form-actions {
                flex-direction: column;
            }
        }
    </style>
</body>
</html>