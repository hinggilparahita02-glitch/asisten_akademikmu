@include('layouts.header')
@include('layouts.navbar')

<div class="timer-container">
    <div class="timer-header">
        <span>⏱️</span>
        <span>Study Timer</span>
    </div>

    <div class="timer-subject" style="margin-bottom: 15px;">
        <label for="alarm-select">🔔 Pilih Suara Alarm</label>
        <select id="alarm-select" class="form-control">
            <option value="bell">Classic Bell</option>
            <option value="digital">Digital Beep</option>
            <option value="zen">Zen Chime</option>
        </select>
    </div>

    <div class="timer-modes">
        <button class="timer-mode-btn active" data-mode="pomodoro" data-minutes="30">
            🍅 Pomodoro (30m)
        </button>
        <button class="timer-mode-btn" data-mode="short_break" data-minutes="5">
            ☕ Break Pendek (5m)
        </button>
        <button class="timer-mode-btn" data-mode="long_break" data-minutes="15">
            🧘 Break Panjang (15m)
        </button>
        <button class="timer-mode-btn" data-mode="custom" data-minutes="45">
            ⏰ Custom (45m)
        </button>
    </div>

    <div class="timer-subject">
        <label for="subject-select">📚 Mata Kuliah</label>
        <select id="subject-select">
            <option value="">Pilih mata kuliah...</option>
            {{-- Menggunakan sintaks Blade agar tidak error --}}
            @foreach ($subjects as $subject)
                <option value="{{ $subject->id }}">
                    {{ $subject->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="timer-display">
        <div class="timer-time" id="timer-display">30:00</div>
    </div>

    <div class="timer-controls">
        <button class="timer-btn primary" id="start-btn" onclick="toggleTimer()">
            <span>▶</span> Mulai
        </button>
        <button class="timer-btn secondary" onclick="resetTimer()">
            <span>↺</span>
        </button>
    </div>

    <div style="text-align: center;">
        <div class="timer-count">
            🍅 Pomodoro Selesai Hari Ini: <span id="today-pomodoro-count">{{ $studyStats['total_sessions'] }}</span>
        </div>
    </div>
</div>

{{-- Grid Statistik --}}
<div class="timer-stats-grid">
    <div class="timer-stat-card">
        <span class="timer-stat-icon">⏱️</span>
        <div class="timer-stat-label">Hari Ini</div>
        <div class="timer-stat-value" id="today-minutes">{{ $studyStats['today_minutes'] }} menit</div>
    </div>
    <div class="timer-stat-card">
        <span class="timer-stat-icon">📈</span>
        <div class="timer-stat-label">Total Sesi</div>
        <div class="timer-stat-value" id="total-time">{{ $studyStats['total_sessions'] }}</div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let timerInterval = null;
        let remainingSeconds = 30 * 60;
        let isRunning = false;
        
        // Objek Suara Alarm (Bisa dicustom)
        const alarms = {
            bell: new Audio('https://actions.google.com/sounds/v1/alarms/bugle_tune.ogg'),
            digital: new Audio('https://actions.google.com/sounds/v1/alarms/alarm_clock_beep.ogg'),
            zen: new Audio('https://actions.google.com/sounds/v1/alarms/mechanical_clock_ringing.ogg')
        };

        function updateDisplay() {
            const mins = Math.floor(remainingSeconds / 60);
            const secs = remainingSeconds % 60;
            document.getElementById('timer-display').textContent = 
                `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
        }

        window.toggleTimer = function () {
            if (isRunning) {
                pauseTimer();
            } else {
                startTimer();
            }
        };

        function startTimer() {
            isRunning = true;
            document.getElementById('start-btn').innerHTML = '<span>⏸</span> Pause';
            timerInterval = setInterval(() => {
                remainingSeconds--;
                updateDisplay();
                if (remainingSeconds <= 0) {
                    clearInterval(timerInterval);
                    playAlarm();
                    alert("Waktu belajar selesai! 🎯");
                    resetTimer();
                }
            }, 1000);
        }

        function pauseTimer() {
            isRunning = false;
            clearInterval(timerInterval);
            document.getElementById('start-btn').innerHTML = '<span>▶</span> Lanjut';
        }

        window.resetTimer = function () {
            pauseTimer();
            const activeBtn = document.querySelector('.timer-mode-btn.active');
            remainingSeconds = parseInt(activeBtn.dataset.minutes) * 60;
            updateDisplay();
            document.getElementById('start-btn').innerHTML = '<span>▶</span> Mulai';
        };

        function playAlarm() {
            const selectedType = document.getElementById('alarm-select').value;
            const sound = alarms[selectedType];
            sound.play();
            // Berhenti otomatis setelah 5 detik
            setTimeout(() => { sound.pause(); sound.currentTime = 0; }, 5000);
        }

        // Mode Switcher
        document.querySelectorAll('.timer-mode-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.timer-mode-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                remainingSeconds = parseInt(this.dataset.minutes) * 60;
                updateDisplay();
                pauseTimer();
            });
        });

        // Double Click Custom
        const customBtn = document.querySelector('[data-mode="custom"]');
        customBtn.addEventListener('dblclick', function() {
            const mins = prompt("Masukkan menit custom:", "45");
            if(mins) {
                this.dataset.minutes = mins;
                this.innerHTML = `⏰ Custom (${mins}m)`;
                remainingSeconds = mins * 60;
                updateDisplay();
            }
        });
    });
</script>

@include('layouts.footer')