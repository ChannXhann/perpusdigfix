function searchAnggota() {
    const searchInput = document.getElementById("searchInput").value.toLowerCase();
    const tableRows = document.querySelectorAll("tbody tr");

    tableRows.forEach(row => {
        const nik = row.cells[1].textContent.toLowerCase();
        const nama = row.cells[2].textContent.toLowerCase();
        const status = row.cells[5].textContent.toLowerCase();

        // Tampilkan baris jika NIK atau Nama cocok dengan input pencarian
        if (nik.includes(searchInput) || nama.includes(searchInput) || status.includes(searchInput)) {
            row.style.display = ""; // Tampilkan baris
        } else {
            row.style.display = "none"; // Sembunyikan baris
        }
    });
}