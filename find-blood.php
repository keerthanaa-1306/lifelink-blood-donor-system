<?php
require_once "config/database.php";

$blood_group = "";
$city = "";
$donors = [];

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    $blood_group = $_GET["blood_group"] ?? "";
    $city = trim($_GET["city"] ?? "");

    $sql = "SELECT * FROM donors WHERE 1=1";
    $types = "";
    $values = [];

    if ($blood_group != "") {
        $sql .= " AND blood_group = ?";
        $types .= "s";
        $values[] = $blood_group;
    }

    if ($city != "") {
        $sql .= " AND city LIKE ?";
        $types .= "s";
        $values[] = "%" . $city . "%";
    }

    $sql .= " ORDER BY created_at DESC";

    $stmt = $conn->prepare($sql);

    if (!empty($values)) {
        $stmt->bind_param($types, ...$values);
    }

    $stmt->execute();

    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $donors[] = $row;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Find Blood Donors - LifeLink</title>

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

<section class="find-hero">
    <h1>Find Compatible Donors</h1>
    <p>Search for registered blood donors by blood group and city.</p>
</section>

<section class="find-section">

    <form method="GET" action="find-blood.php" class="search-form">

        <div class="form-group">
            <label for="blood_group">Blood Group</label>

            <select name="blood_group" id="blood_group">
                <option value="">Any Blood Group</option>

                <?php
                $groups = ["A+", "A-", "B+", "B-", "AB+", "AB-", "O+", "O-"];

                foreach ($groups as $group) {
                    $selected = ($blood_group == $group) ? "selected" : "";

                    echo '<option value="' . htmlspecialchars($group) . '" '
                        . $selected . '>'
                        . htmlspecialchars($group)
                        . '</option>';
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label for="city">City</label>

            <input
                type="text"
                id="city"
                name="city"
                placeholder="Enter city"
                value="<?php echo htmlspecialchars($city); ?>"
            >
        </div>

        <button type="submit" class="form-submit search-button">
            Search Donors
        </button>

    </form>

    <div class="results-heading">
        <h2>Available Donors</h2>
        <p>
            <?php echo count($donors); ?>
            donor(s) found.
        </p>
    </div>

    <?php if (count($donors) > 0): ?>

        <div class="donor-grid">

            <?php foreach ($donors as $donor): ?>

                <div class="donor-card">

                    <div class="donor-card-top">
                        <span class="blood-badge">
                            <?php echo htmlspecialchars($donor["blood_group"]); ?>
                        </span>

                        <span class="availability-badge">
                            Registered
                        </span>
                    </div>

                    <h3>
                        <?php echo htmlspecialchars($donor["full_name"]); ?>
                    </h3>

                    <p>
                        📍 <?php echo htmlspecialchars($donor["city"]); ?>
                    </p>

                    <p>
                        📞 <?php echo htmlspecialchars($donor["phone"]); ?>
                    </p>

                    <p>
                        ✉️ <?php echo htmlspecialchars($donor["email"]); ?>
                    </p>

                    <p>
                        Age: <?php echo (int) $donor["age"]; ?>
                    </p>

                    <a
                        class="contact-button"
                        href="contact.php?id=<?php echo (int) $donor["id"]; ?>"
                    >
                        Contact Donor
                    </a>

                </div>

            <?php endforeach; ?>

        </div>

    <?php else: ?>

        <div class="no-results">
            <h3>No matching donors found</h3>
            <p>Try another blood group or city.</p>
        </div>

    <?php endif; ?>

</section>

<footer class="footer">
    <div class="footer-bottom">
        &copy; <?php echo date("Y"); ?> LifeLink. Every drop counts.
    </div>
</footer>

</body>
</html>