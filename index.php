<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LifeLink - Blood Donor Network</title>

    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<!-- Navigation Bar -->
<header class="navbar">
    <a href="index.php" class="logo">
        <span class="logo-icon">♥</span>
        LifeLink
    </a>

    <nav>
        <a href="index.php" class="active">Home</a>
        <a href="#how-it-works">How It Works</a>
        <a href="find-blood.php">Find Blood</a>
        <a href="register.php" class="nav-button">Become a Donor</a>
    </nav>
</header>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-content">
        <span class="hero-label">🩸 EVERY DROP COUNTS</span>

        <h1>Your Blood Can<br>Save a <span>Life.</span></h1>

        <p>
            Connect with blood donors in your community.
            A simple act of kindness today can give someone
            a lifetime of tomorrows.
        </p>

        <div class="hero-buttons">
            <a href="find-blood.php" class="btn btn-light">
                Find Blood &rarr;
            </a>

            <a href="register.php" class="btn btn-outline">
                ♥ Become a Donor
            </a>
        </div>
    </div>
</section>

<!-- How It Works -->
<section class="how-section" id="how-it-works">

    <div class="section-heading">
        <span class="section-label">SIMPLE PROCESS</span>
        <h2>How LifeLink Works</h2>
        <p>Three simple steps to make a life-saving difference.</p>
    </div>

    <div class="steps">

        <div class="step-card">
            <div class="step-icon">👤</div>
            <h3>Register as a Donor</h3>
            <p>
                Sign up and share your blood group and
                location to help people find you.
            </p>
        </div>

        <div class="step-card">
            <div class="step-icon">🔍</div>
            <h3>Find Blood</h3>
            <p>
                Search for registered donors by blood
                group and city.
            </p>
        </div>

        <div class="step-card">
            <div class="step-icon">♥</div>
            <h3>Save a Life</h3>
            <p>
                Connect with a matching donor and
                coordinate the next steps.
            </p>
        </div>

    </div>
</section>

<!-- Blood Groups -->
<section class="blood-section">

    <div class="section-heading">
        <span class="section-label">BE A LIFESAVER</span>
        <h2>All Blood Groups Matter</h2>
        <p>Every blood group can make a difference.</p>
    </div>

    <div class="blood-groups">
        <?php
        $groups = ["A+", "A-", "B+", "B-", "AB+", "AB-", "O+", "O-"];

        foreach ($groups as $group) {
            echo '<a class="blood-card" href="find-blood.php?blood_group='
                . urlencode($group) . '">';
            echo '<span class="blood-drop">♥</span>';
            echo '<strong>' . htmlspecialchars($group) . '</strong>';
            echo '<span class="blood-caption">Find Donors</span>';
            echo '</a>';
        }
        ?>
    </div>

</section>

<!-- Call to Action -->
<section class="cta-section">
    <h2>Be Someone's Hero Today.</h2>
    <p>Your small act of kindness could mean everything to someone.</p>
    <a href="register.php" class="btn btn-light">Register as a Donor &rarr;</a>
</section>

<!-- Footer -->
<footer class="footer">
    <div class="footer-content">
        <div>
            <a href="index.php" class="footer-logo">♥ LifeLink</a>
            <p>Connecting hearts. Saving lives.</p>
        </div>

        <div class="footer-links">
            <a href="index.php">Home</a>
            <a href="find-blood.php">Find Blood</a>
            <a href="register.php">Become a Donor</a>
        </div>
    </div>

    <div class="footer-bottom">
        &copy; <?php echo date("Y"); ?> LifeLink. Every drop counts.
    </div>
</footer>

<script src="js/script.js"></script>

</body>
</html>