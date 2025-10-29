<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>ReHabit</title>
  <meta name="description" content="ReHabit — Membangun kebiasaan baik sehari-hari." />
  <link rel="stylesheet" href="assets/style.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Sofia">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <script src="assets/script.js" defer></script>


</head>
<body>
  <header>
    <h1>ReHabit</h1>
    <p>Bangun kebiasaan baik, sedikit demi sedikit.</p>
    <button id="darkModeToggle" class="btn btn-primary">🌙 Dark Mode</button>
  </header>

  <nav aria-label="Menu utama">
    <ul>
      <li><a href="login.php">Login</a></li>
      <li><a href="dashboard.php">Dashboard</a></li>
      <li><a href="#about">Tentang</a></li>
      <li><a href="#features">Fitur</a></li>
      <li><a href="#how-it-works">Cara Kerja</a></li>
      <li><a href="#contact">Kontak</a></li>
    </ul>
  </nav>

  <main>
    <section id="hero">
      <h2>Selamat datang di ReHabit</h2>
      <p>ReHabit membantu membentuk kebiasaan baru dengan langkah kecil, pengingat, dan statistik sederhana.</p>
      <img src="assets/rehabit.png" alt="logo ReHabit" />
    </section>

    <section id="about">
      <h2>Tentang ReHabit</h2>
      <p>ReHabit tak hanya membuat habit baru tetapi juga mempertahankan habitmu.</p>
    </section>

    <section id="features">
      <h2>Fitur ReHabit</h2>

      <div class="features-grid">
        <article class="card">
          <h3>1. Pencatatan Kebiasaan</h3>
          <p>Pengguna dapat menambahkan kebiasaan yang ingin dilacak.</p>
        </article>

        <article class="card">
          <h3>2. Progress</h3>
          <p>Tampilan ringkas progres.</p>
        </article>

        <article class="card">
          <h3>3. Buat & Edit Kebiasaan</h3>
          <p>Form sederhana untuk menambah, mengubah, dan menghapus daftar kebiasaan.</p>
        </article>

      </div>
    </section>


    <section id="how-it-works">
      <h2>Bagaimana ReHabit Bekerja?</h2>
      
      <div class="steps-grid">
        <div class="card step">
          <h3>1</h3>
          <p>Tambahkan kebiasaan yang ingin dibangun.</p>
        </div>

        <div class="card step">
          <h3>2</h3>
          <p>Tandai kebiasaan saat selesai setiap hari.</p>
        </div>

        <div class="card step">
          <h3>3</h3>
          <p>Lihat progress dan pertahankan streak.</p>
        </div>
      </div>
    </section>


    <section id="contact">
      <h2>Kontak</h2>
      <p>3yanisa@gmail.com</p>
    </section>
  </main>

  <footer>
    <p>&copy; 2025 ReHabit</p>
  </footer>
</body>
</html>
