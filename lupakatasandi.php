<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perbarui Kata Sandi</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* style.css */
        html,
        body {
            height: 100%;
            width: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Poppins', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            width: 100vw;
            margin: 0;
            background-color: #f7f8fc;
        }

        .container {
            display: flex;
            justify-content: space-between;
            width: 100%;
            height: 100%;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: #fff;
            border-radius: 10px;
            overflow: hidden;
        }

        .form-container,
        .image-container {
            flex: 1;
            padding: 2rem;
            height: 100%;
        }

        .logo-container {
            display: flex;
            align-items: center;
            margin-bottom: 5rem;
            margin-top: 1rem;
        }

        .logo {
            width: 50px;
            height: 50px;
            margin-right: 0.5rem;
        }

        .perpus {
            font-family: 'Poppins';
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
        }

        .dig {
            font-family: 'Poppins';
            font-size: 24px;
            font-weight: bold;
            color: #ff6b00;
        }

        .header-container {
            display: flex;
            flex-direction: column;
            align-items: right;
            justify-content: right;
            margin-bottom: 10px;
        }

        .header-container .back-link {
            text-decoration: none;
            color: #000;
            font-size: 14px;
            margin-top: 10px;
        }

        .header-container h2 {
            font-size: 40px;
            font-weight: bold;
            margin-bottom: 10px;
            text-align: center;
        }

        .header-container p {
            margin-bottom: 1rem;
            color: #666;
            text-align: center;
            padding-bottom: 10px;
        }

        .user-box {
            position: relative;
            margin-bottom: 10px;
        }

        .user-box input[type="email"] {
            padding: 1rem 0.8rem 1rem 0.8rem;
            width: 100%;
            font-size: 1rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .user-box input:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        .user-box input::placeholder {
            color: transparent;
        }

        .user-box label {
            position: absolute;
            top: 50%;
            left: 12px;
            transform: translateY(-50%);
            font-size: 1rem;
            color: #999;
            background: white;
            padding: 0 5px;
            pointer-events: none;
            transition: all 0.3s ease;
            z-index: 1;
        }

        .user-box input:focus~label,
        .user-box input:not(:placeholder-shown)~label {
            top: -1px;
            left: 10px;
            font-size: 0.8rem;
            color: #007bff;
        }

        button {
            width: 100%;
            padding: 12px 15px;
            font-size: 16px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
            margin-bottom: 10px;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 400px;
            text-align: center;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .close {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            position: absolute;
            top: 10px;
            right: 15px;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .image-container {
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f0f2f5;
        }

        .image-container img {
            max-width: 100%;
            height: auto;
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="form-container">
            <!-- Logo dan konten lainnya -->
            <div class="header-container">
                <a href="login.php" class="back-link">&lt; Kembali</a>
                <h2>Perbarui Kata Sandi</h2>
                <p>Jangan khawatir. Masukkan email Anda di bawah ini untuk memulihkan kata sandi Anda</p>
            </div>

            <!-- Modal untuk status -->
            <div id="statusModal" class="modal">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <p id="modalMessage"></p>
                </div>
            </div>

            <form id="resetForm" method="POST" action="send_email.php">
                <div class="user-box">
                    <input type="email" id="email" name="email_user" placeholder="Masukkan email Anda" required>
                    <label for="email">Email</label>
                </div>
                <button type="submit" id="nextButton">Selanjutnya</button>
            </form>
        </div>
        <div class="image-container">
            <img src="assets/images/password.png" alt="Forgot Password" weight="400">
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $("#resetForm").on("submit", function (e) {
                e.preventDefault();

                const email = $("#email").val();

                if (!validateEmail(email)) {
                    showModal("error", "Masukkan email yang valid.");
                    return;
                }

                showModal("loading", "Memproses...");

                $.ajax({
                    type: "POST",
                    url: "send_email.php",
                    data: { email_user: email },
                    dataType: "json",
                    success: function (response) {
                        if (response.status === "success") {
                            showModal("success", response.message);
                            setTimeout(function () {
                                window.location.href = "forgotverify.php"; // Pengalihan setelah sukses
                            }, 2000);
                        } else {
                            showModal("error", response.message); // Tampilkan pesan error jika gagal
                        }
                    },
                    error: function () {
                        showModal("error", "Terjadi kesalahan. Silakan coba lagi.");
                    }
                });
            });

            function validateEmail(email) {
                const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return regex.test(email);
            }

            function showModal(type, message) {
                const modal = $("#statusModal");
                const modalMessage = $("#modalMessage");

                if (type === "success") {
                    modalMessage.css("color", "#28a745");
                } else if (type === "error") {
                    modalMessage.css("color", "#dc3545");
                } else if (type === "loading") {
                    modalMessage.css("color", "#ffc107");
                }

                modalMessage.text(message);
                modal.show();
            }

            $(".close").click(function () {
                $("#statusModal").hide();
            });

            $(window).click(function (event) {
                if ($(event.target).is("#statusModal")) {
                    $("#statusModal").hide();
                }
            });
        });

    </script>
</body>

</html>