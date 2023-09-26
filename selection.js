// Simulated data for available teams with logos
const teamsData1 = [
    { name: 'Team 1A', logo: 'team1a.png' },
    { name: 'Team 1B', logo: 'team1b.png' },
    { name: 'Team 1C', logo: 'team1c.png' },
];

const teamsData2 = [
    { name: 'Team 2X', logo: 'team2x.png' },
    { name: 'Team 2Y', logo: 'team2y.png' },
    { name: 'Team 2Z', logo: 'team2z.png' },
];

document.addEventListener('DOMContentLoaded', () => {
    const team1Logo = document.getElementById('team1-logo');
    const team2Logo = document.getElementById('team2-logo');
    const availableTeamsList1 = document.getElementById('available-teams1');
    const availableTeamsList2 = document.getElementById('available-teams2');
    const prevTeam1Button = document.getElementById('prev-team1');
    const nextTeam1Button = document.getElementById('next-team1');
    const prevTeam2Button = document.getElementById('prev-team2');
    const nextTeam2Button = document.getElementById('next-team2');
    const submitButton = document.getElementById('submit-button');

    let selectedTeam1Index = 0;
    let selectedTeam2Index = 0;

    function renderTeams() {
        const selectedTeam1Data = teamsData1[selectedTeam1Index];
        const selectedTeam2Data = teamsData2[selectedTeam2Index];

        // Display selected team data
        team1Logo.style.backgroundImage = `url(${selectedTeam1Data.logo})`;
        team2Logo.style.backgroundImage = `url(${selectedTeam2Data.logo})`;

        availableTeamsList1.innerHTML = '';
        availableTeamsList2.innerHTML = '';

        // Display selected team names
        const listItem1 = document.createElement('li');
        listItem1.textContent = selectedTeam1Data.name;
        availableTeamsList1.appendChild(listItem1);

        const listItem2 = document.createElement('li');
        listItem2.textContent = selectedTeam2Data.name;
        availableTeamsList2.appendChild(listItem2);
    }

    function updateSelection() {
        renderTeams();
    }

    prevTeam1Button.addEventListener('click', () => {
        if (selectedTeam1Index > 0) {
            selectedTeam1Index--;
            updateSelection();
        }
    });

    nextTeam1Button.addEventListener('click', () => {
        if (selectedTeam1Index < teamsData1.length - 1) {
            selectedTeam1Index++;
            updateSelection();
        }
    });

    prevTeam2Button.addEventListener('click', () => {
        if (selectedTeam2Index > 0) {
            selectedTeam2Index--;
            updateSelection();
        }
    });

    nextTeam2Button.addEventListener('click', () => {
        if (selectedTeam2Index < teamsData2.length - 1) {
            selectedTeam2Index++;
            updateSelection();
        }
    });

    submitButton.addEventListener('click', () => {
        const selectedTeam1Data = teamsData1[selectedTeam1Index];
        const selectedTeam2Data = teamsData2[selectedTeam2Index];
        
        window.location.href='index.html';
    });

    // Call updateSelection to display the initial teams when the page loads
    updateSelection();
});
