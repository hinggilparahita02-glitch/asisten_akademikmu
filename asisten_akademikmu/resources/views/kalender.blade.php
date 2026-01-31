@include('layouts.header')
@include('layouts.navbar')
@include('layouts.footer')

<div class="calendar-container">
    <div class="calendar-header">
        <div class="calendar-title">
            <span>📅</span>
            <span>Kalender Deadline</span>
        </div>
        <div class="calendar-nav">
            <button class="calendar-nav-btn" onclick="prevMonth()">‹</button>
            <span class="calendar-month" id="calendar-month-year"></span>
            <button class="calendar-nav-btn" onclick="nextMonth()">›</button>
        </div>
        <button class="today-btn" onclick="goToToday()">Hari Ini</button>
    </div>

    <div class="calendar-grid" id="calendar-grid">
        <div class="calendar-day-header">Min</div>
        <div class="calendar-day-header">Sen</div>
        <div class="calendar-day-header">Sel</div>
        <div class="calendar-day-header">Rab</div>
        <div class="calendar-day-header">Kam</div>
        <div class="calendar-day-header">Jum</div>
        <div class="calendar-day-header">Sab</div>
    </div>

    <div class="calendar-legend">
        <div class="legend-item">
            <div class="legend-color today"></div>
            <span>Hari Ini</span>
        </div>
        <div class="legend-item">
            <div class="legend-color pending"></div>
            <span>Tugas Belum Selesai</span>
        </div>
        <div class="legend-item">
            <div class="legend-color completed"></div>
            <span>Tugas Selesai</span>
        </div>
    </div>
</div>

<!-- Selected Date Tasks -->
<div class="task-section" style="margin-bottom: var(--spacing-xl);">
    <div class="section-header">
        <span>📋</span>
        <span id="selected-date-title">Tugas pada Hari Ini</span>
    </div>
    <div id="selected-date-tasks">
        <div class="empty-state">
            <p>Tidak ada tugas pada tanggal ini</p>
        </div>
    </div>
</div>

<!-- Upcoming Deadlines -->
<div class="task-section">
    <div class="section-header">
        <span>⏰</span>
        <span>Deadline Mendatang</span>
    </div>
    <div id="upcoming-deadlines">
        <div class="empty-state">
            <p>Tidak ada deadline mendatang! 🎉</p>
        </div>
    </div>
</div>

<!-- Add Task Modal -->
<div class="modal-overlay" id="task-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Tambah Tugas Baru</h3>
            <button class="modal-close" onclick="closeModal('task-modal')">×</button>
        </div>
        <div class="modal-body">
            <form id="task-form">
                <div class="form-group">
                    <label for="task-title">Judul Tugas</label>
                    <input type="text" id="task-title" name="title" placeholder="Masukkan judul tugas" required>
                </div>
                <div class="form-group">
                    <label for="task-description">Deskripsi (opsional)</label>
                    <textarea id="task-description" name="description" rows="3"
                        placeholder="Masukkan deskripsi tugas"></textarea>
                </div>
                <div class="form-group">
                    <label for="task-deadline">Deadline</label>
                    <input type="datetime-local" id="task-deadline" name="deadline" required>
                </div>
            
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('task-modal')">Batal</button>
            <button class="btn btn-primary" onclick="saveTask()">Simpan</button>
        </div>
    </div>
</div>

<style>
    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 1px;
        background: #e5e7eb;
        border-radius: var(--radius-md);
        overflow: hidden;
    }

    .add-task-btn {
        position: fixed;
        bottom: var(--spacing-xl);
        right: var(--spacing-xl);
        width: 56px;
        height: 56px;
        border-radius: var(--radius-full);
        background: var(--primary-gradient);
        color: white;
        font-size: 1.5rem;
        box-shadow: var(--shadow-lg);
        transition: var(--transition-fast);
    }

    .add-task-btn:hover {
        transform: scale(1.1);
    }

    textarea {
        width: 100%;
        padding: var(--spacing-md);
        border: 1px solid #e5e7eb;
        border-radius: var(--radius-md);
        resize: vertical;
    }
</style>

<button class="add-task-btn" onclick="openAddTaskModal()">+</button>

<script>
    const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    const dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

    let currentDate = new Date();
    let selectedDate = new Date();
    let tasksData = {};

    async function loadCalendar() {
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth() + 1;

        // Update header
        document.getElementById('calendar-month-year').textContent =
            `${monthNames[currentDate.getMonth()]} ${year}`;

        // Load tasks for this month
        const result = await apiRequest(`api/tasks.php?month=${month}&year=${year}`);
        if (result.success) {
            tasksData = {};
            result.data.forEach(task => {
                const date = task.deadline_date;
                if (!tasksData[date]) tasksData[date] = [];
                tasksData[date].push(task);
            });
        }

        renderCalendar();
        loadSelectedDateTasks();
        loadUpcomingDeadlines();
    }

    function renderCalendar() {
        const grid = document.getElementById('calendar-grid');

        // Clear existing days (keep headers)
        const headers = grid.querySelectorAll('.calendar-day-header');
        grid.innerHTML = '';
        headers.forEach(h => grid.appendChild(h));

        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();

        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        const startingDay = firstDay.getDay();
        const totalDays = lastDay.getDate();

        const today = new Date();
        const todayStr = formatDateStr(today);
        const selectedStr = formatDateStr(selectedDate);

        // Previous month days
        const prevMonthLastDay = new Date(year, month, 0).getDate();
        for (let i = startingDay - 1; i >= 0; i--) {
            const dayNum = prevMonthLastDay - i;
            const dayEl = createDayElement(dayNum, true);
            grid.appendChild(dayEl);
        }

        // Current month days
        for (let day = 1; day <= totalDays; day++) {
            const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            const dayEl = createDayElement(day, false, dateStr);

            if (dateStr === todayStr) {
                dayEl.classList.add('today');
            }
            if (dateStr === selectedStr) {
                dayEl.classList.add('selected');
            }

            // Add task dots
            if (tasksData[dateStr]) {
                const dotsContainer = document.createElement('div');
                dotsContainer.className = 'calendar-day-tasks';

                const pending = tasksData[dateStr].filter(t => !t.is_completed).length;
                const completed = tasksData[dateStr].filter(t => t.is_completed).length;

                if (pending > 0) {
                    const dot = document.createElement('span');
                    dot.className = 'calendar-task-dot pending';
                    dotsContainer.appendChild(dot);
                }
                if (completed > 0) {
                    const dot = document.createElement('span');
                    dot.className = 'calendar-task-dot completed';
                    dotsContainer.appendChild(dot);
                }

                dayEl.appendChild(dotsContainer);
            }

            grid.appendChild(dayEl);
        }

        // Next month days
        const remainingDays = 42 - (startingDay + totalDays);
        for (let day = 1; day <= remainingDays; day++) {
            const dayEl = createDayElement(day, true);
            grid.appendChild(dayEl);
        }
    }

    function createDayElement(day, isOtherMonth, dateStr = '') {
        const dayEl = document.createElement('div');
        dayEl.className = 'calendar-day' + (isOtherMonth ? ' other-month' : '');

        const dayNum = document.createElement('div');
        dayNum.className = 'calendar-day-number';
        dayNum.textContent = day;
        dayEl.appendChild(dayNum);

        if (!isOtherMonth && dateStr) {
            dayEl.onclick = () => selectDate(dateStr);
        }

        return dayEl;
    }

    function formatDateStr(date) {
        return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;
    }

    function selectDate(dateStr) {
        selectedDate = new Date(dateStr);
        renderCalendar();
        loadSelectedDateTasks();
    }

    async function loadSelectedDateTasks() {
        const dateStr = formatDateStr(selectedDate);
        const dayName = dayNames[selectedDate.getDay()];
        const day = selectedDate.getDate();
        const month = monthNames[selectedDate.getMonth()];
        const year = selectedDate.getFullYear();

        document.getElementById('selected-date-title').textContent =
            `Tugas pada ${dayName}, ${day} ${month} ${year}`;

        const result = await apiRequest(`api/tasks.php?date=${dateStr}`);
        const container = document.getElementById('selected-date-tasks');

        if (result.success && result.data.length > 0) {
            container.innerHTML = '<div class="task-list">' +
                result.data.map(task => `
                <div class="task-item" data-task-id="${task.id}">
                    <div class="task-checkbox ${task.is_completed ? 'completed' : ''}" 
                         onclick="toggleTask(${task.id}, ${task.is_completed})"></div>
                    <div class="task-info">
                        <div class="task-title" style="${task.is_completed ? 'text-decoration: line-through; color: var(--text-muted);' : ''}">${task.title}</div>
                        <div class="task-deadline">${task.subject_name || ''} • ${new Date(task.deadline).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })}</div>
                    </div>
                    <button class="note-action-btn" onclick="deleteTask(${task.id})" title="Hapus">🗑️</button>
                </div>
            `).join('') + '</div>';
        } else {
            container.innerHTML = '<div class="empty-state"><p>Tidak ada tugas pada tanggal ini</p></div>';
        }
    }

    async function loadUpcomingDeadlines() {
        const result = await apiRequest('api/tasks.php');
        const container = document.getElementById('upcoming-deadlines');

        if (result.success && result.data.length > 0) {
            const upcoming = result.data.slice(0, 5);
            container.innerHTML = '<div class="task-list">' +
                upcoming.map(task => `
                <div class="task-item">
                    <div class="task-checkbox" onclick="toggleTask(${task.id}, 0)"></div>
                    <div class="task-info">
                        <div class="task-title">${task.title}</div>
                        <div class="task-deadline ${getUrgencyClass(task.deadline)}">${formatDeadline(task.deadline)}</div>
                    </div>
                </div>
            `).join('') + '</div>';
        } else {
            container.innerHTML = '<div class="empty-state"><p>Tidak ada deadline mendatang! 🎉</p></div>';
        }
    }

    function getUrgencyClass(deadline) {
        const days = Math.ceil((new Date(deadline) - new Date()) / (1000 * 60 * 60 * 24));
        if (days <= 0) return 'overdue';
        if (days <= 3) return 'urgent';
        return '';
    }

    function formatDeadline(deadline) {
        const date = new Date(deadline);
        return `${dayNames[date.getDay()]}, ${date.getDate()} ${monthNames[date.getMonth()]}`;
    }

    function prevMonth() {
        currentDate.setMonth(currentDate.getMonth() - 1);
        loadCalendar();
    }

    function nextMonth() {
        currentDate.setMonth(currentDate.getMonth() + 1);
        loadCalendar();
    }

    function goToToday() {
        currentDate = new Date();
        selectedDate = new Date();
        loadCalendar();
    }

    async function loadSubjects() {
        const result = await apiRequest('api/subjects.php');
        if (result.success) {
            const select = document.getElementById('task-subject');
            select.innerHTML = '<option value="">Pilih Mata Kuliah</option>' +
                result.data.map(s => `<option value="${s.id}">${s.name}</option>`).join('');
        }
    }

    function openAddTaskModal() {
        const dateStr = formatDateStr(selectedDate);
        document.getElementById('task-deadline').value = dateStr + 'T23:59';
        document.getElementById('task-form').reset();
        document.getElementById('task-deadline').value = dateStr + 'T23:59';
        loadSubjects();
        openModal('task-modal');
    }

    async function saveTask() {
        const form = document.getElementById('task-form');
        const data = {
            title: document.getElementById('task-title').value,
            description: document.getElementById('task-description').value,
            deadline: document.getElementById('task-deadline').value,
            subject_id: document.getElementById('task-subject').value || null
        };

        if (!data.title || !data.deadline) {
            showToast('Judul dan deadline harus diisi!', 'warning');
            return;
        }

        const result = await apiRequest('api/tasks.php', 'POST', data);

        if (result.success) {
            showToast('Tugas berhasil ditambahkan! 📝', 'success');
            closeModal('task-modal');
            loadCalendar();
        } else {
            showToast('Gagal menambahkan tugas', 'error');
        }
    }

    async function toggleTask(taskId, currentStatus) {
        const result = await apiRequest('api/tasks.php', 'PUT', {
            id: taskId,
            is_completed: currentStatus ? 0 : 1
        });

        if (result.success) {
            showToast(currentStatus ? 'Tugas dibuka kembali' : 'Hebat! Tugas selesai! 🎉', 'success');
            loadCalendar();
        }
    }

    async function deleteTask(taskId) {
        if (!confirm('Hapus tugas ini?')) return;

        const result = await apiRequest(`api/tasks.php?id=${taskId}`, 'DELETE');
        if (result.success) {
            showToast('Tugas dihapus', 'success');
            loadCalendar();
        }
    }

    // Initialize
    loadCalendar();
</script>

@include('layouts.footer')