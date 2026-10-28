<?php
require_once "config/database.php";

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get and clean form values
    $full_name = trim($_POST["full_name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $blood_group = trim($_POST["blood_group"] ?? "");
    $gender = trim($_POST["gender"] ?? "");
    $age_input = trim($_POST["age"] ?? "");
    $city = trim($_POST["city"] ?? "");
    $last_donation_date = trim($_POST["last_donation_date"] ?? "");
    $address = trim($_POST["address"] ?? "");

    // Convert empty donation date to NULL
    $last_donation_date = ($last_donation_date !== "")
        ? $last_donation_date
        : null;

    // Allowed values
    $allowed_groups = [
        "A+", "A-", "B+", "B-",
        "AB+", "AB-", "O+", "O-"
    ];

    $allowed_genders = [
        "Male", "Female", "Other", "Prefer not to say"
    ];

    // Convert age only after checking the input
    $age = filter_var($age_input, FILTER_VALIDATE_INT);

    // Validate name
    if (!preg_match("/^[a-zA-Z ]{2,100}$/", $full_name)) {

        $message = "Name must contain only English letters and spaces (2–100 characters).";
        $message_type = "error";

    // Validate email
    } elseif (
        $email === "" ||
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        strlen($email) > 100
    ) {

        $message = "Please enter a valid email address.";
        $message_type = "error";

    // Validate Indian mobile number without country code
    } elseif (!preg_match("/^[6-9][0-9]{9}$/", $phone)) {

        $message = "Please enter a valid 10-digit Indian mobile number starting with 6, 7, 8, or 9.";
        $message_type = "error";

    // Validate blood group
    } elseif (!in_array($blood_group, $allowed_groups, true)) {

        $message = "Please select a valid blood group.";
        $message_type = "error";

    // Validate gender if supplied
    } elseif (
        $gender !== "" &&
        !in_array($gender, $allowed_genders, true)
    ) {

        $message = "Please select a valid gender option.";
        $message_type = "error";

    // Validate age
    } elseif (
        $age === false ||
        $age < 18 ||
        $age > 100
    ) {

        $message = "Donors must be between 18 and 100 years old.";
        $message_type = "error";

    // Validate city
    } elseif ($city === "" || strlen($city) > 100) {

        $message = "Please enter a valid city.";
        $message_type = "error";

    // Validate address
    } elseif ($address === "") {

        $message = "Please enter your full address.";
        $message_type = "error";

    // Validate optional donation date
    } elseif ($last_donation_date !== null) {

        $date = DateTime::createFromFormat(
            "!Y-m-d",
            $last_donation_date
        );

        $date_errors = DateTime::getLastErrors();

        $valid_date = $date !== false
            && $date->format("Y-m-d") === $last_donation_date
            && (
                $date_errors === false ||
                (
                    $date_errors["warning_count"] === 0 &&
                    $date_errors["error_count"] === 0
                )
            );

        if (
            !$valid_date ||
            $last_donation_date > date("Y-m-d")
        ) {

            $message = "Please enter a valid donation date that is not in the future.";
            $message_type = "error";

        } else {

            // Insert donor information into MySQL
            $sql = "INSERT INTO donors
                    (full_name, email, phone, blood_group, gender,
                     age, city, last_donation_date, address)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);

            if ($stmt) {

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

            } else {

                $message = "Unable to process registration right now.";
                $message_type = "error";
            }
        }

    } else {

        // Insert donor information when no donation date is supplied
        $sql = "INSERT INTO donors
                (full_name, email, phone, blood_group, gender,
                 age, city, last_donation_date, address)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);

        if ($stmt) {

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

        } else {

            $message = "Unable to process registration right now.";
            $message_type = "error";
        }
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

        <?php if ($message !== ""): ?>

            <div class="message-box <?php echo htmlspecialchars($message_type, ENT_QUOTES, 'UTF-8'); ?>">
                <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
            </div>

        <?php endif; ?>

        <form method="POST" action="register.php">

            <div class="form-grid">

                <div class="form-group">
                    <label for="full_name">Full Name *</label>

                    <input
                        type="text"
                        id="full_name"
                        name="full_name"
                        minlength="2"
                        maxlength="100"
                        pattern="[a-zA-Z ]{2,100}"
                        title="Enter 2–100 English letters and spaces only."
                        value="<?php echo htmlspecialchars($_POST['full_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="email">Email Address *</label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        maxlength="100"
                        value="<?php echo htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number *</label>

                    <input
                        type="tel"
                        id="phone"
                        name="phone"
                        pattern="[6-9][0-9]{9}"
                        minlength="10"
                        maxlength="10"
                        inputmode="numeric"
                        title="Enter a 10-digit Indian mobile number starting with 6, 7, 8, or 9. Do not include the country code."
                        value="<?php echo htmlspecialchars($_POST['phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="blood_group">Blood Group *</label>

                    <select id="blood_group" name="blood_group" required>
                        <option value="">Select Blood Group</option>

                        <?php
                        foreach ($allowed_groups as $group):
                        ?>
                            <option
                                value="<?php echo htmlspecialchars($group, ENT_QUOTES, 'UTF-8'); ?>"
                                <?php echo (($_POST['blood_group'] ?? '') === $group) ? 'selected' : ''; ?>
                            >
                                <?php echo htmlspecialchars($group, ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php endforeach; ?>

                    </select>
                </div>

                <div class="form-group">
                    <label for="gender">Gender</label>

                    <select id="gender" name="gender">
                        <option value="">Select Gender</option>

                        <?php foreach ($allowed_genders as $option): ?>
                            <option
                                value="<?php echo htmlspecialchars($option, ENT_QUOTES, 'UTF-8'); ?>"
                                <?php echo (($_POST['gender'] ?? '') === $option) ? 'selected' : ''; ?>
                            >
                                <?php echo htmlspecialchars($option, ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php endforeach; ?>

                    </select>
                </div>

                <div class="form-group">
                    <label for="age">Age *</label>

                    <input
                        type="number"
                        id="age"
                        name="age"
                        min="18"
                        max="100"
                        value="<?php echo htmlspecialchars($_POST['age'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="city">City *</label>

                    <input
                        type="text"
                        id="city"
                        name="city"
                        maxlength="100"
                        value="<?php echo htmlspecialchars($_POST['city'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="last_donation_date">Last Donation Date</label>

                    <input
                        type="date"
                        id="last_donation_date"
                        name="last_donation_date"
                        max="<?php echo date('Y-m-d'); ?>"
                        value="<?php echo htmlspecialchars($_POST['last_donation_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                    >
                </div>

                <div class="form-group full-width">
                    <label for="address">Full Address *</label>

                    <textarea
                        id="address"
                        name="address"
                        rows="3"
                        required
                    ><?php echo htmlspecialchars($_POST['address'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
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