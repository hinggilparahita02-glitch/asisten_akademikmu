@php
    $navItems = [
        ['page' => 'dashboard', 'label' => 'Dashboard', 'icon' => '🏠'],
        ['page' => 'kalender', 'label' => 'Kalender', 'icon' => '📅'],
        ['page' => 'study-timer', 'label' => 'Study Timer', 'icon' => '⏱️'],
        ['page' => 'checklist.index', 'label' => 'Checklist', 'icon' => '✅'],
        ['page' => 'catatan', 'label' => 'Catatan', 'icon' => '📝'],
        ['page' => 'pencapaian', 'label' => 'Pencapaian', 'icon' => '🏆'],
        ['page' => 'mata-kuliah', 'label' => 'Mata Kuliah', 'icon' => '📚'],
        ['page' => 'simulasi', 'label' => 'Simulasi', 'icon' => '🎯'],
    ];
@endphp

<header class="main-header">
    <div class="logo-section">
        <div class="logo-text">
            <h1>Asisten Akademik Harian</h1>
            {{-- Menggunakan auth() agar tidak error jika variable userDetails tidak dikirim dari controller --}}
            <p>{{ session('user_name') ?? 'Guest' }}</p>
        </div>
    </div>
</header>

<nav class="main-nav">
    <ul class="nav-tabs">
        @foreach ($navItems as $item)
            <li class="nav-item">
                <a href="{{ route($item['page']) }}" 
                   class="nav-link {{ Request::routeIs($item['page']) ? 'active' : '' }}">
                    <span class="nav-icon">{{ $item['icon'] }}</span>
                    <span class="nav-label">{{ $item['label'] }}</span>
                </a>
            </li>
        @endforeach
    </ul>
</nav>