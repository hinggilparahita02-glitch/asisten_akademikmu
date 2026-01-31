@include('layouts.header')
@include('layouts.navbar')

<div class="greeting-card">
    <div class="greeting-header">
        <div class="greeting-text">
            <h2>Selamat Datang, {{ $userDetails->name ?? 'Mahasiswa' }}! 👋</h2>
            <p class="greeting-date">{{ Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
            <div class="greeting-quote">Jangan menyerah! Perjuanganmu akan membuahkan hasil! 🚀</div>
        </div>
        <div class="greeting-gpa">
            <div class="label">IPK Saat Ini</div>
            <div class="value">{{ number_format($stats['current_gpa'], 2) }}</div>
        </div>
    </div>
</div>

{{-- Stats Grid - Menampilkan 4 Kartu --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon blue">📘</div>
        <div class="stat-value">{{ $stats['subjects'] }}</div>
        <div class="stat-label">Mata Kuliah</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange">⚠️</div>
        <div class="stat-value">{{ $stats['active_tasks'] }}</div>
        <div class="stat-label">Tugas Aktif</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon purple">⭐</div>
        <div class="stat-value">{{ $stats['streak_days'] }}</div>
        <div class="stat-label">Hari Streak</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">⏱️</div>
        <div class="stat-value">{{ round($stats['total_study_minutes'] / 60, 1) }}h</div>
        <div class="stat-label">Waktu Belajar</div>
    </div>
</div>

{{-- Daftar Tugas --}}
<div class="tasks-container">
    <div class="task-card">
        <h3>📅 Tugas Hari Ini</h3>
        <div class="task-list">
            @forelse ($todayTasks as $task)
                <div class="task-item">
                    <div class="task-info">
                        <div class="task-title">{{ $task->title }}</div>
                        <div class="task-subject">{{ $task->subject_name }}</div>
                    </div>
                    <div class="task-status">✅</div>
                </div>
            @empty
                <div class="empty-state">
                    <p>Tidak ada tugas dengan deadline hari ini! ✨</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

@include('layouts.footer')