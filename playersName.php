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
</head> 
<body> 
<?php foreach ($teams as $teamName => $players): ?> 
    <div class="player-list" id="<?= $teamName ?>-list"> 
        <h3><?= $teamName ?></h3> 
        <?php foreach ($players as $playerInfo): ?> 
            <div class="player-item"> 
                <input type="checkbox" onclick="handleCheckboxClick(this, '<?= $playerInfo['PlayerID'] ?>', '<?= $teamName ?>');" /> 
                <span><?= $playerInfo['PlayerName'] ?></span> 
            </div> 
        <?php endforeach; ?> 
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
        let displayContent = '<h2>Player Ratings</h2>';

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

        const newWindow = window.open("", "Player Ratings", "width=400,height=600");
        newWindow.document.write(displayContent);
    }
</script>
</body>
</html>
