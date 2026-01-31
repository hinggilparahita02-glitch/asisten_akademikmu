@include('layouts.header')
@include('layouts.navbar')

<main class="container">
    <div class="notes-header">
        <div class="search-box">
            <span>🔍</span>
            <input type="text" id="search-notes" placeholder="Cari catatan..." oninput="searchNotes()">
        </div>
        <button class="add-btn" onclick="openAddNoteModal()">
            <span>+</span>
            <span>Catatan Baru</span>
        </button>
    </div>

    <div class="notes-grid" id="notes-container">
        <div class="loading-state">Memuat catatan...</div>
    </div>

    <div class="modal-overlay" id="note-modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="note-modal-title">Catatan Baru</h3>
                <button class="modal-close" onclick="closeModal('note-modal')">×</button>
            </div>
            <div class="modal-body">
                <form id="note-form">
                    @csrf
                    <input type="hidden" id="note-id">
                    <div class="form-group">
                        <label for="note-title">Judul</label>
                        <input type="text" id="note-title" placeholder="Judul catatan" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="note-content">Isi Catatan</label>
                        <textarea id="note-content" rows="5" placeholder="Tulis catatanmu di sini..." class="form-control"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Warna</label>
                        <div class="color-picker">
                            <div class="color-option yellow selected" data-color="yellow"></div>
                            <div class="color-option blue" data-color="blue"></div>
                            <div class="color-option green" data-color="green"></div>
                            <div class="color-option pink" data-color="pink"></div>
                            <div class="color-option purple" data-color="purple"></div>
                            <div class="color-option orange" data-color="orange"></div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeModal('note-modal')">Batal</button>
                <button class="btn btn-primary" onclick="saveNote()">Simpan</button>
            </div>
        </div>
    </div>
</main>

@include('layouts.footer')

<script>
    let selectedColor = 'yellow';
    let editingNoteId = null;

    // Menyesuaikan url API ke route Laravel (tanpa .php)
    const API_BASE_URL = '/api/notes'; 

    async function loadNotes(search = '') {
        const url = search ? `${API_BASE_URL}?search=${encodeURIComponent(search)}` : API_BASE_URL;
        try {
            const response = await fetch(url);
            const result = await response.json();
            const container = document.getElementById('notes-container');

            // Laravel biasanya langsung mengembalikan array atau objek data
            const notes = result.data || result; 

            if (notes.length > 0) {
                container.innerHTML = notes.map(note => `
                <div class="note-card ${note.color}" data-note-id="${note.id}">
                    <span class="note-pin ${note.is_pinned ? 'pinned' : ''}" 
                          onclick="togglePin(${note.id}, ${note.is_pinned})">📌</span>
                    <div class="note-title">${escapeHtml(note.title)}</div>
                    <div class="note-content">${escapeHtml(note.content || '')}</div>
                    <div class="note-footer">
                        <span>${formatDate(note.updated_at)}</span>
                        <div class="note-actions">
                            <button class="note-action-btn" onclick="editNote(${note.id})" title="Edit">✏️</button>
                            <button class="note-action-btn" onclick="deleteNote(${note.id})" title="Hapus">🗑️</button>
                        </div>
                    </div>
                </div>
            `).join('');
            } else {
                container.innerHTML = `
                <div class="empty-state" style="grid-column: 1/-1;">
                    <div class="icon">📝</div>
                    <p>Belum ada catatan. Buat catatan pertamamu!</p>
                </div>
            `;
            }
        } catch (error) {
            console.error("Gagal memuat catatan:", error);
        }
    }

    async function saveNote() {
        const title = document.getElementById('note-title').value.trim();
        const content = document.getElementById('note-content').value.trim();
        const id = document.getElementById('note-id').value;

        if (!title) return alert('Judul tidak boleh kosong!');

        const method = id ? 'PUT' : 'POST';
        const url = id ? `${API_BASE_URL}/${id}` : API_BASE_URL;

        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ title, content, color: selectedColor })
        });

        if (response.ok) {
            closeModal('note-modal');
            loadNotes();
        } else {
            alert('Gagal menyimpan catatan');
        }
    }

    async function editNote(id) {
        const response = await fetch(`${API_BASE_URL}/${id}`);
        const note = await response.json();

        if (note) {
            editingNoteId = id;
            document.getElementById('note-modal-title').textContent = 'Edit Catatan';
            document.getElementById('note-id').value = id;
            document.getElementById('note-title').value = note.title;
            document.getElementById('note-content').value = note.content || '';
            
            selectedColor = note.color;
            document.querySelectorAll('.color-option').forEach(c => {
                c.classList.toggle('selected', c.dataset.color === note.color);
            });

            openModal('note-modal');
        }
    }

    async function deleteNote(id) {
        if (!confirm('Hapus catatan ini?')) return;
        const response = await fetch(`${API_BASE_URL}/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        });
        if (response.ok) loadNotes();
    }

    async function togglePin(id, currentState) {
        await fetch(`${API_BASE_URL}/${id}/pin`, {
            method: 'PATCH',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        });
        loadNotes();
    }

    // Fungsi Helper
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function formatDate(dateStr) {
        const date = new Date(dateStr);
        return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
    }

    function searchNotes() {
        loadNotes(document.getElementById('search-notes').value);
    }

    function openAddNoteModal() {
        editingNoteId = null;
        document.getElementById('note-modal-title').textContent = 'Catatan Baru';
        document.getElementById('note-form').reset();
        document.getElementById('note-id').value = '';
        openModal('note-modal');
    }

    function openModal(id) { document.getElementById(id).style.display = 'flex'; }
    function closeModal(id) { document.getElementById(id).style.display = 'none'; }

    document.addEventListener('DOMContentLoaded', () => {
        loadNotes();
        document.querySelectorAll('.color-option').forEach(opt => {
            opt.onclick = () => {
                document.querySelectorAll('.color-option').forEach(c => c.classList.remove('selected'));
                opt.classList.add('selected');
                selectedColor = opt.dataset.color;
            };
        });
    });
</script>