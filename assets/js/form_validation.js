function validateForm() {
    // Mengambil nilai input
    const nip = document.getElementById("nip").value.trim();
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();

    // Mengambil elemen error
    const nipError = document.getElementById("nipError");
    const emailError = document.getElementById("emailError");
    const passwordError = document.getElementById("passwordError");

    // Reset error message
    nipError.textContent = "";
    emailError.textContent = "";
    passwordError.textContent = "";

    let isValid = true;

    // Validasi NIP
    if (nip === "") {
        nipError.textContent = "NIP tidak boleh kosong.";
        isValid = false;
    } else if (!/^\d+$/.test(nip)) {
        nipError.textContent = "NIP harus berupa angka.";
        isValid = false;
    } else if (nip.length !== 18) {
        nipError.textContent = "NIP harus tepat 18 karakter.";
        isValid = false;
    }

    // Validasi Email
    const emailRegex = /^[a-zA-Z0-9]+@gmail\.com$/;
    if (email === "") {
        emailError.textContent = "Email tidak boleh kosong.";
        isValid = false;
    } else if (!emailRegex.test(email)) {
        emailError.textContent = "Email tidak boleh menggunakan karakter dan format harus @gmail.com dan valid.";
        isValid = false;
    }

    // Validasi Password
    const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
    if (password === "") {
        passwordError.textContent = "Kata sandi tidak boleh kosong.";
        isValid = false;
    } else if (!passwordRegex.test(password)) {
        passwordError.textContent = "Kata sandi harus minimal 8 karakter, mengandung huruf besar, huruf kecil, angka, dan karakter khusus.";
        isValid = false;
    }

    return isValid; // Jika validasi lolos, form akan disubmit
}
