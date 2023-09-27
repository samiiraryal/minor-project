// Sample player data (you can replace this with your own data)
const playersData = [
    { name: "Player 1", rating: 85, position: "Forward" },
    { name: "Player 2", rating: 90, position: "Midfielder" },
    { name: "Player 3", rating: 82, position: "Defender" },
    { name: "Player 4", rating: 88, position: "Midfielder" },
    { name: "Player 5", rating: 87, position: "Forward" },
    { name: "Player 6", rating: 84, position: "Defender" },
    { name: "Player 7", rating: 89, position: "Midfielder" },
    { name: "Player 8", rating: 83, position: "Forward" },
    { name: "Player 9", rating: 86, position: "Defender" },
    { name: "Player 10", rating: 91, position: "Midfielder" },
    { name: "Player 11", rating: 90, position: "Goalkeeper" },
    { name: "Player 12", rating: 84, position: "Midfielder" },
    { name: "Player 13", rating: 82, position: "Defender" },
    { name: "Player 14", rating: 88, position: "Forward" },
    { name: "Player 15", rating: 87, position: "Defender" },
    { name: "Player 16", rating: 89, position: "Midfielder" },
    { name: "Player 17", rating: 83, position: "Forward" },
    // Add more player data as needed
];

// Initialize lineup and substitutes arrays
let lineup = playersData.slice(0, 11); // First 11 players as lineup
let substitutes = playersData.slice(11, 17); // Next 6 players as substitutes

// Variables to keep track of the selected playing player and selected substitute
let selectedPlayingPlayer = null;
let selectedSubstitutePlayer = null;

// Variables to keep track of the substituted-in and swapped-out player indices
let substituteInPlayerIndex = null;
let substitutedOutPlayerIndex = null;

// Function to render the lineup and substitutes lists
function renderLineupAndSubstitutes() {
    const lineupList = document.getElementById("lineup-list");
    lineupList.innerHTML = `
        <li class="player-header">
            <span class="player-name">Name</span>
            <span class="player-position">Position</span>
            <span class="player-rating">Rating</span>
            <span class="player-actions">Actions</span>
        </li>
    `;

    lineup.forEach((player, index) => {
        const listItem = document.createElement("li");
        listItem.innerHTML = `
            <span class="player-name ${selectedPlayingPlayer === index ? 'highlighted-playing' : ''}">${player.name}</span>
            <span class="player-position">${player.position}</span>
            <span class="player-rating">${player.rating}</span>
            <button onclick="handleSubstitute(${index})">Substitute</button>
        `;
        lineupList.appendChild(listItem);
    });

    const substitutesList = document.getElementById("substitutes-list");
    substitutesList.innerHTML = `
        <li class="player-header">
            <span class="player-name">Name</span>
            <span class="player-position">Position</span>
            <span class="player-rating">Rating</span>
            <span class="player-actions">Actions</span>
        </li>
    `;

    substitutes.forEach((player, index) => {
        const listItem = document.createElement("li");
        listItem.innerHTML = `
            <span class="player-name ${selectedSubstitutePlayer === index ? 'highlighted-substitute' : ''}">${player.name}</span>
            <span class="player-position">${player.position}</span>
            <span class="player-rating">${player.rating}</span>
            <button onclick="handleSubstituteSelect(${index})">Select</button>
        `;
        substitutesList.appendChild(listItem);
    });
}

// Function to handle the substitute action when the "Substitute" button is clicked
function handleSubstitute(playerIndex) {
    selectedPlayingPlayer = playerIndex;
    selectedSubstitutePlayer = null; // Reset the selected substitute
    renderLineupAndSubstitutes(); // Update the display
}

// Function to handle the selection of a substitute
function handleSubstituteSelect(substituteIndex) {
    if (selectedPlayingPlayer !== null) {
        const temp = lineup[selectedPlayingPlayer];
        lineup[selectedPlayingPlayer] = substitutes[substituteIndex];
        substitutes[substituteIndex] = temp;
        substituteInPlayerIndex = selectedPlayingPlayer; // Highlight the substituted-in player in green
        substitutedOutPlayerIndex = substituteIndex; // Highlight the swapped-out player in red
        selectedPlayingPlayer = null;
        selectedSubstitutePlayer = null; // Reset the selected substitute
        renderLineupAndSubstitutes();
    }
}

// Initial rendering of lineup and substitutes
renderLineupAndSubstitutes();
