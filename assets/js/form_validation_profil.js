function enableEditing(id) {
    const field = document.getElementById(id);
    field.readOnly = false;
    field.focus();
}

function resetForm() {
    // Mengembalikan semua field ke nilai awal dan readonly
    const name = document.getElementById('name');
    const email = document.getElementById('email');
    const phone = document.getElementById('phone');
    name.value = "Ini Bapak Budi";
    email.value = "inibapak@gmail.com";
    phone.value = "08980098177";
    name.readOnly = true;
    email.readOnly = true;
    phone.readOnly = true;
    clearErrors();
}

function clearErrors() {
    document.getElementById('nameError').textContent = '';
    document.getElementById('emailError').textContent = '';
    document.getElementById('phoneError').textContent = '';
}

function validateForm() {
    clearErrors();
    const name = document.getElementById('name').value.trim();
    const email = document.getElementById('email').value.trim();
    const phone = document.getElementById('phone').value.trim();
    const nameError = document.getElementById('nameError');
    const emailError = document.getElementById('emailError');
    const phoneError = document.getElementById('phoneError');
    let isValid = true;

    // Validasi nama
    if (!name) {
        nameError.textContent = "Nama tidak boleh kosong.";
        isValid = false;
    } else if (!/^[a-zA-Z\s]+$/.test(name)) {
        nameError.textContent = "Nama hanya boleh berisi huruf dan spasi.";
        isValid = false;
    }

    // Validasi email
    if (!email) {
        emailError.textContent = "Email tidak boleh kosong.";
        isValid = false;
    } else if (!/^[\w.-]+@gmail\.com$/.test(email)) {
        emailError.textContent = "Email harus menggunakan domain @gmail.com.";
        isValid = false;
    }

    // Validasi nomor telepon
    if (!phone) {
        phoneError.textContent = "Nomor telepon tidak boleh kosong.";
        isValid = false;
    } else if (!/^\d{11,13}$/.test(phone)) {
        phoneError.textContent = "Nomor telepon harus berupa angka dengan panjang 11-13 karakter.";
        isValid = false;
    }

    if (isValid) {
        alert("Form berhasil disimpan!");
        document.getElementById('name').readOnly = true;
        document.getElementById('email').readOnly = true;
        document.getElementById('phone').readOnly = true;
    }
}