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

$sql = "SELECT players.PlayerID, players.PlayerName, teams.TeamName FROM players INNER JOIN teams ON players.TeamID = teams.TeamID"; 
$result = $conn->query($sql); 

$teams = []; 
while($row = $result->fetch_assoc()) { 
    $teams[$row['TeamName']][] = [
        'PlayerID' => $row['PlayerID'], 
        'PlayerName' => $row['PlayerName'] 
    ]; 
} 

$playerRatings = [];
foreach ($teams as $teamName => $players) { 
    foreach ($players as $playerInfo) { 
        $playerRatings[$playerInfo['PlayerID']] = [
            'name' => $playerInfo['PlayerName'],
            'rating' => getPlayerRating($playerInfo['PlayerID'], $conn)
        ];
    } 
}
?>

<!DOCTYPE html> 
<html> 
<head> 
    <title>Select Players</title> 
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Oswald', sans-serif;
            background-color: #f4f4f4;
            color: #333;
            padding: 20px;
        }

        h3 {
            color: #007f00;
            border-bottom: 2px solid #007f00;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .team {
            margin-bottom: 40px;
        }

        .player-list {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .player-item {
            flex: 0 0 calc(50% - 10px);  /* Make it take half the space minus the gap */
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 40px;
            height: 20px;
            vertical-align: middle;
            margin-right: 10px;
        }

        .switch input {
            display: none;
        }

        .slider {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: 0.4s;
            border-radius: 20px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 2px;
            bottom: 2px;
            background-color: white;
            transition: 0.4s;
            border-radius: 50%;
        }

        input:checked + .slider {
            background-color: #007f00;
        }

        input:checked + .slider:before {
            transform: translateX(20px);
        }

        button {
            padding: 10px 20px;
            font-size: 18px;
            background-color: #007f00;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
        }

        button:hover {
            background-color: #005a00;
        }

        /* Popup Window Styles */
        pre {
            font-family: 'Oswald', sans-serif;
            padding: 20px;
            background-color: #f4f4f4;
            color: #333;
        }
    </style>
</head> 
<body> 
<?php foreach ($teams as $teamName => $players): ?> 
    <div class="team">
        <h3><?= $teamName ?></h3>
        <div class="player-list"> 
            <?php foreach ($players as $playerInfo): ?> 
                <div class="player-item"> 
                    <label class="switch">
                        <input type="checkbox" onclick="handleCheckboxClick(this, '<?= $playerInfo['PlayerID'] ?>', '<?= $teamName ?>');">
                        <span class="slider"></span>
                    </label>
                    <span><?= $playerInfo['PlayerName'] ?></span> 
                </div> 
            <?php endforeach; ?> 
        </div>
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
        let displayContent = '<!DOCTYPE html><html><head><style>';
        displayContent += `
            body {
                font-family: 'Oswald', sans-serif;
                background-color: #f4f4f4;
                color: #333;
                padding: 20px;
            }
            h2, h3 {
                color: #007f00;
                border-bottom: 2px solid #007f00;
                padding-bottom: 10px;
            }
            hr {
                margin-top: 20px;
                margin-bottom: 20px;
                border: 0;
                height: 1px;
                background-color: #ccc;
            }
        `;
        displayContent += '</style></head><body><h2>Player Ratings</h2>';

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
</body>
</html>
