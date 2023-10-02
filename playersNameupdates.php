<?php 
// Database connection 
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "royacomn_football_data"; 

$conn = new mysqli($servername, $username, $password, $dbname); 

if ($conn->connect_error) { 
    die("Connection failed: " . $conn->connect_error); 
} 

function getPlayerRating($playerID, $conn) { 
    $playerRating = 0; 
    $sql = "SELECT Rating FROM playerratings WHERE PlayerID = ?"; 
    $stmt = $conn->prepare($sql); 
    if ($stmt) { 
        $stmt->bind_param("i", $playerID);
        $stmt->execute(); 
        $stmt->bind_result($playerRating); 
        $stmt->fetch(); 
        $stmt->close(); 
    } 
    return $playerRating; 
} 

// Fetch team data
$teams = [];
$sql = "SELECT TeamID, TeamName FROM teams";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $teams[$row['TeamID']] = $row['TeamName'];
}

// Fetch player data
$sql = "SELECT players.PlayerID, players.PlayerName, players.TeamID FROM players";
$result = $conn->query($sql);

$playerRatings = [];
while ($row = $result->fetch_assoc()) { 
    $playerRatings[$row['PlayerID']] = [
        'name' => $row['PlayerName'],
        'rating' => getPlayerRating($row['PlayerID'], $conn),
        'team' => $teams[$row['TeamID']]
    ];
}
?>
<!DOCTYPE html> 
<html> 
<head> 
    <title>Select Players</title> 
    <style>
        body {
            background-color: #222;
            color: white;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        h1 {
            color: #e67e22;
            text-align: center;
        }

        .player-list {
            background-color: #333;
            border: 2px solid #e67e22;
            border-radius: 5px;
            padding: 10px;
            margin: 20px 0;
        }

        .player-item {
            display: flex;
            align-items: center;
            margin: 5px 0;
        }

        .player-item input[type="checkbox"] {
            margin-right: 10px;
        }

        #select-players-btn {
            background-color: #e67e22;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            display: block;
            margin: 20px auto;
        }

        #select-players-btn:hover {
            background-color: #ff9632;
        }

        ul {
            list-style-type: none;
            padding: 0;
        }

        .popup {
            background-color: #333;
            color: white;
            border: 2px solid #e67e22;
            border-radius: 5px;
            padding: 20px;
            margin: 20px auto;
            max-width: 400px;
        }
    </style>
</head> 
<body> 
<div class="container">
    <h1>Select Players</h1>
    <?php foreach ($teams as $teamID => $teamName): ?> 
        <div class="player-list" id="<?= $teamName ?>-list"> 
            <h3><?= $teamName ?></h3> 
            <?php foreach ($playerRatings as $playerID => $playerInfo): 
                if ($playerInfo['team'] === $teamName): ?>
                    <div class="player-item"> 
                        <input type="checkbox" onclick="handleCheckboxClick(this, '<?= $playerID ?>', '<?= $teamName ?>');" /> 
                        <span><?= $playerInfo['name'] ?></span> 
                    </div> 
                <?php endif;
            endforeach; ?> 
        </div> 
    <?php endforeach; ?> 

    <button id="select-players-btn">Select Players</button> 

    <script>
        const teamSelections = {};

        function handleCheckboxClick(checkbox, playerID, teamName) {
            if (!teamSelections[teamName]) {
                teamSelections[teamName] = [];
            }
            if (checkbox.checked) {
                if (teamSelections[teamName].length < 16) {
                    teamSelections[teamName].push(playerID);
                } else {
                    alert('You can only select up to 16 players from ' + teamName + '.');
                    checkbox.checked = false;
                }
            } else {
                const index = teamSelections[teamName].indexOf(playerID);
                if (index > -1) {
                    teamSelections[teamName].splice(index, 1);
                }
            }
        }

        document.getElementById('select-players-btn').addEventListener('click', () => {
            let allTeamsValid = true;
            for (let team in teamSelections) {
                if (teamSelections[team].length < 11 || teamSelections[team].length > 16) {
                    alert('Please select between 11 and 16 players from ' + team + '.');
                    allTeamsValid = false;
                    break;
                }
            }
            if (allTeamsValid) {
                displayPlayerRatingsInNewWindow();
            }
        });

        function displayPlayerRatingsInNewWindow() {
            const playerRatings = <?= json_encode($playerRatings) ?>;
            let displayContent = `
                <html>
                <head>
                    <title>Player Ratings</title>
                    <style>
                        body {
                            background-color: #333;
                            color: white;
                            font-family: Arial, sans-serif;
                            margin: 0;
                            padding: 20px;
                        }

                        h2 {
                            color: #e67e22;
                            text-align: center;
                        }

                        h3 {
                            color: #e67e22;
                        }

                        p {
                            margin: 5px 0;
                        }

                        strong {
                            color: #e67e22;
                        }

                        hr {
                            border: 1px solid #e67e22;
                            margin-top: 10px;
                        }
                    </style>
                </head>
                <body>
                <h2>Player Ratings</h2>
            `;

            for (let team in teamSelections) {
                displayContent += '<h3>' + team + '</h3>';
                let overallTeamRating = 0;
                let count = 0;

                for (let playerID of teamSelections[team]) {
                    const playerName = playerRatings[playerID] ? playerRatings[playerID].name : 'Unknown';
                    const playerRatingValue = playerRatings[playerID] ? playerRatings[playerID].rating : 'N/A';
                    displayContent += '<p>' + playerName + ': ' + playerRatingValue + '</p>';
                    overallTeamRating += playerRatingValue;
                    count++;
                }

                if (count > 0) {
                    overallTeamRating /= count;
                }
                displayContent += '<p><strong>' + team + ' Overall Rating: ' + overallTeamRating.toFixed(2) + '</strong></p>';
                displayContent += '<hr>'; 
            }

            displayContent += '</body></html>';

            const newWindow = window.open("", "Player Ratings", "width=400,height=600");
            newWindow.document.write(displayContent);
        }
    </script>
</div>
</body>
</html>
