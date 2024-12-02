
        // Fungsi untuk melakukan pencarian langsung berdasarkan input
        function searchAdmin() {
            const searchInput = document.getElementById("searchInput").value.toLowerCase();
            const tableRows = document.querySelectorAll("tbody tr");

            tableRows.forEach(row => {
                const nip = row.cells[1].textContent.toLowerCase();
                const nama = row.cells[2].textContent.toLowerCase();

                // Tampilkan baris jika NIP atau Nama cocok dengan input pencarian
                if (nip.includes(searchInput) || nama.includes(searchInput)) {
                    row.style.display = ""; // Tampilkan baris
                } else {
                    row.style.display = "none"; // Sembunyikan baris
                }
            });
        }
