<?php
require_once 'config.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Catatan</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen pb-24">

    <!-- NAVBAR -->
    <nav class="bg-white border-b border-slate-200 sticky top-0 z-30 px-4 py-3">
        <div class="max-w-6xl mx-auto flex justify-between items-center">
            <span class="text-xl font-bold text-indigo-600">iNotes 📝</span>
            <div class="flex items-center gap-4">
                <span class="text-sm text-slate-600 hidden sm:inline">Halo, <?= htmlspecialchars($_SESSION['user_name']) ?></span>
               <a href="logout.php" class="p-2 rounded-xl text-slate-500 hover:text-red-600 hover:bg-red-50 dark:text-slate-400 dark:hover:text-red-400 dark:hover:bg-red-950/30 transition-all duration-200" title="Keluar">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                </svg>
                </a>
            </div>
        </div>
    </nav>

    <!-- CONTAINER UTAMA -->
    <main class="max-w-6xl mx-auto px-4 mt-8">
        <!-- Area Pencarian & Filter Ringan -->
        <div class="mb-6">
            <input type="text" id="search-input" placeholder="Cari catatan..." class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white">
        </div>

        <!-- GRID CATATAN (Responsive: 1 kolom di HP, bertahap hingga 3 kolom di Desktop) -->
        <div id="notes-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            <!-- Data dimuat dinamis dari JavaScript -->
        </div>
    </main>

    <!-- TOMBOL TAMBAH FLOAT (Mobile-Friendly ala Aplikasi Android/iOS) -->
    <button id="btn-add-note" class="fixed bottom-6 right-6 bg-indigo-600 hover:bg-indigo-700 text-white p-4 rounded-full shadow-lg transition-transform hover:scale-110 active:scale-95 z-40">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
    </button>

    <!-- MODAL FORM (TAMBAH / EDIT CATATAN) -->
    <div id="note-modal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm hidden items-center justify-center p-4 z-50">
        <div class="bg-white rounded-2xl w-full max-w-lg p-6 shadow-xl transform transition-all flex flex-col max-h-[90vh]">
            <div class="flex justify-between items-center mb-4">
                <h3 id="modal-title" class="text-lg font-bold text-slate-800">Catatan Baru</h3>
                <button id="btn-close-modal" class="text-slate-400 hover:text-slate-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            
            <form id="note-form" class="space-y-4 flex-1 flex flex-col overflow-y-auto">
                <input type="hidden" id="note-id">
                
                <div>
                    <input type="text" id="note-title" placeholder="Judul" required class="w-full text-lg font-semibold border-b border-slate-100 focus:border-indigo-500 focus:outline-none py-1">
                </div>
                
                <div class="flex-1 min-h-[150px] flex flex-col">
                    <textarea id="note-content" placeholder="Tulis catatan Anda di sini..." required class="w-full flex-1 resize-none focus:outline-none py-1 text-slate-700"></textarea>
                </div>

                <!-- Opsi Tambahan Fitur: Pin & Warna -->
                <div class="flex flex-wrap gap-4 items-center justify-between border-t border-slate-100 pt-4">
                    <div class="flex items-center gap-2">
                        <label class="text-xs font-semibold text-slate-500">Pilih Warna:</label>
                        <input type="color" id="note-color" value="#ffffff" class="w-8 h-8 rounded-md cursor-pointer border-0">
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" id="note-pin" class="rounded text-indigo-600 focus:ring-indigo-500">
                        <label for="note-pin" class="text-sm text-slate-600 font-medium">Sematkan di atas</label>
                    </div>
                </div>

                <div class="flex justify-end gap-2 border-t border-slate-100 pt-4">
                    <button type="button" id="btn-delete-note" class="hidden px-4 py-2 bg-red-50 hover:bg-red-100 text-red-600 rounded-xl font-semibold text-sm transition-colors">Hapus</button>
                    <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-semibold text-sm shadow-md shadow-indigo-100 transition-colors">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- KONTAINER TOAST NOTIFIKASI (Melayang di pojok kanan bawah pada desktop, atau di tengah bawah pada HP) -->
    <div id="toast-container" class="fixed bottom-24 right-6 left-6 sm:left-auto sm:bottom-6 sm:w-80 flex flex-col gap-2 z-50 pointer-events-none"></div>

    <!-- JAVASCRIPT LOGIC INTERAKTIF -->
    <script>
        let allNotes = [];
        const notesGrid = document.getElementById('notes-grid');
        const searchInput = document.getElementById('search-input');
        const noteModal = document.getElementById('note-modal');
        const noteForm = document.getElementById('note-form');
        
        // Modal Form Inputs
        const noteIdInput = document.getElementById('note-id');
        const noteTitleInput = document.getElementById('note-title');
        const noteContentInput = document.getElementById('note-content');
        const noteColorInput = document.getElementById('note-color');
        const notePinInput = document.getElementById('note-pin');
        
        // Buttons
        const btnAddNote = document.getElementById('btn-add-note');
        const btnCloseModal = document.getElementById('btn-close-modal');
        const btnDeleteNote = document.getElementById('btn-delete-note');
        const modalTitle = document.getElementById('modal-title');

        // Mengambil seluruh data catatan saat load awal
        async function fetchNotes() {
            try {
                const res = await fetch('notes_api.php');
                allNotes = await res.json();
                renderNotes(allNotes);
            } catch (err) {
                console.error("Gagal mengambil data:", err);
            }
        }

        // Render data catatan ke dalam Grid HTML (Versi Auto-Contrast)
        function renderNotes(notes) {
            notesGrid.innerHTML = '';
            if (notes.length === 0) {
                notesGrid.innerHTML = `<div class="col-span-full text-center py-12 text-slate-400 font-medium">Belum ada catatan. Klik tombol plus di bawah untuk membuat!</div>`;
                return;
            }

            notes.forEach(note => {
                const card = document.createElement('div');
                card.style.backgroundColor = note.color || '#ffffff';
                card.className = `p-5 rounded-2xl shadow-sm border border-slate-200/60 cursor-pointer hover:shadow-md transition-all relative group flex flex-col justify-between min-h-[140px]`;
                
                // Ambil kelas warna teks yang pas untuk background ini
                const textColor = getContrastColor(note.color);

                //efek melayang, bayangan lembut, dan transisi animasi mikro saat di-hover
                card.className = `p-5 rounded-2xl shadow-sm border border-slate-200/60 dark:border-slate-800/40 cursor-pointer relative group flex flex-col justify-between min-h-[140px] transition-all duration-300 ease-in-out hover:-translate-y-1.5 hover:shadow-xl hover:border-indigo-500/20`;

                // Menghindari serangan XSS dengan memfilter text isi catatan
                const safeTitle = escapeHtml(note.title);
                const safeContent = escapeHtml(note.content).replace(/\n/g, '<br>');

                card.innerHTML = `
                    <div>
                        ${note.is_pinned == 1 ? '<span class="absolute top-3 right-3 text-indigo-600 text-xs font-bold bg-indigo-50 px-2 py-0.5 rounded-full shadow-sm">📌 Disematkan</span>' : ''}
                        <h4 class="font-bold text-base mb-2 pr-20 line-clamp-1 ${textColor.title}">${safeTitle}</h4>
                        <p class="text-sm line-clamp-4 leading-relaxed ${textColor.content}">${safeContent}</p>
                    </div>
                    <div class="text-[10px] mt-4 text-right ${textColor.date}">
                        ${new Date(note.updated_at).toLocaleDateString('id-ID', {day: 'numeric', month: 'short'})}
                    </div>
                `;
                
                card.addEventListener('click', () => openModal(note));
                notesGrid.appendChild(card);
            });
        }

        // Buka modal untuk mode tambah atau edit
        function openModal(note = null) {
            noteModal.classList.remove('hidden');
            noteModal.classList.add('flex');
            
            if (note) {
                modalTitle.innerText = "Ubah Catatan";
                noteIdInput.value = note.id;
                noteTitleInput.value = note.title;
                noteContentInput.value = note.content;
                noteColorInput.value = note.color || '#ffffff';
                notePinInput.checked = note.is_pinned == 1;
                btnDeleteNote.classList.remove('hidden');
            } else {
                modalTitle.innerText = "Catatan Baru";
                noteForm.reset();
                noteIdInput.value = '';
                noteColorInput.value = '#ffffff';
                btnDeleteNote.classList.add('hidden');
            }
        }

        function closeModal() {
            noteModal.classList.remove('flex');
            noteModal.classList.add('hidden');
        }

        // Kirim Aksi Create/Update ke Backend via Fetch API
        noteForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const id = noteIdInput.value;
        const action = id ? 'update' : 'create';
        
        const payload = {
            id: id ? parseInt(id) : null,
            title: noteTitleInput.value,
            content: noteContentInput.value,
            color: noteColorInput.value,
            is_pinned: notePinInput.checked ? 1 : 0
        };

        try {
            const res = await fetch(`notes_api.php?action=${action}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            
            const result = await res.json();
            if (result.success) {
                closeModal();
                fetchNotes();
                
                // --- NOTIFIKASI DI SINI ---
                if (action === 'create') {
                    showToast('Catatan berhasil ditambahkan! 🎉', 'success');
                } else {
                    showToast('Catatan berhasil diperbarui! 📝', 'info');
                }
            } else {
                showToast('Gagal menyimpan catatan.', 'danger');
            }
        } catch (err) {
            showToast('Terjadi kesalahan koneksi.', 'danger');
        }
    });

        // Kirim Aksi Hapus Catatan
        btnDeleteNote.addEventListener('click', async () => {
        if (!confirm('Apakah Anda yakin ingin menghapus catatan ini?')) return;
        
        try {
            const res = await fetch('notes_api.php?action=delete', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: parseInt(noteIdInput.value) })
            });

            const result = await res.json();
            if (result.success) {
                closeModal();
                fetchNotes();
                
                // --- NOTIFIKASI DI SINI ---
                showToast('Catatan berhasil dihapus. 🗑️', 'danger');
            } else {
                showToast('Gagal menghapus catatan.', 'danger');
            }
        } catch (err) {
            showToast('Terjadi kesalahan koneksi.', 'danger');
        }
    });

        // Fungsi untuk menentukan apakah teks harus berwarna gelap atau terang berdasarkan background hex
            function getContrastColor(hexColor) {
            // Jika tidak ada warna, default ke gelap (karena background default putih)
            if (!hexColor) return { title: 'text-slate-800', content: 'text-slate-600', date: 'text-slate-400' };
            
            // Hapus karakter '#' jika ada
            const hex = hexColor.replace('#', '');
            
            // Ubah hex ke nilai RGB
            const r = parseInt(hex.substr(0, 2), 16);
            const g = parseInt(hex.substr(2, 2), 16);
            const b = parseInt(hex.substr(4, 2), 16);
            
            // Hitung tingkat kecerahan warna menggunakan rumus standar YIQ
            const yiq = ((r * 299) + (g * 587) + (b * 114)) / 1000;
            
            // Jika yiq >= 128 berarti warna terang -> gunakan teks gelap
            // Jika yiq < 128 berarti warna gelap -> gunakan teks terang
            if (yiq >= 128) {
                return {
                    title: 'text-slate-800',
                    content: 'text-slate-600',
                    date: 'text-slate-400'
                };
            } else {
                return {
                    title: 'text-white',
                    content: 'text-slate-200',
                    date: 'text-slate-300/80'
                };
            }
        }

    // Fungsi untuk memicu Toast Notification yang interaktif
    function showToast(message, type = 'success') {
    const container = document.getElementById('toast-container');
    
    // Buat elemen toast baru
    const toast = document.createElement('div');
    
    // Tentukan warna berdasarkan tipe notifikasi
    let bgColor = 'bg-emerald-600'; // Success (hijau)
    if (type === 'danger') bgColor = 'bg-rose-600'; // Delete/Error (merah)
    if (type === 'info') bgColor = 'bg-blue-600'; // Edit/Update (biru)

    toast.className = `${bgColor} text-white px-4 py-3 rounded-xl shadow-lg flex items-center justify-between gap-3 transform translate-y-4 opacity-0 transition-all duration-300 ease-out pointer-events-auto text-sm font-medium`;
    
    // Isi konten toast (pesan + tombol close silang)
        toast.innerHTML = `
            <span>${message}</span>
            <button class="hover:text-white/80 focus:outline-none transition-colors" onclick="this.parentElement.remove()">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        `;

        // Masukkan ke dalam container
        container.appendChild(toast);

        // Trigger animasi muncul (sedikit jeda agar transisi CSS berjalan)
        setTimeout(() => {
            toast.classList.remove('translate-y-4', 'opacity-0');
        }, 10);

        // Hapus otomatis setelah 3.5 detik dengan efek animasi keluar
        setTimeout(() => {
            toast.classList.add('translate-y-2', 'opacity-0');
            setTimeout(() => {
                toast.remove();
            }, 300); // Tunggu animasi transisi keluar selesai sebelum dihapus dari DOM
        }, 3500);
    }

        // Logika Pencarian Catatan Instan (Client-Side Filter)
        searchInput.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase();
            const filtered = allNotes.filter(n => 
                n.title.toLowerCase().includes(query) || 
                n.content.toLowerCase().includes(query)
            );
            renderNotes(filtered);
        });

        // Helper anti XSS injection
        function escapeHtml(text) {
            return text.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
        }

        btnAddNote.addEventListener('click', () => openModal());
        btnCloseModal.addEventListener('click', closeModal);
        window.addEventListener('click', (e) => { if (e.target === noteModal) closeModal(); });

        // Load data awal ketika halaman dibuka
        fetchNotes();
    </script>
</body>
</html>