<?php
// Include database configuration
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: cc_form.html");
    exit();
}

// Check if client data exists in session
if (!isset($_SESSION['temp_client'])) {
    header("Location: client_create.php");
    exit();
}

// Get caterer information
$caterer_id = $_SESSION['caterer_id'];
$caterer_name = $_SESSION['caterer_name'];
$client_data = $_SESSION['temp_client'];

// Handle client personal information submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $street_number = sanitize_input($_POST['street_number']);
    $street_name = sanitize_input($_POST['street_name']);
    $city = sanitize_input($_POST['city']);
    $state = sanitize_input($_POST['state']);
    $zip_code = sanitize_input($_POST['zip_code']);
    $phone_number = sanitize_input($_POST['phone_number']);
    
    // Validate input
    if (empty($street_number) || empty($street_name) || empty($city) || empty($state) || empty($zip_code) || empty($phone_number)) {
        echo "<script>alert('All fields are required. Please fill out the form completely.'); window.history.back();</script>";
        exit();
    }
    
    // Street number validation
    if (!is_numeric($street_number) || $street_number <= 0) {
        echo "<script>alert('Street number must be a positive number'); window.history.back();</script>";
        exit();
    }
    
    // Street name validation
    if (!/^[a-zA-Z0-9\s]+$/.test($street_name) || strlen($street_name) < 3) {
        echo "<script>alert('Street name must be at least 3 characters and contain only letters, numbers, and spaces'); window.history.back();</script>";
        exit();
    }
    
    // City validation
    if (!/^[a-zA-Z\s]+$/.test($city) || strlen($city) < 2) {
        echo "<script>alert('City must be at least 2 characters and contain only letters and spaces'); window.history.back();</script>";
        exit();
    }
    
    // State validation (2 letters)
    if (!/^[A-Z]{2}$/.test($state)) {
        echo "<script>alert('State must be exactly 2 uppercase letters'); window.history.back();</script>";
        exit();
    }
    
    // ZIP code validation (5 digits or 5+4 format)
    if (!/^\d{5}(-\d{4})?$/.test($zip_code)) {
        echo "<script>alert('ZIP code must be in format 12345 or 12345-6789'); window.history.back();</script>";
        exit();
    }
    
    // Phone number validation
    if (!/^(\d{3}-\d{3}-\d{4}|\(\d{3}\) \d{3}-\d{4})$/.test($phone_number)) {
        echo "<script>alert('Phone number must be in format 123-456-7890 or (123) 456-7890'); window.history.back();</script>";
        exit();
    }
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Insert client basic information
        $insert_client_sql = "INSERT INTO clients (ClientFirstName, ClientLastName, ClientIdentification) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insert_client_sql);
        $stmt->bind_param("sss", $client_data['first_name'], $client_data['last_name'], $client_data['id']);
        $stmt->execute();
        $client_db_id = $conn->insert_id;
        
        // Insert client personal information
        $insert_info_sql = "INSERT INTO client_personal_info (ClientID, ClientStreetNumber, ClientStreetName, ClientCity, ClientState, ClientZipCode, ClientPhoneNumber) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_info_sql);
        $stmt->bind_param("issssss", $client_db_id, $street_number, $street_name, $city, $state, $zip_code, $phone_number);
        $stmt->execute();
        
        // Commit transaction
        $conn->commit();
        
        // Clear session data
        unset($_SESSION['temp_client']);
        
        echo "<script>alert('Client Information Record has been created successfully!'); window.location.href='client_create.php';</script>";
        exit();
        
    } catch (Exception $e) {
        // Rollback transaction
        $conn->rollback();
        echo "<script>alert('Error creating client account. Please try again.'); window.history.back();</script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Information - Culinary Connoisseurs</title>
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
                <a href="client_create.php" class="nav-link">Create Client</a>
                <a href="logout.php" class="nav-link">Logout</a>
            </div>
            <div class="nav-user">
                <span>Welcome, <?php echo htmlspecialchars($caterer_name); ?></span>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <h1>Client Personal Information</h1>
        <p>Enter the personal information for <?php echo htmlspecialchars($client_data['first_name'] . ' ' . $client_data['last_name']); ?> (ID: <?php echo htmlspecialchars($client_data['id']); ?>)</p>

        <div class="client-summary">
            <h3>Client Summary</h3>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($client_data['first_name'] . ' ' . $client_data['last_name']); ?></p>
            <p><strong>Client ID:</strong> <?php echo htmlspecialchars($client_data['id']); ?></p>
        </div>

        <form id="clientInfoForm" method="post" action="client_information.php" onsubmit="return validateClientInfoForm()">
            
            <div class="form-row">
                <div class="form-group">
                    <label for="street_number">Street Number:</label>
                    <input type="text" id="street_number" name="street_number" required>
                    <small>Enter the street number (numbers only)</small>
                </div>

                <div class="form-group">
                    <label for="street_name">Street Name:</label>
                    <input type="text" id="street_name" name="street_name" required>
                    <small>Enter the street name (minimum 3 characters)</small>
                </div>
            </div>

            <div class="form-group">
                <label for="city">City:</label>
                <input type="text" id="city" name="city" required>
                <small>Enter the city name (minimum 2 characters, letters only)</small>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="state">State:</label>
                    <input type="text" id="state" name="state" required maxlength="2">
                    <small>Enter 2-letter state abbreviation (e.g., NJ, NY)</small>
                </div>

                <div class="form-group">
                    <label for="zip_code">ZIP Code:</label>
                    <input type="text" id="zip_code" name="zip_code" required>
                    <small>Enter ZIP code (12345 or 12345-6789)</small>
                </div>
            </div>

            <div class="form-group">
                <label for="phone_number">Phone Number:</label>
                <input type="text" id="phone_number" name="phone_number" required>
                <small>Enter phone number (123-456-7890 or (123) 456-7890)</small>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Create Client Information Record</button>
                <button type="button" class="btn btn-secondary" onclick="window.location.href='client_create.php'">Cancel</button>
            </div>
        </form>

        <div class="help-section">
            <h3>Important Information</h3>
            <ul>
                <li>All client personal information must be validated before submission</li>
                <li>State abbreviation should be 2 uppercase letters</li>
                <li>Phone number can be entered in 123-456-7890 or (123) 456-7890 format</li>
                <li>After creation, the client will be available for booking events</li>
            </ul>
        </div>
    </div>

    <script>
        function validateClientInfoForm() {
            const streetNumber = document.getElementById('street_number').value.trim();
            const streetName = document.getElementById('street_name').value.trim();
            const city = document.getElementById('city').value.trim();
            const state = document.getElementById('state').value.trim();
            const zipCode = document.getElementById('zip_code').value.trim();
            const phoneNumber = document.getElementById('phone_number').value.trim();

            // Street number validation
            if (!/^\d+$/.test(streetNumber) || streetNumber <= 0) {
                alert('Street number must be a positive number');
                return false;
            }

            // Street name validation
            if (!/^[a-zA-Z0-9\s]+$/.test(streetName) || streetName.length < 3) {
                alert('Street name must be at least 3 characters and contain only letters, numbers, and spaces');
                return false;
            }

            // City validation
            if (!/^[a-zA-Z\s]+$/.test(city) || city.length < 2) {
                alert('City must be at least 2 characters and contain only letters and spaces');
                return false;
            }

            // State validation
            if (!/^[A-Z]{2}$/.test(state)) {
                alert('State must be exactly 2 uppercase letters');
                return false;
            }

            // ZIP code validation
            if (!/^\d{5}(-\d{4})?$/.test(zipCode)) {
                alert('ZIP code must be in format 12345 or 12345-6789');
                return false;
            }

            // Phone number validation
            if (!/^(\d{3}-\d{3}-\d{4}|\(\d{3}\) \d{3}-\d{4})$/.test(phoneNumber)) {
                alert('Phone number must be in format 123-456-7890 or (123) 456-7890');
                return false;
            }

            // Confirmation dialog
            const confirmMessage = 'Are you sure you want to create the client information record with the following details?\n\n' +
                                 'Address: ' + streetNumber + ' ' + streetName + '\n' +
                                 'City: ' + city + ', ' + state + ' ' + zipCode + '\n' +
                                 'Phone: ' + phoneNumber + '\n\n' +
                                 'This will complete the client account creation process.';

            if (confirm(confirmMessage)) {
                return true;
            } else {
                return false;
            }
        }

        // Format inputs as user types
        document.getElementById('street_number').addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/\D/g, '');
        });

        document.getElementById('street_name').addEventListener('input', function(e) {
            const words = e.target.value.split(' ');
            const capitalizedWords = words.map(word => {
                return word.charAt(0).toUpperCase() + word.slice(1).toLowerCase();
            });
            e.target.value = capitalizedWords.join(' ');
        });

        document.getElementById('city').addEventListener('input', function(e) {
            const words = e.target.value.split(' ');
            const capitalizedWords = words.map(word => {
                return word.charAt(0).toUpperCase() + word.slice(1).toLowerCase();
            });
            e.target.value = capitalizedWords.join(' ');
        });

        document.getElementById('state').addEventListener('input', function(e) {
            e.target.value = e.target.value.toUpperCase().slice(0, 2);
        });

        document.getElementById('zip_code').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 5) {
                value = value.slice(0, 5) + '-' + value.slice(5, 9);
            }
            e.target.value = value;
        });

        document.getElementById('phone_number').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 6) {
                value = value.slice(0, 3) + '-' + value.slice(3, 6) + '-' + value.slice(6, 10);
            } else if (value.length >= 3) {
                value = value.slice(0, 3) + '-' + value.slice(3);
            }
            e.target.value = value;
        });
    </script>

    <style>
        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
        }

        .client-summary {
            background: #e8f4fd;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            border-left: 4px solid #667eea;
        }

        .client-summary h3 {
            margin-top: 0;
            color: #333;
        }

        .client-summary p {
            margin: 5px 0;
            color: #666;
        }

        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 25px;
        }

        .form-row .form-group {
            flex: 1;
            margin-bottom: 0;
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

            .form-row {
                flex-direction: column;
                gap: 0;
            }

            .form-actions {
                flex-direction: column;
            }
        }
    </style>
</body>
</html>