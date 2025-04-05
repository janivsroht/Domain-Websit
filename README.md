# HTML Car Game
Car Racing Game with High Scores

This is a fun car racing game where you can play, score points, and try to beat your own high score! It’s built with HTML, CSS, JavaScript, and PHP, and your high scores get saved in a database, so you can track your progress over time.

Features

Simple Racing Gameplay: Use the arrow keys to drive your car and dodge other cars.
High Score Tracking: Your best score is saved, so you can try to beat it each time you play.
Leaderboard: See who’s got the highest scores on the leaderboard (if you’ve set it up).
Easy to Play: Basic controls and a simple design to get you started quickly.

What You Need
To run this game, you’ll need:

A server setup with PHP and MySQL (like XAMPP, WAMP, or LAMP).
MySQL database to store the high scores.
Setup Instructions
Download the Files: Download all the files in this project.

Set Up the Database:

Open MySQL and create a database called website.
Inside that database, create a table called gamesiteONE with the following setup:

sql
Copy code

CREATE TABLE gamesiteONE (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL,
  highscore INT DEFAULT 0
);
Update the database details in mainpage.php (username, password) to match your setup:
php
Copy code
$con = new mysqli("localhost", "root", "rootpassword", "website");
Session Login:

The game uses a session variable username to keep track of the player.

You can set $_SESSION['username'] in index.php for testing if you don't have a login setup yet.

How It Works

index.php: 
This is the landing page where you enter your username.

mainpage.php: This is the game page where you actually play. It also tracks and updates your high score.

leaderboard.php: (Optional) A page to view the leaderboard with all players' high scores.

newgame.css: Styling for the game.

How to Play
Start the Game:

Open index.php in your browser to enter a username and start your session.
After you submit your username, you'll be directed to mainpage.php, where the game is ready to go.

Controls:

Click on "Click here to start the game" to begin.
Use the arrow keys to control your car:

Up Arrow: Move forward

Down Arrow: Slow down

Left Arrow: Move left

Right Arrow: Move right

Scoring:

Your score goes up as you keep playing.
The session high score updates if your current score beats it.
When the game ends, it checks if your new high score is higher than the one in the database. If it is, it updates the database with your new high score.

Check the Leaderboard:

Click on Leaderboard (if you've set up leaderboard.php) to see everyone’s high scores.
Code Basics

HTML, CSS, JavaScript: Handles the game visuals and controls.

PHP and MySQL: Connects to a database to save and update high scores.

Common Issues

Database Connection Errors: Double-check your MySQL credentials and make sure the database and table are set up correctly.

Session Issues: Ensure $_SESSION['username'] is properly set. If you’re testing locally, you might want to set it manually.
High Score Not Updating: Make sure the JavaScript function submitScore is working and sending scores to mainpage.php. You can check the console for errors.

Ideas for Improvement

Add levels of difficulty by making opponents faster or more numerous.
Set up a real login system.
Add more visuals, like better car and road graphics.
Add sound effects and animations.
Hope you enjoy the game! Let us know if you run into any issues or have ideas to make it even better.

