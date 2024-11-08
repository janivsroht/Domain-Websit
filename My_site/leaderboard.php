<?php
// Database connection
$con = new mysqli("localhost", "root", "rootpassword", "website");

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Query to retrieve all players’ usernames and scores in descending order of highscore
$result = $con->query("SELECT username, highscore FROM gamesiteONE ORDER BY highscore DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Leaderboard</title>
</head>
<body>
    <h1>Leaderboard</h1>
    <table border="1">
        <tr>
            <th>Rank</th>
            <th>Username</th>
            <th>Highscore</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            $rank = 1;
            while ($row = $result->fetch_assoc()) {
                echo "<tr><td>" . $rank++ . "</td><td>" . htmlspecialchars($row['username']) . "</td><td>" . $row['highscore'] . "</td></tr>";
            }
        } else {
            echo "<tr><td colspan='3'>No scores available</td></tr>";
        }
        $con->close();
        ?>
    </table>
</body>
</html>
