@include('layouts.header')
@include('layouts.navbar')

<main class="content-wrapper">
    {{-- Container Utama sesuai gambar --}}
    <div class="simulation-container" style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px; padding: 20px;">
        
        {{-- Card Kiri: Input Target (Sesuai image_d8bfe8.png) --}}
        <div class="task-card" style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px;">
                <span style="font-size: 1.2rem;">⚙️</span>
                <h3 style="margin: 0; font-size: 1.1rem; color: #333;">Simulasi Target IPK</h3>
            </div>
            
            <div class="form-group">
                <label style="display: block; font-size: 0.9rem; color: #666; margin-bottom: 8px;">Target IPK Semester Depan</label>
                <div style="display: flex; gap: 10px;">
                    <input type="number" id="target-gpa" step="0.01" min="0" max="4" 
                           placeholder="Contoh: 3.80"
                           value="{{ $userDetails->target_gpa ?? '' }}" 
                           style="flex: 1; padding: 10px; border: 1px solid #e0e0e0; border-radius: 8px; background: #f8f9fa;">
                    
                    <button class="btn btn-primary" onclick="runSimulation()" 
                            style="background: #6c757d; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; gap: 5px;">
                        📈 Simulasi
                    </button>
                </div>
            </div>
        </div>

        {{-- Card Kanan: Tabel Mata Kuliah --}}
        <div class="task-card" style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); min-height: 200px; display: flex; flex-direction: column;">
            @if($subjects->isEmpty())
                <div style="margin: auto; text-align: center; color: #999;">
                    <p>Belum ada mata kuliah yang ditambahkan</p>
                </div>
            @else
                <div style="overflow-x: auto;">
                    <table class="subjects-table" id="simulation-table" style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="text-align: left; border-bottom: 1px solid #eee;">
                                <th style="padding: 12px; font-weight: 600; color: #555;">Mata Kuliah</th>
                                <th style="padding: 12px; font-weight: 600; color: #555;">SKS</th>
                                <th style="padding: 12px; font-weight: 600; color: #555;">Nilai Saat Ini</th>
                                <th style="padding: 12px; font-weight: 600; color: #555;">Target</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($subjects as $subject)
                            <tr data-subject-id="{{ $subject->id }}" data-credits="{{ $subject->credits }}" style="border-bottom: 1px solid #f9f9f9;">
                                <td style="padding: 12px; color: #333;">{{ $subject->name }}</td>
                                <td style="padding: 12px; color: #333;">{{ $subject->credits }}</td>
                                <td style="padding: 12px;">
                                    <select class="current-grade" style="padding: 6px; border-radius: 5px; border: 1px solid #ddd;">
                                        <option value="">-</option>
                                        @foreach(['A', 'A-', 'B+', 'B', 'B-', 'C+', 'C', 'C-', 'D+', 'D', 'E'] as $g)
                                            <option value="{{ $g }}" {{ $subject->grade === $g ? 'selected' : '' }}>{{ $g }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="target-grade" style="padding: 12px; font-weight: bold; color: #4e73df;">-</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                {{-- Box Hasil Analisis --}}
                <div id="simulation-result" style="margin-top: 20px; padding: 15px; border-radius: 10px; background: #f0f7ff; display: none; border-left: 4px solid #4e73df;">
                    <p id="result-message" style="margin: 0; color: #2e59d9; font-size: 0.95rem;"></p>
                </div>
            @endif
        </div>
    </div>
</main>

@include('layouts.footer')