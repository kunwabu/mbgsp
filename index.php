<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SPPG Sumber Payung</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    @media print {
      .no-print { display: none !important; }
      body { background: white; color: black; }
    }
  </style>
</head>
<body class="bg-slate-50 min-h-screen text-slate-800 flex flex-col font-sans">

  <!-- Header -->
  <header class="bg-emerald-700 text-white shadow-lg sticky top-0 z-50 no-print">
    <div class="max-w-7xl mx-auto px-3 sm:px-4 py-3 flex justify-between items-center">
      
      <!-- Bagian Kiri: Logo -->
      <div class="flex items-center space-x-3">
        <!-- Tambahkan id="logoContainer" dan ukuran pasti (w-10 h-10) agar bundar sempurna -->
        <div id="logoContainer" class="bg-white p-0.5 rounded-full text-emerald-700 shadow flex-shrink-0 w-10 h-10 flex items-center justify-center overflow-hidden">
          <!-- Icon default sebelum data dimuat -->
          <i class="fa-solid fa-utensils text-xl"></i>
        </div>
        
        <!-- Teks Judul: Hanya tampil di layar tablet/desktop (sm:block) -->
        <div class="hidden sm:block">
          <!-- Tambahkan id untuk nama dan alamat -->
          <h1 id="headerNama" class="font-bold text-lg leading-tight">Memuat data...</h1>
          <p id="headerAlamat" class="text-xs text-emerald-200">Menunggu sambungan server...</p>
        </div>
      </div>

      <!-- Bagian Kanan: Tanggal & Tombol Admin -->
      <div class="flex items-center space-x-2 sm:space-x-3">
        <div id="publicDateContainer" class="bg-emerald-800/80 px-2 py-1.5 sm:px-3 rounded-lg flex items-center space-x-1 sm:space-x-2 text-xs sm:text-sm border border-emerald-600">
          <i class="fa-regular fa-calendar text-emerald-300"></i>
          <input type="date" id="selectedDate" onchange="filterDataByDate()" class="bg-transparent text-white focus:outline-none cursor-pointer w-[105px] sm:w-auto">
        </div>
        <button id="authBtn" onclick="toggleAuthModal()" class="bg-emerald-600 hover:bg-emerald-500 text-white px-2.5 py-1.5 sm:px-3 rounded-lg text-sm font-medium transition flex items-center space-x-1 shadow flex-shrink-0">
          <i class="fa-solid fa-lock"></i>
          <!-- Teks Login: Hanya tampil di layar tablet/desktop (sm:inline) -->
          <span class="hidden sm:inline">Login Admin</span>
        </button>
      </div>

    </div>
  </header>

  <!-- Main Content -->
  <main class="max-w-7xl mx-auto px-4 py-6 flex-grow w-full">
    <div id="loadingOverlay" class="flex flex-col justify-center items-center py-20">
      <i class="fa-solid fa-circle-notch fa-spin text-4xl text-emerald-600 mb-3"></i>
      <p id="loadingText" class="text-slate-500 text-sm font-medium">Memuat data dari server...</p>
    </div>

    <!-- PUBLIC VIEW -->
    <div id="publicView" class="hidden">
      <div class="flex border-b border-slate-200 mb-6 bg-white rounded-xl p-1 shadow-sm overflow-x-auto no-print">
        <button onclick="switchTab('Kecil')" id="tab-Kecil" class="portion-tab flex-1 py-2.5 px-4 rounded-lg font-medium text-sm transition text-slate-500 flex items-center justify-center space-x-2"><i class="fa-solid fa-child"></i> <span>Porsi Kecil</span></button>
        <button onclick="switchTab('Besar')" id="tab-Besar" class="portion-tab flex-1 py-2.5 px-4 rounded-lg font-medium text-sm transition text-slate-500 flex items-center justify-center space-x-2"><i class="fa-solid fa-user"></i> <span>Porsi Besar</span></button>
        <button onclick="switchTab('Balita')" id="tab-Balita" class="portion-tab flex-1 py-2.5 px-4 rounded-lg font-medium text-sm transition text-slate-500 flex items-center justify-center space-x-2"><i class="fa-solid fa-baby"></i> <span>Balita</span></button>
        <button onclick="switchTab('BumilBusui')" id="tab-BumilBusui" class="portion-tab flex-1 py-2.5 px-4 rounded-lg font-medium text-sm transition text-slate-500 flex items-center justify-center space-x-2"><i class="fa-solid fa-person-breastfeeding"></i> <span>Bumil & Busui</span></button>
      </div>
      <div id="portionContent" class="grid grid-cols-1 lg:grid-cols-12 gap-6"></div>
    </div>

    <!-- ADMIN VIEW -->
    <div id="adminView" class="hidden space-x-0 space-y-6">
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-white p-4 rounded-xl shadow-sm border border-slate-200 gap-4">
        <div class="flex items-center space-x-3">
          <span class="bg-amber-100 text-amber-800 text-xs font-semibold px-2.5 py-1 rounded-full">Panel Admin</span>
          <h2 class="text-lg font-bold">Kelola Data Menu</h2>
        </div>
        
        <!-- Area Kontrol Admin (Filter & Tambah) -->
        <div class="flex items-center space-x-2 w-full sm:w-auto">
          <!-- Filter Tanggal Admin -->
          <div class="flex items-center bg-slate-50 border border-slate-200 rounded-lg px-2 py-1.5 text-sm w-full sm:w-auto">
            <label for="adminFilterDate" class="text-slate-400 mr-2"><i class="fa-solid fa-filter"></i></label>
            <input type="date" id="adminFilterDate" onchange="renderAdminTable()" class="bg-transparent text-slate-700 focus:outline-none cursor-pointer text-xs sm:text-sm w-full">
            <button onclick="clearAdminFilter()" class="ml-2 text-slate-400 hover:text-rose-500 transition" title="Tampilkan Semua Data">
              <i class="fa-solid fa-circle-xmark"></i>
            </button>
          </div>

          <!-- Tombol Edit Profil Baru -->
          <button onclick="openProfilModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg text-sm font-medium transition flex items-center space-x-2 shadow whitespace-nowrap">
            <i class="fa-solid fa-building"></i><span class="hidden sm:inline">Edit Profil</span>
          </button>

          <!-- Tombol Tambah -->
          <button onclick="openFormModal()" class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-2 rounded-lg text-sm font-medium transition flex items-center space-x-2 shadow whitespace-nowrap">
            <i class="fa-solid fa-plus"></i><span class="hidden sm:inline">Tambah Menu</span>
          </button>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-sm text-left text-slate-600">
            <thead class="text-xs uppercase bg-slate-100 border-b border-slate-200 text-slate-700">
              <tr><th class="px-4 py-3">Tanggal</th><th class="px-4 py-3">Jenis Porsi</th><th class="px-4 py-3">Jumlah Porsi</th><th class="px-4 py-3">Daftar Menu</th><th class="px-4 py-3">Gizi (EPLKS)</th><th class="px-4 py-3 text-center">Aksi</th></tr>
            </thead>
            <tbody id="adminTableBody" class="divide-y divide-slate-100"></tbody>
          </table>
        </div>
      </div>
    </div>
  </main>

  <!-- Modal Login Authenticator -->
  <div id="authModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-md mx-4">
      <h3 class="text-lg font-bold text-slate-800 mb-4 text-center">Keamanan Admin SPPG</h3>
      <div class="space-y-4">
        <div class="text-center mb-4">
          <i class="fa-solid fa-shield-halved text-4xl text-emerald-500 mb-2"></i>
          <p class="text-xs text-slate-500">Buka aplikasi Google Authenticator di HP Anda dan masukkan 6 digit angka yang muncul.</p>
        </div>
        <div>
          <input type="text" id="adminPin" maxlength="6" placeholder="Masukkan OTP" onkeydown="if(event.key === 'Enter') submitLogin()" class="w-full border-2 border-emerald-100 rounded-lg px-4 py-3 text-center text-2xl font-bold tracking-[0.5em] focus:border-emerald-500 focus:ring-0 outline-none transition">
        </div>
        <div class="flex justify-end space-x-2 mt-4">
          <button onclick="toggleAuthModal()" class="w-1/2 py-2.5 rounded-lg text-sm text-slate-600 hover:bg-slate-100 font-medium transition">Batal</button>
          <button id="loginSubmitBtn" onclick="submitLogin()" class="w-1/2 py-2.5 rounded-lg text-sm bg-emerald-600 text-white font-medium hover:bg-emerald-700 transition">Verifikasi</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Form Modal (Tambah/Edit) -->
  <div id="formModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto mx-4">
      <h3 id="formTitle" class="text-lg font-bold text-slate-800 mb-4">Tambah Data Menu</h3>
      <form id="menuForm" onsubmit="handleFormSubmit(event)" class="space-y-4">
        <input type="hidden" id="formRowIndex">
        <input type="hidden" id="formFotoUrl_hidden">
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div><label class="block text-xs font-semibold text-slate-600 mb-1">Tanggal</label><input type="date" id="formTanggal" required class="w-full border rounded-lg px-3 py-2 text-sm"></div>
          <div><label class="block text-xs font-semibold text-slate-600 mb-1">Jenis Porsi</label>
            <select id="formPorsi" required class="w-full border rounded-lg px-3 py-2 text-sm">
              <option value="Kecil">Porsi Kecil</option><option value="Besar">Porsi Besar</option><option value="Balita">Balita</option><option value="BumilBusui">Bumil & Busui</option>
            </select>
          </div>
          <div><label class="block text-xs font-semibold text-slate-600 mb-1">Jumlah Box</label><input type="number" id="formJumlahPorsi" required min="1" class="w-full border rounded-lg px-3 py-2 text-sm"></div>
        </div>

        <div><label class="block text-xs font-semibold text-slate-600 mb-1">Daftar Menu (Pisahkan dengan koma)</label><textarea id="formMenu" required rows="2" placeholder="Nasi Putih, Ayam Goreng..." class="w-full border rounded-lg px-3 py-2 text-sm"></textarea></div>

        <div class="grid grid-cols-2 md:grid-cols-5 gap-3 bg-slate-50 p-3 rounded-lg border">
          <div><label class="block text-xs font-semibold text-slate-600 mb-1">Energi</label><input type="number" step="0.1" id="formEnergi" required class="w-full border rounded px-2 py-1 text-sm"></div>
          <div><label class="block text-xs font-semibold text-slate-600 mb-1">Protein</label><input type="number" step="0.1" id="formProtein" required class="w-full border rounded px-2 py-1 text-sm"></div>
          <div><label class="block text-xs font-semibold text-slate-600 mb-1">Lemak</label><input type="number" step="0.1" id="formLemak" required class="w-full border rounded px-2 py-1 text-sm"></div>
          <div><label class="block text-xs font-semibold text-slate-600 mb-1">Karbo</label><input type="number" step="0.1" id="formKarbohidrat" required class="w-full border rounded px-2 py-1 text-sm"></div>
          <div><label class="block text-xs font-semibold text-slate-600 mb-1">Serat</label><input type="number" step="0.1" id="formSerat" required class="w-full border rounded px-2 py-1 text-sm"></div>
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1">Upload Foto Ompreng (Wajib Ekstensi .jpg / .png)</label>
          <input type="file" id="formFotoFile" accept="image/*" class="w-full border rounded-lg px-3 py-1.5 text-sm bg-slate-50 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
          <p id="editPhotoNote" class="text-xs text-amber-600 mt-1 hidden">Biarkan kosong jika tidak ingin mengubah foto sebelumnya.</p>
        </div>

        <div class="flex justify-end space-x-2 pt-2 border-t mt-4">
          <button type="button" onclick="closeFormModal()" class="px-4 py-2 rounded-lg text-sm text-slate-600 hover:bg-slate-100">Batal</button>
          <button type="submit" class="px-4 py-2 rounded-lg text-sm bg-emerald-600 text-white font-medium hover:bg-emerald-700">Simpan Data</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal Tampil Gambar Penuh (Lightbox) -->
  <div id="imageModal" onclick="closeImageModal()" class="fixed inset-0 bg-slate-900/95 backdrop-blur-sm z-[60] flex items-center justify-center hidden opacity-0 transition-opacity duration-300 cursor-pointer">
    <!-- Tombol Tutup -->
    <button class="absolute top-4 right-4 text-white hover:text-emerald-400 text-4xl focus:outline-none transition">
      <i class="fa-solid fa-xmark"></i>
    </button>
    <!-- Area Gambar -->
    <div class="relative w-full max-w-4xl max-h-screen p-4 flex justify-center">
      <!-- event.stopPropagation() mencegah modal tertutup saat gambar itu sendiri yang diklik -->
      <img id="fullSizeImage" onclick="event.stopPropagation()" src="" alt="Foto Utuh" class="max-w-full max-h-[90vh] object-contain rounded-xl shadow-2xl cursor-default">
    </div>
  </div>

  <!-- Modal Edit Profil -->
  <div id="profilModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-md mx-4">
      <h3 class="text-lg font-bold text-slate-800 mb-4">Edit Profil SPPG</h3>
      <form id="profilForm" onsubmit="handleProfilSubmit(event)" class="space-y-4">
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1">Nama Organisasi / Katering</label>
          <input type="text" id="formProfilNama" required class="w-full border rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1">Alamat Lengkap</label>
          <textarea id="formProfilAlamat" required rows="2" class="w-full border rounded-lg px-3 py-2 text-sm"></textarea>
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1">Upload Logo Baru (Opsional)</label>
          <input type="file" id="formProfilLogo" accept="image/*" class="w-full border rounded-lg px-3 py-1.5 text-sm bg-slate-50">
          <p class="text-xs text-amber-600 mt-1">Biarkan kosong jika tidak ingin mengubah logo saat ini.</p>
        </div>
        <div class="flex justify-end space-x-2 pt-2 border-t mt-4">
          <button type="button" onclick="closeProfilModal()" class="px-4 py-2 rounded-lg text-sm text-slate-600 hover:bg-slate-100">Batal</button>
          <button type="submit" class="px-4 py-2 rounded-lg text-sm bg-blue-600 text-white font-medium hover:bg-blue-700">Simpan Profil</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    // ==========================================
    // KONFIGURASI API
    // ==========================================
    
    // Convert API_URL ke Base64
    const ENCODE_API = "aHR0cHM6Ly9zY3JpcHQuZ29vZ2xlLmNvbS9tYWNyb3Mvcy9BS2Z5Y2J5WV9DYW04WFFHS0x5TlJDRTBnN0RUaTdWS203OWF0TFBXRlREY3BKWkRkb292Z29rcnhkcW43SmNVYjdGdlVzZllsdy9leGVj";

    // Fungsi bawaan JS untuk decode (atob) saat script berjalan
    const API_URL = atob(ENCODE_API);
    
    let rawDatabase = [];
    let activeTab = 'Kecil';
    let isAdmin = false;
    let chartInstance = null;
    let currentProfil = {};

    function checkLoginStatus() {
      const currentToken = sessionStorage.getItem('adminToken');
      
      if (currentToken) {
        isAdmin = true;
        document.getElementById('authBtn').innerHTML = `<i class="fa-solid fa-right-from-bracket"></i><span class="hidden sm:inline">Keluar Admin</span>`;
        document.getElementById('adminView').classList.remove('hidden'); 
        document.getElementById('publicView').classList.add('hidden'); 
        document.getElementById('publicDateContainer').classList.add('hidden'); 
      } else {
        isAdmin = false;
        document.getElementById('authBtn').innerHTML = `<i class="fa-solid fa-lock"></i><span class="hidden sm:inline">Login Admin</span>`;
        document.getElementById('adminView').classList.add('hidden'); 
        document.getElementById('publicView').classList.remove('hidden'); 
        document.getElementById('publicDateContainer').classList.remove('hidden'); 
      }
    }

    window.addEventListener('DOMContentLoaded', () => {
      // Panggil fungsi cek login pertama kali halaman dimuat
      checkLoginStatus();

      const date = new Date();
      // Mengurangi waktu dengan selisih zona waktu lokal
      const localDate = new Date(date.getTime() - (date.getTimezoneOffset() * 60000))
        .toISOString()
        .split('T')[0];

      document.getElementById('selectedDate').value = localDate;
      
      // Tambahkan baris ini untuk filter Admin:
      document.getElementById('adminFilterDate').value = localDate;

      fetchData();
    });

    // Helper Konversi Foto ke Base64
    function getBase64(file) {
      return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = () => resolve(reader.result);
        reader.onerror = error => reject(error);
      });
    }

    async function fetchData() {
      showLoading("Memuat data dari Google Sheets...");
      try {
        const res = await fetch(`${API_URL}?action=getData`);
        const result = await res.json(); // Menyimpan respon mentah
        
        // Panggil fungsi render header jika data profil dikirim dari backend
        if (result.profil) {
          currentProfil = result.profil; 
          renderHeaderProfile(result.profil);
        }

        // Asumsi data array menu Anda ada di result.data (sesuaikan dengan output API Anda)
        rawDatabase = result.data || result; 
        
        hideLoading();
        if (!isAdmin) { 
          document.getElementById('publicView').classList.remove('hidden'); 
          renderPublicView(); 
        } else { 
          document.getElementById('adminView').classList.remove('hidden'); 
          renderAdminTable(); 
        }
      } catch (err) { 
        hideLoading(); 
        alert('Gagal memuat data: Pastikan API URL benar.'); 
      }
    }

    // ==========================================
    // RENDER HEADER
    // ==========================================
    function renderHeaderProfile(dataProfil) {
      // 1. Ubah Nama (H1)
      if (dataProfil.nama) {
        document.getElementById('headerNama').innerText = dataProfil.nama;
      }
      
      // 2. Ubah Alamat (P)
      if (dataProfil.alamat) {
        document.getElementById('headerAlamat').innerText = dataProfil.alamat;
      }
      
      // 3. Ubah Logo (Jika URL gambar tersedia, ganti icon dengan tag <img>)
      if (dataProfil.logoUrl) {
        document.getElementById('logoContainer').innerHTML = `
          <img src="${dataProfil.logoUrl}" alt="Logo" class="w-full h-full object-cover rounded-full">
        `;
      }
    }

    // ==========================================
    // RENDER PUBLIK
    // ==========================================
    function renderPublicView() {
      const selectedDate = document.getElementById('selectedDate').value;
      document.querySelectorAll('.portion-tab').forEach(t => { t.classList.remove('bg-emerald-600', 'text-white', 'shadow'); t.classList.add('text-slate-500'); });
      const activeBtn = document.getElementById(`tab-${activeTab}`);
      if (activeBtn) { activeBtn.classList.add('bg-emerald-600', 'text-white', 'shadow'); activeBtn.classList.remove('text-slate-500'); }

      const container = document.getElementById('portionContent');
      const item = rawDatabase.find(d => d.tanggal === selectedDate && d.porsi === activeTab);

      if (!item) {
        container.innerHTML = `<div class="col-span-12 bg-white rounded-2xl p-12 text-center border border-slate-200"><i class="fa-solid fa-utensils text-4xl text-slate-300 mb-3"></i><h3 class="text-base font-semibold text-slate-700">Belum Ada Data Menu</h3><p class="text-xs text-slate-400 mt-1">Laporan belum diinput untuk tanggal ${selectedDate}.</p></div>`;
        return;
      }

      const menuList = item.menu.split(',').map(m => `<li class="flex items-center space-x-2"><i class="fa-solid fa-check text-emerald-500 text-xs"></i><span>${m.trim()}</span></li>`).join('');
      container.innerHTML = `
        <div class="lg:col-span-5 bg-white p-4 rounded-2xl shadow-sm border border-slate-200">
            <div onclick="openImageModal('${item.fotoUrl}')" class="relative group overflow-hidden rounded-xl bg-slate-100 aspect-video lg:aspect-square cursor-pointer">
              <!-- Gambar dengan efek zoom in saat di-hover -->
              <img src="${item.fotoUrl}" alt="Foto Ompreng" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
            
              <!-- Overlay Kaca Pembesar -->
              <div class="absolute inset-0 bg-slate-900/0 group-hover:bg-slate-900/30 transition-colors duration-300 flex items-center justify-center">
                <i class="fa-solid fa-magnifying-glass-plus text-white text-4xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 drop-shadow-md"></i>
              </div>
            </div>
            <p class="text-center text-[10px] text-slate-400 mt-2"><i class="fa-regular fa-hand-pointer mr-1"></i>Klik foto untuk melihat ukuran penuh</p>
        </div>
        <div class="lg:col-span-7 space-y-4">
          <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
            <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Daftar Menu</h3>
            <ul class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm font-medium text-slate-700">${menuList}</ul>
          </div>
          <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
            <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-4">Informasi Laporan & Gizi</h3>
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
              
              <!-- Kotak Hijau: Sekarang Menampilkan Jumlah Porsi -->
              <div class="md:col-span-5 flex flex-col items-center justify-center p-3 bg-emerald-50 rounded-xl border border-emerald-100">
                <span class="text-4xl font-extrabold text-emerald-700">${item.jumlahPorsi}</span>
                <span class="text-xs font-bold text-emerald-800 uppercase mt-1">Jumlah Ompreng Disalurkan</span>
              </div>
              
              <div class="md:col-span-7"><div class="h-44 flex justify-center"><canvas id="nutritionChart"></canvas></div></div>
            </div>
            
            <!-- Deretan Data Gizi: Ditambah menjadi 5 kolom untuk Energi -->
            <div class="grid grid-cols-2 md:grid-cols-5 gap-2 mt-4 text-center">
              <div class="p-2 bg-slate-50 rounded-lg border"><div class="text-xs text-slate-400">Energi</div><div class="text-sm font-bold text-slate-700">${item.energi} kkal</div></div>
              <div class="p-2 bg-slate-50 rounded-lg border"><div class="text-xs text-slate-400">Protein</div><div class="text-sm font-bold text-slate-700">${item.protein} g</div></div>
              <div class="p-2 bg-slate-50 rounded-lg border"><div class="text-xs text-slate-400">Lemak</div><div class="text-sm font-bold text-slate-700">${item.lemak} g</div></div>
              <div class="p-2 bg-slate-50 rounded-lg border"><div class="text-xs text-slate-400">Karbo</div><div class="text-sm font-bold text-slate-700">${item.karbohidrat} g</div></div>
              <div class="p-2 bg-slate-50 rounded-lg border"><div class="text-xs text-slate-400">Serat</div><div class="text-sm font-bold text-slate-700">${item.serat} g</div></div>
            </div>
          </div>
        </div>`;
      renderNutritionChart(item);
    }

    // ==========================================
    // LOGIKA MODAL GAMBAR PENUH
    // ==========================================
    function openImageModal(url) {
      const modal = document.getElementById('imageModal');
      document.getElementById('fullSizeImage').src = url;
      modal.classList.remove('hidden');
      
      // Sedikit delay untuk efek transisi fade-in
      setTimeout(() => {
        modal.classList.remove('opacity-0');
        modal.classList.add('opacity-100');
      }, 10);
    }

    function closeImageModal() {
      const modal = document.getElementById('imageModal');
      modal.classList.remove('opacity-100');
      modal.classList.add('opacity-0');
      
      // Tunggu animasi fade-out selesai baru sembunyikan
      setTimeout(() => {
        modal.classList.add('hidden');
        document.getElementById('fullSizeImage').src = ''; // Kosongkan src memori
      }, 300);
    }

    function renderNutritionChart(item) {
      const ctx = document.getElementById('nutritionChart'); if (!ctx) return;
      if (chartInstance) chartInstance.destroy();
      chartInstance = new Chart(ctx, { 
        type: 'doughnut', 
        data: { 
          labels: ['Energi (kkal)', 'Protein (g)', 'Lemak (g)', 'Karbo (g)', 'Serat (g)'], 
          datasets: [{ 
            data: [item.energi, item.protein, item.lemak, item.karbohidrat, item.serat], 
            backgroundColor: ['#3b82f6', '#f59e0b', '#8b5cf6', '#10b981', '#ef4444'], 
            borderWidth: 2 
          }] 
        }, 
        options: { 
          responsive: true, 
          maintainAspectRatio: false, 
          plugins: { 
            legend: { position: 'right', labels: { boxWidth: 12, font: { size: 10 } } } 
          } 
        } 
      });
    }

    function switchTab(p) { activeTab = p; renderPublicView(); }
    function filterDataByDate() { if (!isAdmin) renderPublicView(); }
    function showLoading(txt) { document.getElementById('loadingText').innerText = txt; document.getElementById('loadingOverlay').classList.remove('hidden'); }
    function hideLoading() { document.getElementById('loadingOverlay').classList.add('hidden'); }

    // ==========================================
    // LOGIKA LOGIN AUTHENTICATOR
    // ==========================================
    async function toggleAuthModal() { 
      if (isAdmin) {
        // --- 1. AMBIL TOKEN & BERITAHU SERVER UNTUK MENGHAPUSNYA ---
        const currentToken = sessionStorage.getItem('adminToken');
        if (currentToken) {
          showLoading("Keluar...");
          try {
            await fetch(API_URL, {
              method: 'POST',
              body: JSON.stringify({
                action: 'logout',
                token: currentToken
              })
            });
          } catch (e) {
            console.error("Gagal menghubungi server untuk logout:", e);
          } finally {
            hideLoading();
          }
        }

        // --- 2. LANJUTKAN PROSES LOGOUT UI ANDA SEPERTI BIASA ---
        isAdmin = false; 
        sessionStorage.removeItem('adminToken');
        document.getElementById('authBtn').innerHTML = `<i class="fa-solid fa-lock"></i><span class="hidden sm:inline">Login Admin</span>`;
        document.getElementById('adminView').classList.add('hidden'); 
        document.getElementById('publicView').classList.remove('hidden'); 
        document.getElementById('publicDateContainer').classList.remove('hidden'); 
        renderPublicView();
      } else { 
        // Proses Buka / Tutup Modal Login (Tidak ada yang dirubah di bagian ini)
        const authModal = document.getElementById('authModal');
        if (authModal.classList.contains('hidden')) {
          authModal.classList.remove('hidden'); // Buka modal
          document.getElementById('adminPin').focus();
        } else {
          authModal.classList.add('hidden'); // Tutup modal saat klik Batal
          document.getElementById('adminPin').value = ''; // Bersihkan sisa ketikan PIN
        }
      }
    }

    async function submitLogin() {
      const pin = document.getElementById('adminPin').value;
      if (pin.length !== 6) return alert("Masukkan 6 digit angka!");

      const btn = document.getElementById('loginSubmitBtn');
      const origText = btn.innerHTML;
      btn.innerHTML = `<i class="fa-solid fa-circle-notch fa-spin"></i>`;
      btn.disabled = true;

      try {
        const res = await fetch(API_URL, { method: 'POST', body: JSON.stringify({ action: 'verifyPin', pin: pin }) });
        const result = await res.json();
        
        if (result.status === 'success') {

          sessionStorage.setItem('adminToken', result.token);

          isAdmin = true; 
          document.getElementById('authModal').classList.add('hidden'); 
          document.getElementById('adminPin').value = '';
          document.getElementById('authBtn').innerHTML = `<i class="fa-solid fa-right-from-bracket"></i><span class="hidden sm:inline">Keluar Admin</span>`;
          document.getElementById('publicView').classList.add('hidden'); 
          document.getElementById('publicDateContainer').classList.add('hidden');
          document.getElementById('adminView').classList.remove('hidden'); 
          renderAdminTable();
        } else { alert(result.message); }
      } catch (err) { alert('Terjadi kesalahan koneksi.'); }

      btn.innerHTML = origText;
      btn.disabled = false;
    }

    // ==========================================
    // RENDER ADMIN
    // ==========================================
    function renderAdminTable() {
      const tbody = document.getElementById('adminTableBody'); 
      tbody.innerHTML = '';
      
      // Ambil nilai tanggal dari input filter admin
      const filterDate = document.getElementById('adminFilterDate').value;
      
      // Filter database berdasarkan tanggal, jika input kosong maka tampilkan semua
      let filteredDatabase = rawDatabase;
      if (filterDate !== '') {
        filteredDatabase = rawDatabase.filter(item => item.tanggal === filterDate);
      }

      if (filteredDatabase.length === 0) { 
        tbody.innerHTML = `<tr><td colspan="6" class="px-4 py-8 text-center text-slate-400">Belum ada data ${filterDate ? 'pada tanggal ini' : ''}</td></tr>`; 
        return; 
      }
      
      filteredDatabase.forEach(item => {
        const tr = document.createElement('tr'); tr.className = 'hover:bg-slate-50 transition';
        tr.innerHTML = `<td class="px-4 py-3 font-medium">${item.tanggal}</td>
          <td class="px-4 py-3"><span class="bg-emerald-100 text-emerald-800 text-xs px-2 py-0.5 rounded-full">${item.porsi}</span></td>
          <td class="px-4 py-3">${item.jumlahPorsi} Box</td><td class="px-4 py-3 text-xs">${item.menu}</td><td class="px-4 py-3 text-xs">${item.energi} kkal, ${item.protein} g, ${item.lemak} g, ${item.karbohidrat} g, ${item.serat} g</td>
          <td class="px-4 py-3 text-center space-x-3">
            <button onclick="editData(${item.rowIndex})" class="text-blue-600 hover:text-blue-800"><i class="fa-solid fa-pen-to-square"></i></button>
            <button onclick="deleteData(${item.rowIndex})" class="text-rose-600 hover:text-rose-800"><i class="fa-solid fa-trash"></i></button>
          </td>`;
        tbody.appendChild(tr);
      });
    }

    // Fungsi untuk menghapus filter (Menampilkan semua tanggal)
    function clearAdminFilter() {
      document.getElementById('adminFilterDate').value = '';
      renderAdminTable();
    }

    function openFormModal(editItem = null) {
      document.getElementById('formModal').classList.remove('hidden');
      document.getElementById('formFotoFile').value = ''; 
      if (editItem) {
        document.getElementById('formTitle').innerText = 'Edit Data Menu';
        document.getElementById('formRowIndex').value = editItem.rowIndex;
        document.getElementById('formTanggal').value = editItem.tanggal;
        document.getElementById('formPorsi').value = editItem.porsi;
        document.getElementById('formJumlahPorsi').value = editItem.jumlahPorsi;
        document.getElementById('formMenu').value = editItem.menu;
        document.getElementById('formEnergi').value = editItem.energi;
        document.getElementById('formProtein').value = editItem.protein;
        document.getElementById('formLemak').value = editItem.lemak;
        document.getElementById('formKarbohidrat').value = editItem.karbohidrat;
        document.getElementById('formSerat').value = editItem.serat;
        document.getElementById('formFotoUrl_hidden').value = editItem.fotoUrl;
        document.getElementById('editPhotoNote').classList.remove('hidden');
        document.getElementById('formFotoFile').required = false;
      } else {
        document.getElementById('formTitle').innerText = 'Tambah Data Menu Baru';
        document.getElementById('menuForm').reset();
        document.getElementById('formRowIndex').value = '';
        document.getElementById('formFotoUrl_hidden').value = '';
        document.getElementById('formTanggal').value = document.getElementById('selectedDate').value;
        document.getElementById('editPhotoNote').classList.add('hidden');
        document.getElementById('formFotoFile').required = true;
      }
    }
    function closeFormModal() { document.getElementById('formModal').classList.add('hidden'); }
    function editData(r) { const it = rawDatabase.find(d => d.rowIndex === r); if (it) openFormModal(it); }

    async function deleteData(r) {
      if (!confirm('Hapus baris data ini dari sistem?')) return;
      
      // Ambil token dari Session Storage
      const currentToken = sessionStorage.getItem('adminToken');
      if (!currentToken) {
        alert("Sesi login tidak ditemukan. Silakan login ulang.");
        location.reload();
        return;
      }

      showLoading("Menghapus...");
      try { 
        const res = await fetch(API_URL, { 
          method: 'POST', 
          body: JSON.stringify({ 
            action: 'deleteData', 
            rowIndex: r,
            token: currentToken // <- Sisipkan token di sini
          }) 
        }); 
        
        const result = await res.json();
        if (result.status === 'error') {
          alert(result.message);
          // Paksa user login ulang jika pesan error adalah tentang akses ditolak
          if(result.message.includes("Akses Ditolak")) location.reload();
        } else {
          fetchData(); 
        }
      } catch (e) { 
        hideLoading(); 
        alert('Gagal menghapus.'); 
      }
    }

    // ==========================================
    // PROSES UPLOAD FOTO & SIMPAN
    // ==========================================
    async function handleFormSubmit(e) {
      e.preventDefault();
      closeFormModal();
      
      // Ambil token dari Session Storage
      const currentToken = sessionStorage.getItem('adminToken');
      if (!currentToken) {
        alert("Sesi login tidak ditemukan. Silakan login ulang.");
        location.reload();
        return;
      }

      showLoading("Sedang mengunggah foto ke Google Drive & menyimpan data...");

      const fileInput = document.getElementById('formFotoFile');
      let fotoFileData = null;
      if (fileInput.files.length > 0) {
        const file = fileInput.files[0];
        const base64full = await getBase64(file);
        fotoFileData = { filename: file.name, mimeType: file.type, base64: base64full.split(',')[1] };
      }

      const formData = {
        rowIndex: document.getElementById('formRowIndex').value,
        tanggal: document.getElementById('formTanggal').value,
        porsi: document.getElementById('formPorsi').value,
        jumlahPorsi: document.getElementById('formJumlahPorsi').value,
        menu: document.getElementById('formMenu').value,
        energi: document.getElementById('formEnergi').value,
        protein: document.getElementById('formProtein').value,
        lemak: document.getElementById('formLemak').value,
        karbohidrat: document.getElementById('formKarbohidrat').value,
        serat: document.getElementById('formSerat').value,
        fotoUrl: document.getElementById('formFotoUrl_hidden').value,
        fotoFile: fotoFileData
      };

      try {
        const res = await fetch(API_URL, { 
          method: 'POST', 
          body: JSON.stringify({ 
            action: 'saveData', 
            data: formData,
            token: currentToken // <- Sisipkan token di sini
          }) 
        });
        
        const result = await res.json();
        if (result.status === 'error') {
          alert(result.message);
          if(result.message.includes("Akses Ditolak")) location.reload();
        } else {
          fetchData(); 
        }
      } catch (err) { 
        hideLoading(); 
        alert('Gagal menyimpan.'); 
      }
    }

    // ==========================================
    // LOGIKA MODAL PROFIL
    // ==========================================
    function openProfilModal() {
      document.getElementById('profilModal').classList.remove('hidden');
      // Isi otomatis form dengan data saat ini
      document.getElementById('formProfilNama').value = currentProfil.nama || '';
      document.getElementById('formProfilAlamat').value = currentProfil.alamat || '';
      document.getElementById('formProfilLogo').value = ''; // Kosongkan file input
    }

    function closeProfilModal() {
      document.getElementById('profilModal').classList.add('hidden');
    }

    async function handleProfilSubmit(e) {
      e.preventDefault();
      closeProfilModal();
      
      // Ambil token dari Session Storage
      const currentToken = sessionStorage.getItem('adminToken');
      if (!currentToken) {
        alert("Sesi login tidak ditemukan. Silakan login ulang.");
        location.reload();
        return;
      }

      showLoading("Menyimpan data profil & mengunggah logo...");

      const fileInput = document.getElementById('formProfilLogo');
      let fotoFileData = null;
      
      if (fileInput.files.length > 0) {
        const file = fileInput.files[0];
        const base64full = await getBase64(file);
        fotoFileData = { filename: "LOGO_" + file.name, mimeType: file.type, base64: base64full.split(',')[1] };
      }

      const formData = {
        nama: document.getElementById('formProfilNama').value,
        alamat: document.getElementById('formProfilAlamat').value,
        logoUrl: currentProfil.logoUrl,
        fotoFile: fotoFileData
      };

      try {
        const res = await fetch(API_URL, { 
          method: 'POST', 
          body: JSON.stringify({ 
            action: 'saveProfil', 
            data: formData,
            token: currentToken // <- Sisipkan token di sini
          }) 
        });
        
        const result = await res.json();
        if (result.status === 'error') {
          alert(result.message);
          if(result.message.includes("Akses Ditolak")) location.reload();
        } else {
          fetchData(); 
        }
      } catch (err) { 
        hideLoading(); 
        alert('Gagal menyimpan profil.'); 
      }
    }
  </script>
</body>
</html>
