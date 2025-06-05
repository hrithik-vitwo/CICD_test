<style>
.table-container {
    display: flex;
    justify-content: space-between; /* Adjust alignment as needed */
}

.table {
    width: 48%; /* Adjust the width as needed to leave some space between the tables */
    border: 1px solid #ccc; /* Add borders as needed */
    margin: 5px; /* Add margin for spacing */
}
</style>
<!DOCTYPE html>
<html>
<head>
    <title>Table Matching</title>
</head>
<body>
<div class="table-container">
    <table id="table1" class="table">
        <thead>
            <tr>
                <th>Date</th>
                <th>No</th>
                <th>Credit</th>
                <th>Debit</th>
                <th>Check</th>
                <th>Percentage</th>
            </tr>
        </thead>
        <tbody>
        <tr>
            <td>10/09/2023</td>
            <td>123</td>
            <td>100</td>
            <td>990</td>
            <td></td>
            <td></td>
          </tr>

          <tr>
            <td>11/09/2023</td>
            <td>124</td>
            <td>100</td>
            <td>90</td>
            <td></td>
            <td></td>
          </tr>
        </tbody>
    </table>
 

    <table id="table2" class="table">
        <thead>
            <tr>
                <th>Date</th>
                <th>No</th>
                <th>Credit</th>
                <th>Debit</th>
            </tr>
        </thead>
        <tbody>
          <tr>
          <td>11/09/2023</td>
            <td>124</td>
            <td>100</td>
            <td>990</td>
          </tr>

          <tr>
          <td>10/09/2023</td>
            <td>123</td>
            <td>100</td>
            <td>990</td>
          </tr>

        </tbody>
    </table>
</div>

    <button onclick="matchTables()">Match</button>
    <button onclick="confirmMatches()">Confirm</button>

    <script>
       function matchTables() {
    const table1 = document.getElementById('table1');
    const table2Rows = document.getElementById('table2').querySelectorAll('tbody tr');

    table2Rows.forEach(table2Row => {
        let highestMatchPercentage = 0;
        let matchingTable1Row = null;

        const date2 = table2Row.cells[0].textContent;
        const no2 = table2Row.cells[1].textContent;

        table1.querySelectorAll('tbody tr').forEach(table1Row => {
            const date1 = table1Row.cells[0].textContent;
            const no1 = table1Row.cells[1].textContent;

            if (date1 === date2 && no1 === no2) {
                const matchPercentage = calculateMatchPercentage(table1Row, table2Row);

                if (matchPercentage > highestMatchPercentage) {
                    highestMatchPercentage = matchPercentage;
                    matchingTable1Row = table1Row;
                }
            }
        });

        if (matchingTable1Row) {
            // Insert the matching row from Table 2 next to the matching row in Table 1
            const clonedRow = table2Row.cloneNode(true);
            matchingTable1Row.insertAdjacentElement('afterend', clonedRow);

            // Add the percentage to the new row in Table 1
            clonedRow.cells[4].textContent = highestMatchPercentage + "%";
        }
    });
}


        function calculateMatchPercentage(row1, row2) {
           //console.log(row1);
    const date1 = row1.cells[0].textContent;
    const no1 = row1.cells[1].textContent;
    const credit1 = row1.cells[2].textContent;
    const debit1 = row1.cells[3].textContent;

    const date2 = row2.cells[0].textContent;
    const no2 = row2.cells[1].textContent;
    const credit2 = row2.cells[2].textContent;
    const debit2 = row2.cells[3].textContent;

    let matchCount = 0;

    // Compare date column
    if (date1 === date2) {
        matchCount++;
    }

    // Compare no column
    if (no1 === no2) {
        matchCount++;
    }

    // Compare credit column
    if (credit1 === credit2) {
        matchCount++;
    }

    // Compare debit column
    if (debit1 === debit2) {
        matchCount++;
    }

    // Calculate the match percentage based on the number of matches
    const totalColumns = 4; // Assuming you are comparing 4 columns
    const matchPercentage = (matchCount / totalColumns) * 100;

    //console.log(matchPercentage);

    return matchPercentage.toFixed(2); // Return the match percentage rounded to 2 decimal places
}


        function confirmMatches() {
            // Implement your logic to confirm the matches and take further actions.
        }
    </script>
</body>
</html>
