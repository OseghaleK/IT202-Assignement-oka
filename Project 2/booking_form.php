<?php

require_once 'config.php';


if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: cc_form.html");
    exit();
}


$caterer_id = $_SESSION['caterer_id'];
$caterer_name = $_SESSION['caterer_name'];


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $client_id = sanitize_input($_POST['client_id']);
    $event_date = sanitize_input($_POST['event_date']);
    $food_order = sanitize_input($_POST['food_order']);
    

    if (empty($client_id) || empty($event_date) || empty($food_order)) {
        echo "<script>alert('All fields are required. Please fill out the form completely.'); window.history.back();</script>";
        exit();
    }
    
    
    $client_check_sql = "SELECT * FROM clients WHERE ClientIdentification = ?";
    $stmt = $conn->prepare($client_check_sql);
    $stmt->bind_param("s", $client_id);
    $stmt->execute();
    $client_result = $stmt->get_result();
    
    if ($client_result->num_rows == 0) {
        echo "<script>
            if(confirm('Client does not exist in the database. Would you like to create a new client account?')) {
                window.location.href='client_create.php';
            } else {
                window.location.href='booking_form.php';
            }
        </script>";
        exit();
    }
    
    $catering_id = generate_catering_id();
    

    $client_data = $client_result->fetch_assoc();
    $actual_client_id = $client_data['ClientID'];
    

    $insert_sql = "INSERT INTO catering_info (CateringID, ClientID, CatererID, EventDate, FoodOrder, Status) VALUES (?, ?, ?, ?, ?, 'booked')";
    $stmt = $conn->prepare($insert_sql);
    $stmt->bind_param("iiiss", $catering_id, $actual_client_id, $caterer_id, $event_date, $food_order);
    
    if ($stmt->execute()) {
        echo "<script>alert('Catering event has been successfully booked! Your Catering ID is: " . $catering_id . "'); window.location.href='booking_form.php';</script>";
        exit();
    } else {
        echo "<script>alert('Error booking the catering event. Please try again.'); window.history.back();</script>";
        exit();
    }
    
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Client Catering Event - Culinary Connoisseurs</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <h2>Culinary Connoisseurs</h2>
            </div>
            <div class="nav-links">
                <a href="dashboard.php" class="nav-link">Dashboard</a>
                <a href="search_caterer.php" class="nav-link">Search Account</a>
                <a href="booking_form.php" class="nav-link active">Book Event</a>
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

    <div class="container">
        <h1>Book a Client's Catering Event</h1>
        <p>Fill in the client information and event details below</p>

        <form id="bookingForm" method="post" action="booking_form.php" onsubmit="return validateBookingForm()">
            <div class="form-group">
                <label for="client_id">Client ID:</label>
                <input type="text" id="client_id" name="client_id" required>
                <small>Enter the client's identification number</small>
            </div>

            <div class="form-group">
                <label for="event_date">Date of Event:</label>
                <input type="date" id="event_date" name="event_date" required>
                <small>Select the date for the catering event</small>
            </div>

            <div class="form-group">
                <label for="food_order">Food Order:</label>
                <textarea id="food_order" name="food_order" rows="6" required placeholder="Describe the food order in detail..."></textarea>
                <small>List all food items and quantities required for the event</small>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Book Catering Event</button>
                <button type="button" class="btn btn-secondary" onclick="window.location.href='dashboard.php'">Cancel</button>
            </div>
        </form>

        <div class="help-section">
            <h3>Need Help?</h3>
            <ul>
                <li><a href="client_create.php">Create a new client account</a></li>
                <li><a href="search_caterer.php">View existing clients</a></li>
                <li><a href="dashboard.php">Return to dashboard</a></li>
            </ul>
        </div>
    </div>

    <script>
        function validateBookingForm() {
            const clientId = document.getElementById('client_id').value.trim();
            const eventDate = document.getElementById('event_date').value;
            const foodOrder = document.getElementById('food_order').value.trim();

            // Client ID validation
            if (clientId.length < 3) {
                alert('Client ID must be at least 3 characters long');
                return false;
            }

            // Event date validation
            if (!eventDate) {
                alert('Please select an event date');
                return false;
            }

            const selectedDate = new Date(eventDate);
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            if (selectedDate < today) {
                alert('Event date cannot be in the past');
                return false;
            }

            // Food order validation
            if (foodOrder.length < 10) {
                alert('Please provide a detailed food order (minimum 10 characters)');
                return false;
            }

            // Confirmation dialog
            const confirmMessage = 'Are you sure you want to continue booking this catering event?\n\n' +
                                 'Client ID: ' + clientId + '\n' +
                                 'Event Date: ' + eventDate + '\n' +
                                 'Food Order: ' + foodOrder.substring(0, 100) + (foodOrder.length > 100 ? '...' : '');

            if (confirm(confirmMessage)) {
                return true;
            } else {
                return false;
            }
        }

       
        document.addEventListener('DOMContentLoaded', function() {
            const eventDateInput = document.getElementById('event_date');
            const today = new Date().toISOString().split('T')[0];
            eventDateInput.setAttribute('min', today);
        });

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('bookingForm');
            const inputs = form.querySelectorAll('input, textarea');

            inputs.forEach(input => {
                const savedValue = localStorage.getItem('booking_' + input.name);
                if (savedValue) {
                    input.value = savedValue;
                }
            });

       
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    localStorage.setItem('booking_' + input.name, this.value);
                });
            });

        // Clear saved data on successful submit
            form.addEventListener('submit', function() {
                inputs.forEach(input => {
                    localStorage.removeItem('booking_' + input.name);
                });
            });
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

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 120px;
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
        }

        .help-section a {
            color: #667eea;
            text-decoration: none;
        }

        .help-section a:hover {
            text-decoration: underline;
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