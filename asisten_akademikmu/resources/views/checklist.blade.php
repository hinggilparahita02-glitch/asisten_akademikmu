@include('layouts.header')
@include('layouts.navbar')

@php
    $dailySuggestions = ['Bangun pagi 🌅', 'Olahraga 30 menit 💪', 'Baca buku 📖', 'Review materi kemarin 📚', 'Minum air 8 gelas 💧', 'Tidur sebelum jam 11 😴'];
    $goalSuggestions = ['Selesaikan tugas mingguan', 'Baca 1 bab buku kuliah', 'Kerjakan latihan soal', 'Pelajari materi baru', 'Buat catatan ringkasan'];
@endphp

<div class="checklist-grid">
    {{-- SEKSI RUTINITAS HARIAN --}}
    <div class="checklist-section">
        <div class="checklist-header">
            <div class="checklist-title">
                <span>🌅</span>
                <span>Rutinitas Harian</span>
            </div>
            <span class="checklist-progress" id="daily-progress">
                {{ $tasks->where('type', 'daily')->where('is_completed', 1)->count() }}/{{ $tasks->where('type', 'daily')->count() }}
            </span>
        </div>

        <div class="progress-bar" style="margin-bottom: var(--spacing-lg);">
            @php
                $dailyTotal = $tasks->where('type', 'daily')->count();
                $dailyDone = $tasks->where('type', 'daily')->where('is_completed', 1)->count();
                $dailyPercent = $dailyTotal > 0 ? ($dailyDone / $dailyTotal) * 100 : 0;
            @endphp
            <div class="progress-fill blue" id="daily-progress-bar" style="width: {{ $dailyPercent }}%"></div>
        </div>

        <div class="checklist-items" id="daily-items">
            @forelse($tasks->where('type', 'daily') as $item)
                <div class="checklist-item {{ $item->is_completed ? 'completed' : '' }}">
                    <div class="custom-checkbox {{ $item->is_completed ? 'checked' : '' }}">
                        {{ $item->is_completed ? '✓' : '' }}
                    </div>
                    <span class="checklist-text">{{ $item->title }}</span>
                </div>
            @empty
                <p style="text-align: center; color: gray; padding: 20px;">Belum ada rutinitas.</p>
            @endforelse
        </div>

        <div class="add-checklist-input">
            <input type="text" id="new-daily-item" placeholder="Tambah rutinitas baru...">
            <button onclick="addItem('daily')">+</button>
        </div>

        <div class="quick-add-suggestions">
            @foreach ($dailySuggestions as $suggestion)
                <button class="quick-add-btn" onclick="quickAdd('daily', '{{ $suggestion }}')">
                    + {{ $suggestion }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- SEKSI STUDY GOALS --}}
    <div class="checklist-section">
        <div class="checklist-header">
            <div class="checklist-title">
                <span>🎯</span>
                <span>Study Goals</span>
            </div>
            <span class="checklist-progress" id="goals-progress">
                {{ $tasks->where('type', 'goal')->where('is_completed', 1)->count() }}/{{ $tasks->where('type', 'goal')->count() }}
            </span>
        </div>

        <div class="progress-bar" style="margin-bottom: var(--spacing-lg);">
            @php
                $goalTotal = $tasks->where('type', 'goal')->count();
                $goalDone = $tasks->where('type', 'goal')->where('is_completed', 1)->count();
                $goalPercent = $goalTotal > 0 ? ($goalDone / $goalTotal) * 100 : 0;
            @endphp
            <div class="progress-fill purple" id="goals-progress-bar" style="width: {{ $goalPercent }}%"></div>
        </div>

        <div class="checklist-items" id="goal-items">
            @forelse($tasks->where('type', 'goal') as $item)
                <div class="checklist-item {{ $item->is_completed ? 'completed' : '' }}">
                    <div class="custom-checkbox {{ $item->is_completed ? 'checked' : '' }}">
                        {{ $item->is_completed ? '✓' : '' }}
                    </div>
                    <span class="checklist-text">{{ $item->title }}</span>
                </div>
            @empty
                <p style="text-align: center; color: gray; padding: 20px;">Belum ada target belajar.</p>
            @endforelse
        </div>

        {{-- PERBAIKAN: Menambahkan input yang hilang untuk Study Goals --}}
        <div class="add-checklist-input">
            <input type="text" id="new-goal-item" placeholder="Tambah target belajar...">
            <button onclick="addItem('goal')">+</button>
        </div>

        <div class="quick-add-suggestions">
            @foreach ($goalSuggestions as $suggestion)
                <button class="quick-add-btn" onclick="quickAdd('goal', '{{ $suggestion }}')">
                    + {{ $suggestion }}
                </button>
            @endforeach
        </div>
    </div>
</div>

@include('layouts.footer')

<script>
    async function addItem(type) {
        const inputId = type === 'daily' ? 'new-daily-item' : 'new-goal-item';
        const input = document.getElementById(inputId);
        const title = input.value.trim();

        if (!title) {
            alert('Masukkan judul item!');
            return;
        }

        try {
            // Pastikan bagian ini menggunakan route('checklist.store')
            const response = await fetch("{{ route('checklist.store') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ title: title, type: type })
            });

            const result = await response.json();

            if (response.ok && result.success) {
                input.value = '';
                window.location.reload();
            } else {
                alert('Gagal menyimpan: ' + (result.message || 'Error tidak diketahui'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Gagal menyambung ke server. Pastikan Route [checklist.store] sudah ada.');
        }
    }

    async function quickAdd(type, title) {
        try {
            const response = await fetch("{{ route('checklist.store') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ title: title, type: type })
            });

            if (response.ok) {
                window.location.reload();
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }
</script>