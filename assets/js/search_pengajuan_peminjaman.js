  // Fungsi untuk melakukan pencarian langsung berdasarkan input
  function searchPeminjaman() {
    const searchInput = document.getElementById("searchInput").value.toLowerCase();
    const tableRows = document.querySelectorAll("tbody tr");

    tableRows.forEach(row => {
        const nik = row.cells[1].textContent.toLowerCase();
        const judul = row.cells[4].textContent.toLowerCase();

        // Tampilkan baris jika NIK atau Nama cocok dengan input pencarian
        if (nik.includes(searchInput) || judul.includes(searchInput)) {
            row.style.display = ""; // Tampilkan baris
        } else {
            row.style.display = "none"; // Sembunyikan baris
        }
    });
}