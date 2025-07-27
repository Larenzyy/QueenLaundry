<?php
include 'config.php';

$username = isset($_POST['username']) ? $_POST['username'] : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$role = isset($_POST['role']) ? $_POST['role'] : '';
?>




<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Website Queen Laundry Coin</title>
  <link rel="stylesheet" href="styles.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
<div class="body">  
<div class="brand-fixed">Website Queen Laundry Coin</div>

  <div class="container">
    <div class="left-box">
      <h1 class="main-title">
        <span class="blue-text">Halo, Selamat Datang!</span><br>
        Kelola transaksi dengan mudah!
      </h1>
      <p class="description">
        Catat pemasukan, pembukuan item, dan pantau laporan keuangan harian secara otomatis.
      </p>
      <div class="button-group">
        <button class="btn-yellow" id="btn-admin">Masuk</button>
        <button class="btn-yellow icon-btn" id="btn-owner">⚙️</button>
      </div>
    </div>

    <div class="right-box">
      <img src="logo.png" alt="Logo Queen Laundry">
    </div>
  </div>

  <!-- Popup -->
  <div class="popup" id="popup" style="display: none;">
    <div class="popup-content">
      <img src="logo.png" alt="Logo" class="popup-logo">
      <h2 id="popup-title">Welcome!</h2>
      <input type="text" id="username" placeholder="username" />
      <input type="password" id="password" placeholder="password" />
      <p id="password-note" style="font-size: 10px; color;red; display; none;">*dapatkan password dari owner</p>
      <a id="password-note-owner" href="https://wa.me/6289620383935" target="_blank" style="font-size: 10px; color;red; display; none;">*lupa password? hubungi developer</a>
      <button class="popup-ok" id="popup-ok">Ok</button>
      
    </div>
  </div>

  <script>
    const popup = document.getElementById("popup");
    const popupTitle = document.getElementById("popup-title");
    const btnAdmin = document.getElementById("btn-admin");
    const btnOwner = document.getElementById("btn-owner");
    const okBtn = document.getElementById("popup-ok");
    const passwordNote = document.getElementById("password-note");
    const passwordNoteO = document.getElementById("password-note-owner");

    let userRole = ""; // untuk menyimpan role (admin / owner)

    // Tampilkan popup Admin
    btnAdmin.addEventListener("click", () => {
      popupTitle.textContent = "Welcome Admin!";
      popup.style.display = "flex";
      userRole = "admin";
      passwordNote.style.display = "flex";
      passwordNoteO.style.display = "none";
    });

    // Tampilkan popup Owner
    btnOwner.addEventListener("click", () => {
      popupTitle.textContent = "Welcome Owner!";
      popup.style.display = "flex";
      userRole = "owner";
      passwordNote.style.display = "none";
      passwordNoteO.style.display = "flex";
    });

   // Kalau klik bagian luar popup-content (background popup), tutup popup
popup.addEventListener("click", (event) => {
  if (event.target === popup) {
    popup.style.display = "none";
  }
});

okBtn.addEventListener("click", () => {
  const username = document.getElementById("username").value;
  const password = document.getElementById("password").value;

  // Kirim username, password, dan role ke server
  fetch('cek_login.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: `username=${encodeURIComponent(username)}&password=${encodeURIComponent(password)}&role=${encodeURIComponent(userRole)}`
  })
  .then(response => response.text())
  .then(data => {
    if (data.trim() === "success") {
      if (userRole === "admin") {
        window.location.href = "dashboard-ad.php";
      } else if (userRole === "owner") {
        window.location.href = "dashboard.php";
      }
    } else {
      alert("Username atau Password salah!");
    }
  })
  .catch(error => {
    console.error('Error:', error);
  });
});

// Tambahkan event listener untuk tombol Enter
document.addEventListener("keydown", (event) => {
  if (event.key === "Enter") {
    okBtn.click(); // Memicu klik tombol OK
  }
});


  </script>
</body>
</html>
