@include('layouts.header') {{-- Di sini link CSS harus benar --}}
@include('layouts.navbar')

<div class="container py-4">
    <div class="card shadow-sm border-0 text-white mb-4" style="background: linear-gradient(45deg, #FF416C, #FF4B2B); border-radius: 15px;">
        <div class="card-body d-flex justify-content-between align-items-center p-4">
            <div>
                <h6 class="text-uppercase fw-bold mb-1"><i class="fas fa-fire me-2"></i> Study Streak</h6>
                <h1 class="display-4 fw-bold mb-0">{{ $streakDays ?? 0 }} Hari 🔥</h1>
                <p class="mb-0 opacity-75">Terus pertahankan konsistensimu!</p>
            </div>
            <i class="fas fa-fire fa-4x opacity-25"></i>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4" style="border-radius: 15px;">
        <div class="card-body p-4">
            <h6 class="fw-bold mb-3"><i class="fas fa-trophy text-warning me-2"></i> Progress Pencapaian</h6>
            <div class="d-flex justify-content-between small mb-1">
                <span>Total Pencapaian</span>
                <span class="fw-bold">0 / 16</span>
            </div>
            <div class="progress mb-4" style="height: 10px; border-radius: 5px;">
                <div class="progress-bar bg-primary" style="width: 0%"></div>
            </div>
            
            <div class="row text-center g-2">
                <div class="col-6 col-md-3"><div class="p-2 border rounded bg-light small text-muted">🔥 Streak</div></div>
                <div class="col-6 col-md-3"><div class="p-2 border rounded bg-light small text-muted">⏰ Waktu Belajar</div></div>
                <div class="col-6 col-md-3"><div class="p-2 border rounded bg-light small text-muted">✅ Tugas</div></div>
                <div class="col-6 col-md-3"><div class="p-2 border rounded bg-light small text-muted">⭐ IPK</div></div>
            </div>
        </div>
    </div>

    <h6 class="fw-bold mb-3 text-secondary"><i class="fas fa-lock me-2"></i> Pencapaian Terkunci (16)</h6>
    <div class="row g-3">
        @foreach($achievementsByCategory as $category => $items)
            @foreach($items as $item)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm border-0 bg-light opacity-75" style="border-radius: 12px;">
                    <div class="card-body d-flex align-items-center p-3">
                        <div class="me-3 p-3 bg-white rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fas fa-lock text-muted"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0 text-muted small">{{ $item->name }}</h6>
                            <small class="text-secondary d-block" style="font-size: 0.7rem;">{{ $item->description }}</small>
                            <span class="badge bg-white text-muted border mt-2" style="font-size: 0.65rem;">🔒 Terkunci</span>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        @endforeach
    </div>
</div>

@include('layouts.footer')