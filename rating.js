// Sample player data for Team A and Team B (you can replace this with your own data)
const teamAPlayers = [];
const teamBPlayers = [];

// Generate 24 players for Team A
for (let i = 1; i <= 24; i++) {
    teamAPlayers.push({ name: `A${i}` });
}

// Generate 24 players for Team B
for (let i = 1; i <= 24; i++) {
    teamBPlayers.push({ name: `B${i}` });
}

// Get the player list containers for Team A and Team B
const teamAList = document.getElementById('team-a-list');
const teamBList = document.getElementById('team-b-list');

// Get the selected players list containers
const selectedPlayersListA = document.getElementById('selected-players-a');
const selectedPlayersListB = document.getElementById('selected-players-b');

// Function to create a player item with checkbox
function createPlayerItem(player, team) {
    const playerItem = document.createElement('div');
    playerItem.className = 'player-item';

    const checkbox = document.createElement('input');
    checkbox.type = 'checkbox';
    checkbox.className = 'player-checkbox';

    checkbox.addEventListener('click', () => {
        handleCheckboxClick(player, team);
    });

    const playerName = document.createElement('span');
    playerName.textContent = player.name;

    playerItem.appendChild(checkbox);
    playerItem.appendChild(playerName);

    return playerItem;
}

// Function to handle checkbox click event
function handleCheckboxClick(player, team) {
    const selectedPlayerItem = document.createElement('li');
    selectedPlayerItem.textContent = player.name;

    if (team === 'A') {
        if (selectedPlayersListA.children.length < 11) {
            selectedPlayersListA.appendChild(selectedPlayerItem);
        } else {
            alert('Team A can only select up to 11 players.');
            player.checkbox.checked = false;
        }
    } else if (team === 'B') {
        if (selectedPlayersListB.children.length < 11) {
            selectedPlayersListB.appendChild(selectedPlayerItem);
        } else {
            alert('Team B can only select up to 11 players.');
            player.checkbox.checked = false;
        }
    }
}

// Function to handle Select Players button click event
function handleSelectPlayersClick() {
    const selectedCountA = selectedPlayersListA.children.length;
    const selectedCountB = selectedPlayersListB.children.length;

    if (selectedCountA === 11 && selectedCountB === 11) {
        alert('Selected players for Team A: ' + selectedCountA + '\nSelected players for Team B: ' + selectedCountB);
    } else {
        alert('Please select 11 players for each team.');
    }
}

// Function to render the player list for Team A
function renderTeamAPlayerList() {
    teamAPlayers.forEach(player => {
        const playerItem = createPlayerItem(player, 'A');
        teamAList.appendChild(playerItem);
    });
}

// Function to render the player list for Team B
function renderTeamBPlayerList() {
    teamBPlayers.forEach(player => {
        const playerItem = createPlayerItem(player, 'B');
        teamBList.appendChild(playerItem);
    });
}

// Call the render functions to display the initial player lists for both teams
renderTeamAPlayerList();
renderTeamBPlayerList();

// Add event listener to the Select Players button
const selectPlayersButton = document.getElementById('select-players-btn');
selectPlayersButton.addEventListener('click', handleSelectPlayersClick);
