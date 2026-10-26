<?php
require_once "config/database.php";

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $full_name = trim($_POST["full_name"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $blood_group = $_POST["blood_group"];
    $gender = $_POST["gender"];
    $age = (int) $_POST["age"];
    $city = trim($_POST["city"]);
    $last_donation_date = !empty($_POST["last_donation_date"])
        ? $_POST["last_donation_date"] : null;
    $address = trim($_POST["address"]);

    $allowed_groups = ["A+", "A-", "B+", "B-", "AB+", "AB-", "O+", "O-"];

    if (
        $full_name == "" ||
        $email == "" ||
        $phone == "" ||
        $city == "" ||
        $address == "" ||
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        !in_array($blood_group, $allowed_groups, true) ||
        $age < 18 ||
        $age > 100
    ) {
        $message = "Please enter valid information. Donors must be at least 18 years old.";
        $message_type = "error";
    } else {

        $sql = "INSERT INTO donors
                (full_name, email, phone, blood_group, gender,
                 age, city, last_donation_date, address)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "sssssisss",
            $full_name,
            $email,
            $phone,
            $blood_group,
            $gender,
            $age,
            $city,
            $last_donation_date,
            $address
        );

        if ($stmt->execute()) {
            $message = "Registration successful! Thank you for becoming a donor.";
            $message_type = "success";
        } else {
            $message = "Registration failed. Please try again.";
            $message_type = "error";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Become a Donor - LifeLink</title>

    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header class="navbar">
    <a href="index.php" class="logo">
        <span class="logo-icon">♥</span> LifeLink
    </a>

    <nav>
        <a href="index.php">Home</a>
        <a href="index.php#how-it-works">How It Works</a>
        <a href="find-blood.php">Find Blood</a>
        <a href="register.php" class="nav-button">Become a Donor</a>
    </nav>
</header>

<section class="form-section">

    <div class="form-heading">
        <span class="section-label">MAKE A DIFFERENCE</span>
        <h1>Become a Blood Donor</h1>
        <p>Your registration could help save someone's life.</p>
    </div>

    <div class="form-container">

        <?php if ($message != ""): ?>
            <div class="message-box <?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="register.php">

            <div class="form-grid">

                <div class="form-group">
                    <label for="full_name">Full Name *</label>
                    <input type="text" id="full_name" name="full_name"
                           maxlength="100" required>
                </div>

                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" id="email" name="email"
                           maxlength="100" required>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number *</label>
                    <input type="tel" id="phone" name="phone"
                           maxlength="15" required>
                </div>

                <div class="form-group">
                    <label for="blood_group">Blood Group *</label>
                    <select id="blood_group" name="blood_group" required>
                        <option value="">Select Blood Group</option>
                        <option>A+</option>
                        <option>A-</option>
                        <option>B+</option>
                        <option>B-</option>
                        <option>AB+</option>
                        <option>AB-</option>
                        <option>O+</option>
                        <option>O-</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="gender">Gender</label>
                    <select id="gender" name="gender">
                        <option value="">Select Gender</option>
                        <option>Male</option>
                        <option>Female</option>
                        <option>Other</option>
                        <option>Prefer not to say</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="age">Age *</label>
                    <input type="number" id="age" name="age"
                           min="18" max="100" required>
                </div>

                <div class="form-group">
                    <label for="city">City *</label>
                    <input type="text" id="city" name="city"
                           maxlength="100" required>
                </div>

                <div class="form-group">
                    <label for="last_donation_date">Last Donation Date</label>
                    <input type="date" id="last_donation_date"
                           name="last_donation_date">
                </div>

                <div class="form-group full-width">
                    <label for="address">Full Address *</label>
                    <textarea id="address" name="address"
                              rows="3" required></textarea>
                </div>

            </div>

            <button type="submit" class="form-submit">
                ♥ Register as Donor
            </button>

        </form>

        <p class="form-note">
            Your information will be used to help people find matching donors.
        </p>

    </div>

</section>

<footer class="footer">
    <div class="footer-bottom">
        &copy; <?php echo date("Y"); ?> LifeLink. Every drop counts.
    </div>
</footer>

<script src="js/script.js"></script>

</body>
</html>