function searchTable() {
    const input = document.getElementById("searchInput");
    const filter = input.value.toLowerCase();
    const table = document.getElementById("patientTable");
    const tr = table.getElementsByTagName("tr");

    for (let i = 1; i < tr.length; i++) { // Start from 1 to skip header
        const td = tr[i].getElementsByTagName("td");
        let rowContainsSearch = false;

        for (let j = 0; j < td.length; j++) {
            if (td[j]) {
                const txtValue = td[j].textContent || td[j].innerText;
                if (txtValue.toLowerCase().indexOf(filter) > -1) {
                    rowContainsSearch = true;
                    break;
                }
            }
        }

        if (rowContainsSearch) {
            tr[i].style.display = "";
        } else {
            tr[i].style.display = "none";
        }
    }
}