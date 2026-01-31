@include('layouts.header')
@include('layouts.navbar')
@include('layouts.footer')


<div class="subjects-container">
    <!-- Add Subject Form -->
    <div class="add-subject-card">
        <h3>📚 Tambah Mata Kuliah</h3>
        <form id="add-subject-form" class="add-subject-form">
            <div class="form-group">
                <label for="subject-name">Nama Mata Kuliah</label>
                <input type="text" id="subject-name" placeholder="Contoh: Algoritma" required>
            </div>
            <div class="form-group">
                <label for="subject-credits">SKS</label>
                <input type="number" id="subject-credits" value="3" min="1" max="6" required>
            </div>
            <div class="form-group">
                <label for="subject-grade">Nilai (opsional)</label>
                <input type="number" id="subject-grade" min="0" max="100" placeholder="Contoh: 85">
                <small style="color: var(--text-muted); display: block; margin-top: 4px;">
                    Grade: <span id="grade-preview">-</span>
                </small>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Tambah</button>
        </form>

        <div style="margin-top: var(--spacing-lg); padding: var(--spacing-md); background: var(--bg-primary); border-radius: var(--radius-md);">
            <strong>Konversi Nilai:</strong>
            <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 4px;">
                85-100 = A | 80-84 = A- | 75-79 = B+ | 70-74 = B | 65-69 = B- | 60-64 = C+ | 55-59 = C | 50-54 = C- | 45-49 = D | 40-44 = D+ | 0-39 = E
            </div>
        </div>

        <div
            style="margin-top: var(--spacing-lg); padding: var(--spacing-md); background: var(--card-purple); border-radius: var(--radius-md); text-align: center;">
            <div style="font-size: 0.875rem; color: var(--text-secondary);">IPK Saat Ini</div>
            <div style="font-size: 2rem; font-weight: 700; color: var(--primary-purple);">
                <?= number_format($currentGPA, 2) ?>
            </div>
        </div>
    </div>

    <!-- Subjects List -->
    <div class="subjects-list-card">
        <h3 style="margin-bottom: var(--spacing-lg);">📖 Daftar Mata Kuliah</h3>

        <?php if (empty($subjects)): ?>
            <div class="empty-state">
                <p>Belum ada mata kuliah yang ditambahkan</p>
            </div>
        <?php else: ?>
            <table class="subjects-table">
                <thead>
                    <tr>
                        <th>Mata Kuliah</th>
                        <th>SKS</th>
                        <th>Nilai</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="subjects-tbody">
                    <?php foreach ($subjects as $subject): ?>
                        <tr data-subject-id="<?= $subject['id'] ?>">
                            <td>
                                <?= sanitize($subject['name']) ?>
                            </td>
                            <td>
                                <?= $subject['credits'] ?>
                            </td>
                            <td>
                                <?php if ($subject['grade']): ?>
                                    <span class="grade-badge <?= $subject['grade'][0] ?>">
                                        <?= $subject['grade'] ?>
                                    </span>
                                <?php else: ?>
                                    <span style="color: var(--text-muted);">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button class="note-action-btn" onclick="editSubject(<?= $subject['id'] ?>)"
                                    title="Edit">✏️</button>
                                <button class="note-action-btn" onclick="deleteSubject(<?= $subject['id'] ?>)"
                                    title="Hapus">🗑️</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div
                style="margin-top: var(--spacing-lg); padding: var(--spacing-md); background: var(--bg-primary); border-radius: var(--radius-md);">
                <strong>Total SKS:</strong>
                {{ $subjects->sum('credits') }}
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Edit Subject Modal -->
<div class="modal-overlay" id="edit-subject-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Edit Mata Kuliah</h3>
            <button class="modal-close" onclick="closeModal('edit-subject-modal')">×</button>
        </div>
        <div class="modal-body">
            <form id="edit-subject-form">
                <input type="hidden" id="edit-subject-id">
                <div class="form-group">
                    <label for="edit-subject-name">Nama Mata Kuliah</label>
                    <input type="text" id="edit-subject-name" required>
                </div>
                <div class="form-group">
                    <label for="edit-subject-credits">SKS</label>
                    <input type="number" id="edit-subject-credits" min="1" max="6" required>
                </div>
                <div class="form-group">
                    <label for="edit-subject-grade">Nilai (0-100)</label>
                    <input type="number" id="edit-subject-grade" min="0" max="100" placeholder="Contoh: 85">
                    <small style="color: var(--text-muted); display: block; margin-top: 4px;">
                        Grade: <span id="edit-grade-preview">-</span>
                    </small>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('edit-subject-modal')">Batal</button>
            <button class="btn btn-primary" onclick="saveSubjectEdit()">Simpan</button>
        </div>
    </div>
</div>

<script>
    // Convert number to grade letter
    function numberToGrade(score) {
        if (score === '' || score === null || isNaN(score)) return null;
        score = parseInt(score);
        if (score >= 85) return 'A';
        if (score >= 80) return 'A-';
        if (score >= 75) return 'B+';
        if (score >= 70) return 'B';
        if (score >= 65) return 'B-';
        if (score >= 60) return 'C+';
        if (score >= 55) return 'C';
        if (score >= 50) return 'C-';
        if (score >= 45) return 'D';
        if (score >= 40) return 'D+';
        return 'E';
    }

    // Update grade preview
    function updateGradePreview(inputId, previewId) {
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);
        const grade = numberToGrade(input.value);
        preview.textContent = grade || '-';
        preview.style.color = grade ? 'var(--primary-purple)' : 'var(--text-muted)';
        preview.style.fontWeight = grade ? '600' : 'normal';
    }

    // Add event listeners for grade preview
    document.getElementById('subject-grade').addEventListener('input', function() {
        updateGradePreview('subject-grade', 'grade-preview');
    });

    document.getElementById('edit-subject-grade').addEventListener('input', function() {
        updateGradePreview('edit-subject-grade', 'edit-grade-preview');
    });

    document.getElementById('add-subject-form').addEventListener('submit', async (e) => {
        e.preventDefault();

        const scoreValue = document.getElementById('subject-grade').value;
        const grade = numberToGrade(scoreValue);

        const data = {
            name: document.getElementById('subject-name').value,
            credits: document.getElementById('subject-credits').value,
            grade: grade
        };

        const result = await apiRequest('api/subjects.php', 'POST', data);

        if (result.success) {
            showToast('Mata kuliah ditambahkan! 📚', 'success');
            location.reload();
        } else {
            showToast(result.error || 'Gagal menambahkan', 'error');
        }
    });

    async function editSubject(id) {
        const result = await apiRequest(`api/subjects.php?id=${id}`);

        if (result.success) {
            const subject = result.data;
            document.getElementById('edit-subject-id').value = id;
            document.getElementById('edit-subject-name').value = subject.name;
            document.getElementById('edit-subject-credits').value = subject.credits;
            // Keep the score field empty if no grade, or show approximate score
            document.getElementById('edit-subject-grade').value = subject.numeric_score || '';
            updateGradePreview('edit-subject-grade', 'edit-grade-preview');
            openModal('edit-subject-modal');
        }
    }

    async function saveSubjectEdit() {
        const scoreValue = document.getElementById('edit-subject-grade').value;
        const grade = numberToGrade(scoreValue);

        const data = {
            id: document.getElementById('edit-subject-id').value,
            name: document.getElementById('edit-subject-name').value,
            credits: document.getElementById('edit-subject-credits').value,
            grade: grade,
            numeric_score: scoreValue || null
        };

        const result = await apiRequest('api/subjects.php', 'PUT', data);

        if (result.success) {
            showToast('Perubahan disimpan! ✏️', 'success');
            location.reload();
        } else {
            showToast('Gagal menyimpan', 'error');
        }
    }

    async function deleteSubject(id) {
        if (!confirm('Hapus mata kuliah ini?')) return;

        const result = await apiRequest(`api/subjects.php?id=${id}`, 'DELETE');

        if (result.success) {
            showToast('Mata kuliah dihapus', 'success');
            location.reload();
        }
    }
</script>

@include('layouts.footer')