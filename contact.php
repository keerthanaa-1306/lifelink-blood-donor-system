<?php
require_once "config/database.php";

$donor = null;
$message = "";

if (isset($_GET["id"]) && is_numeric($_GET["id"])) {

    $id = (int) $_GET["id"];

    $sql = "SELECT id, full_name, email, phone, blood_group,
                   age, city
            FROM donors
            WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $result = $stmt->get_result();
    $donor = $result->fetch_assoc();

    $stmt->close();

} else {
    $message = "Invalid donor information.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Contact Donor - LifeLink</title>

    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header class="navbar">
    <a href="index.php" class="logo">
        <span class="logo-icon">♥</span> LifeLink
    </a>

    <nav>
        <a href="index.php">Home</a>
        <a href="find-blood.php" class="active">Find Blood</a>
        <a href="register.php" class="nav-button">Become a Donor</a>
    </nav>
</header>

<section class="contact-section">

    <div class="contact-heading">
        <span class="section-label">LIFELINK DONOR NETWORK</span>
        <h1>Contact Donor</h1>
        <p>Connect with a registered donor.</p>
    </div>

    <div class="contact-card">

        <?php if ($donor): ?>

            <div class="contact-blood-group">
                <?php echo htmlspecialchars($donor["blood_group"]); ?>
            </div>

            <h2>
                <?php echo htmlspecialchars($donor["full_name"]); ?>
            </h2>

            <p>
                📍 <?php echo htmlspecialchars($donor["city"]); ?>
            </p>

            <p>
                Age: <?php echo (int) $donor["age"]; ?>
            </p>

            <div class="contact-details">

                <div>
                    <strong>Phone Number</strong>
                    <p>
                        <?php echo htmlspecialchars($donor["phone"]); ?>
                    </p>
                </div>

                <div>
                    <strong>Email Address</strong>
                    <p>
                        <?php echo htmlspecialchars($donor["email"]); ?>
                    </p>
                </div>

            </div>

            <div class="contact-actions">

                <a
                    class="contact-action-button"
                    href="tel:<?php echo htmlspecialchars($donor["phone"]); ?>"
                >
                    Call Donor
                </a>

                <a
                    class="contact-action-button secondary"
                    href="mailto:<?php echo htmlspecialchars($donor["email"]); ?>"
                >
                    Email Donor
                </a>

            </div>

            <p class="contact-note">
                Please confirm availability directly with the donor.
                Blood donation must be coordinated through an appropriate
                medical professional or blood bank.
            </p>

        <?php else: ?>

            <h2>Donor Not Found</h2>

            <p>
                <?php
                echo htmlspecialchars(
                    $message ?: "We couldn't find a donor with that ID."
                );
                ?>
            </p>

        <?php endif; ?>

        <a href="find-blood.php" class="back-button">
            &larr; Back to Find Blood
        </a>

    </div>

</section>

<footer class="footer">
    <div class="footer-bottom">
        &copy; <?php echo date("Y"); ?> LifeLink. Every drop counts.
    </div>
</footer>

</body>
</html>