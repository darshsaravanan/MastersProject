<link rel="stylesheet" href="static/css/style.css">
<script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
<?php
require_once(__DIR__ . "/../lib/helpers.php");
?>
<nav>
<ul class="nav">
    <li><a href="home.php">Home</a></li>
    <li><a href="collectsqs.php">PlayGame</a></li>
    <?php if (!is_logged_in()): ?>
        <li><a href="login.php">Login</a></li>
        <li><a href="register.php">Register</a></li>
    <?php endif; ?>
    <?php if(HAS_ROLE("Admin")): ?>
        <li><a href = "test_create_scores.php"> Create Score</a></li>
        <li><a href = "test_list_score.php"> List Score</a></li>
        <li><a href = "test_create_scorehistory.php"> Create Score History </a></li>
        <li><a href = "test_list_scorehistory.php"> List score History </a> </li>
    <?php endif; ?>
    <?php if (is_logged_in()): ?>
        <li><a href="profile.php">Profile</a></li>
        <li><a href="logout.php">Logout</a></li>
        <li><a href="create_competitions.php">Create COmpetition</a></li>
        <li><a href="list_competitions.php">List Competition</a></li>
        <li><a href="competition_history.php">Competition History</a></li>
        

    <?php endif; ?>
</ul>
</nav>
