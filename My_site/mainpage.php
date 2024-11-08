<?php
session_start();

// Connect to the database
$con = new mysqli("localhost", "root", "rootpassword", "website");
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Fetch username from session
$username = $_SESSION['username'] ?? 'Player';

if (isset($_POST['newScore'])) {
    $newScore = (int)$_POST['newScore'];
    // Debug: Log the incoming score
    error_log("Received newScore: " . $newScore);

    // Prepare statement to find existing high score for the user
    $stmt = $con->prepare("SELECT highscore FROM gamesiteONE WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($highscore);
    $stmt->fetch();
    $stmt->close();

    // Debug: Log the current high score
    error_log("Current highscore for $username: " . $highscore);

    // If username exists, update the high score if the new score is higher
    if ($highscore !== null) {
        if ($newScore > $highscore) {
            $updateStmt = $con->prepare("UPDATE gamesiteONE SET highscore = ? WHERE username = ?");
            $updateStmt->bind_param("is", $newScore, $username);
            $updateStmt->execute();
            $updateStmt->close();
            error_log("Updated highscore for $username to $newScore");
        } else {
            error_log("New score $newScore is not higher than current highscore $highscore");
        }
    } else {
        // If no entry exists for this username, insert a new record
        $insertStmt = $con->prepare("INSERT INTO gamesiteONE (username, highscore) VALUES (?, ?)");
        $insertStmt->bind_param("si", $username, $newScore);
        $insertStmt->execute();
        $insertStmt->close();
        error_log("Inserted new user $username with score $newScore");
    }
}

// Fetch high score to display
$stmt = $con->prepare("SELECT highscore FROM gamesiteONE WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($highscore);
$stmt->fetch();
$stmt->close();

$con->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Gamesite</title>
    <link href="https://fonts.googleapis.com/css2?family=Lobster&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="newgame.css">
</head>
<body>
<div class="game">
    <div class="score"></div>
    <div class="highScore">
        <h2><?php echo htmlspecialchars($username); ?>'s Highscore: <?php echo htmlspecialchars($highscore ?? 0); ?></h2>
    </div>
    <a href="leaderboard.php" class="leaderboard">Leaderboard</a>
    <div class="startScreen">
        <p class="ClickToStart">Click here to start the game</p>
    </div>
    <div class="gameArea"></div>
</div>

<script>
const username = "<?php echo htmlspecialchars($username); ?>";
const score = document.querySelector('.score');
const highScore = document.querySelector('.highScore');
const startScreen = document.querySelector('.startScreen');
const gameArea = document.querySelector('.gameArea');
const ClickToStart = document.querySelector('.ClickToStart');

ClickToStart.addEventListener('click', Start);
document.addEventListener('keydown', keydown);
document.addEventListener('keyup', keyup);

let keys = {
    ArrowUp: false,
    ArrowDown: false,
    ArrowLeft: false,
    ArrowRight: false,
};
let player = {
    speed: 5,
    score: 0,
    highScore: 0,
    isStart: false,
};

function keydown(e) {
    if (e.key in keys) keys[e.key] = true;
}

function keyup(e) {
    if (e.key in keys) keys[e.key] = false;
}

// Start the game
function Start() {
    gameArea.innerHTML = "";
    startScreen.classList.add('hide');
    player.isStart = true;
    player.score = 0;
    player.speed = 5;

    window.requestAnimationFrame(Play);

    // Create road lines
    for (let i = 0; i < 5; i++) {
        let roadLines = document.createElement('div');
        roadLines.setAttribute('class', 'roadLines');
        roadLines.y = (i * 140);
        roadLines.style.top = roadLines.y + "px";
        gameArea.appendChild(roadLines);
    }
    // Create opponents' cars
    for (let i = 0; i < 3; i++) {
        let Opponents = document.createElement('div');
        Opponents.setAttribute('class', 'Opponents');
        Opponents.y = ((i) * -300);
        Opponents.style.top = Opponents.y + "px";
        gameArea.appendChild(Opponents);
        Opponents.style.left = Math.floor(Math.random() * 350) + "px";
        //Opponents.style.backgroundColor = randomColor();
    }

    // Create player's car
    let car = document.createElement('div');
    car.setAttribute('class', 'car');
    gameArea.appendChild(car);
    player.x = car.offsetLeft;
    player.y = car.offsetTop;
}

//Generate random color
function randomColor() {
   function c() {
       let hex = Math.floor(Math.random() * 256).toString(16);
       return ("0" + String(hex)).substr(-2);
   }
   return "#" + c() + c() + c();
}

// Play the game
function Play() {
    let car = document.querySelector('.car');
    let road = gameArea.getBoundingClientRect();
    if (player.isStart) {
        moveLines();
        moveOpponents(car);

        // Move player's car
        if (keys.ArrowUp && player.y > (road.top + 70)) player.y -= player.speed;
        if (keys.ArrowDown && player.y < (road.bottom - 75)) player.y += player.speed;
        if (keys.ArrowLeft && player.x > 0) player.x -= player.speed;
        if (keys.ArrowRight && player.x < (road.width - 50)) player.x += player.speed;

        car.style.top = player.y + "px";
        car.style.left = player.x + "px";

        // Update score and high score
        player.score++;
        player.speed += 0.01;

        if (player.highScore < player.score) {
            player.highScore++;
            highScore.innerHTML = "Session HighScore: " + (player.highScore - 1);
            highScore.style.top = "80px";
        }

        highScore.innerHTML = "Session HighScore: " + (player.highScore - 1);
        score.innerHTML = username + "'s Score: " + (player.score - 1);

        // Send score to the server every few seconds
        if (player.score % 50 === 0) { // Adjust the condition as needed
            submitScore(player.score);
        }

        window.requestAnimationFrame(Play);
    }
}

function moveLines() {
    let roadLines = document.querySelectorAll('.roadLines');
    roadLines.forEach(function (item) {
        if (item.y >= 700) item.y -= 700;
        item.y += player.speed;
        item.style.top = item.y + "px";
    });
}

function moveOpponents(car) {
    let Opponents = document.querySelectorAll('.Opponents');
    Opponents.forEach(function (item) {
        if (isCollide(car, item)) {
            endGame();
        }
        if (item.y >= 700) {
            item.y = -300;
            item.style.left = Math.floor(Math.random() * 350) + "px";
        }
        item.y += player.speed;
        item.style.top = item.y + "px";
    });
}

// Check for collision
function isCollide(car, opponent) {
    let carRect = car.getBoundingClientRect();
    let opponentRect = opponent.getBoundingClientRect();

    return !(
        carRect.top > opponentRect.bottom ||
        carRect.bottom < opponentRect.top ||
        carRect.left > opponentRect.right ||
        carRect.right < opponentRect.left
    );
}

// End the game
function endGame() {
    player.isStart = false;
    player.speed = 5;
    startScreen.classList.remove('hide');

    let car = document.querySelector('.car');
    car.style.backgroundImage = "url('skull.png')";

    // Send the final score to the server
    submitScore(player.score);
}

// Send score to the server
function submitScore(newScore) {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "mainpage.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.send("newScore=" + encodeURIComponent(newScore));

    xhr.onload = function () {
        if (xhr.status === 200) {
            console.log("Score submitted successfully!");
        } else {
            console.error("Failed to submit score. Status:", xhr.status);
        }
    };
}
</script>
</body>
</html>
