@extends('layouts.component.main')
@section('title', 'Undian Doorprize')
@section('content')
    <style>
        .doorprize-stage {
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #312e81 100%);
            border-radius: 20px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 20px 45px rgba(15, 23, 42, 0.28);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.4s ease;
        }

        .doorprize-stage::before {
            content: '';
            position: absolute;
            top: -40%;
            left: -20%;
            width: 70%;
            height: 140%;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.22) 0%, transparent 65%);
            pointer-events: none;
        }

        .doorprize-stage::after {
            content: '';
            position: absolute;
            bottom: -40%;
            right: -15%;
            width: 65%;
            height: 130%;
            background: radial-gradient(circle, rgba(245, 158, 11, 0.16) 0%, transparent 65%);
            pointer-events: none;
        }

        .doorprize-stage.winner-glow {
            box-shadow: 0 0 0 3px rgba(251, 191, 36, 0.65), 0 25px 55px rgba(245, 158, 11, 0.25);
        }

        .slot-window {
            background: rgba(15, 23, 42, 0.72);
            backdrop-filter: blur(12px);
            border: 2px solid rgba(255, 255, 255, 0.14);
            border-radius: 16px;
            min-height: 210px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.5rem;
            position: relative;
            z-index: 2;
            transition: border-color 0.3s ease, transform 0.3s ease;
        }

        .slot-window.spinning {
            border-color: rgba(129, 140, 248, 0.75);
        }

        .slot-window.landed {
            border-color: #fbbf24;
            background: rgba(30, 27, 75, 0.85);
            animation: winnerPop 0.55s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        @keyframes winnerPop {
            0% { transform: scale(0.95); }
            50% { transform: scale(1.03); }
            100% { transform: scale(1); }
        }

        .slot-name {
            font-size: clamp(1.75rem, 3.2vw, 2.75rem);
            font-weight: 800;
            letter-spacing: -0.02em;
            color: #ffffff;
            text-align: center;
            line-height: 1.2;
            text-shadow: 0 4px 18px rgba(0, 0, 0, 0.35);
        }

        .slot-window.landed .slot-name {
            background: linear-gradient(90deg, #fde68a, #fbbf24, #fef3c7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: none;
        }

        .btn-draw-primary {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            border: none;
            color: #fff;
            font-weight: 700;
            letter-spacing: 0.04em;
            padding: 0.9rem 2.4rem;
            border-radius: 50px;
            box-shadow: 0 10px 25px rgba(245, 158, 11, 0.38);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .btn-draw-primary:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 14px 30px rgba(245, 158, 11, 0.5);
            color: #fff;
        }

        .btn-draw-primary:disabled {
            opacity: 0.65;
            cursor: not-allowed;
        }

        .hadiah-input {
            background: rgba(255, 255, 255, 0.1) !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            color: #fff !important;
            border-radius: 12px;
        }

        .hadiah-input::placeholder {
            color: rgba(255, 255, 255, 0.55);
        }

        .hadiah-input:focus {
            background: rgba(255, 255, 255, 0.16) !important;
            border-color: #fbbf24 !important;
            box-shadow: 0 0 0 3px rgba(251, 191, 36, 0.2) !important;
        }

        .stat-card-clean {
            border-radius: 14px;
            border: 1px solid rgba(15, 23, 42, 0.06);
            transition: transform 0.2s ease;
        }

        .stat-card-clean:hover {
            transform: translateY(-2px);
        }

        .winner-item {
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            transition: background-color 0.2s ease;
        }

        .winner-item:hover {
            background-color: #f8fafc;
        }

        .winner-rank-badge {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.85rem;
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            color: #b45309;
            flex-shrink: 0;
        }

        #confettiCanvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            pointer-events: none;
            z-index: 9999;
        }

        /* Fullscreen mode styles */
        .stage-fullscreen-active {
            position: fixed !important;
            inset: 0 !important;
            z-index: 1055 !important;
            border-radius: 0 !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: center !important;
            padding: 3rem !important;
        }

        .stage-fullscreen-active .slot-window {
            min-height: 320px;
        }
    </style>

    <canvas id="confettiCanvas"></canvas>

    <div class="page-title-head d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h4 class="fs-xl fw-bold m-0">Undian Doorprize</h4>
            <p class="text-muted mb-0 small">Hanya peserta dengan status <strong>Layak Doorprize</strong> yang diikutsertakan dalam undian.</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1" id="btnSoundToggle">
                <i class="ti ti-volume fs-16"></i> <span>Suara: Aktif</span>
            </button>
            <button type="button" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1" id="btnFullscreen">
                <i class="ti ti-maximize fs-16"></i> <span>Mode Layar Penuh</span>
            </button>
        </div>
    </div>

    <div class="container-fluid px-0">
        {{-- STAT CARDS --}}
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card stat-card-clean shadow-sm h-100 mb-0">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-semibold text-uppercase">Total Layak Doorprize</span>
                            <h3 class="fw-bold mb-0 mt-1 text-primary" id="statTotalEligible">0</h3>
                        </div>
                        <div class="avatar-md rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                            <i class="ti ti-users fs-24"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card-clean shadow-sm h-100 mb-0">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-semibold text-uppercase">Sisa Peserta Belum Menang</span>
                            <h3 class="fw-bold mb-0 mt-1 text-warning" id="statRemaining">0</h3>
                        </div>
                        <div class="avatar-md rounded-circle bg-warning-subtle text-warning d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                            <i class="ti ti-ticket fs-24"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card-clean shadow-sm h-100 mb-0">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-semibold text-uppercase">Total Pemenang</span>
                            <h3 class="fw-bold mb-0 mt-1 text-success" id="statWinners">0</h3>
                        </div>
                        <div class="avatar-md rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                            <i class="ti ti-trophy fs-24"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            {{-- LEFT: PANGGUNG UNDIAN --}}
            <div class="col-xl-7">
                <div class="doorprize-stage p-4 p-md-5 text-white" id="doorprizeStage">
                    <div class="d-flex justify-content-between align-items-center mb-4 position-relative" style="z-index: 2;">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold">
                                <i class="ti ti-sparkles me-1"></i> LUCKY DRAW
                            </span>
                            <span class="text-white-50 small d-none d-sm-inline" id="poolStatusBadge">Memuat peserta...</span>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-light d-none" id="btnExitFullscreen">
                            <i class="ti ti-minimize me-1"></i> Keluar Layar Penuh
                        </button>
                    </div>

                    {{-- Input Hadiah Opsional --}}
                    <div class="mb-4 position-relative" style="z-index: 2; max-width: 420px; margin-left: auto; margin-right: auto;">
                        <label class="form-label text-white-50 small text-center d-block mb-1">Nama Hadiah (Opsional)</label>
                        <input type="text" id="inputHadiah" class="form-control text-center hadiah-input"
                            placeholder="Contoh: TV LED 32 Inch / Voucher Belanja...">
                    </div>

                    {{-- Slot Window --}}
                    <div class="slot-window mb-4" id="slotWindow">
                        <div class="text-white-50 text-uppercase small fw-semibold mb-2 tracking-wider" id="slotLabel">
                            MENUNGGU PUTARAN
                        </div>
                        <div class="slot-name" id="slotName">Siap Mengundi</div>
                        <div class="d-flex flex-wrap justify-content-center gap-2 mt-3" id="slotMeta">
                            <span class="badge bg-white bg-opacity-10 text-white px-3 py-2 rounded-pill fs-6" id="slotNik">NIK: -</span>
                            <span class="badge bg-white bg-opacity-10 text-white px-3 py-2 rounded-pill fs-6" id="slotDept">Dept: -</span>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="text-center position-relative" style="z-index: 2;">
                        <button type="button" class="btn btn-draw-primary btn-lg" id="btnDraw">
                            <i class="ti ti-player-play me-2"></i> PUTAR UNDIAN
                        </button>

                        {{-- Tombol Konfirmasi Setelah Pemenang Terpilih --}}
                        <div id="winnerActions" class="d-none mt-4 d-flex flex-wrap justify-content-center gap-2">
                            <button type="button" class="btn btn-success btn-lg rounded-pill px-4 fw-bold shadow" id="btnConfirmWinner">
                                <i class="ti ti-check me-1"></i> Sah! Simpan Pemenang
                            </button>
                            <button type="button" class="btn btn-outline-light btn-lg rounded-pill px-4" id="btnSkipWinner">
                                <i class="ti ti-refresh me-1"></i> Tidak Hadir / Undi Ulang
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Daftar Peserta Layak (Preview Cepat) --}}
                <div class="card shadow-sm mt-4">
                    <div class="card-header bg-light d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <h6 class="mb-0 fw-bold">
                            <i class="ti ti-list-check text-primary me-1"></i> Peserta Layak Belum Menang
                            <span class="badge bg-primary ms-1" id="eligibleListCount">0</span>
                        </h6>
                        <input type="text" id="searchEligible" class="form-control form-control-sm" style="width: 220px;"
                            placeholder="Cari nama / NIK...">
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 270px; overflow-y: auto;">
                            <table class="table table-sm table-hover align-middle mb-0">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th class="ps-3" style="width: 50px;">No</th>
                                        <th>NIK</th>
                                        <th>Nama Pegawai</th>
                                        <th>Departemen</th>
                                    </tr>
                                </thead>
                                <tbody id="eligibleTableBody">
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">Memuat data peserta...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- RIGHT: DAFTAR PEMENANG --}}
            <div class="col-xl-5">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-light d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <h6 class="mb-0 fw-bold">
                            <i class="ti ti-trophy text-warning me-1"></i> Daftar Pemenang
                        </h6>
                        <div class="d-flex gap-2">
                            <a href="{{ route('doorprize.export') }}" class="btn btn-success btn-sm">
                                <i class="ti ti-file-spreadsheet me-1"></i> Export
                            </a>
                            <button type="button" class="btn btn-outline-danger btn-sm" id="btnResetWinners">
                                <i class="ti ti-trash me-1"></i> Reset
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-3" style="max-height: 620px; overflow-y: auto;" id="winnersListContainer">
                        <div class="text-center text-muted py-5" id="emptyWinnersState">
                            <i class="ti ti-gift fs-1 d-block mb-2 opacity-50"></i>
                            <p class="mb-0">Belum ada pemenang yang disimpan.</p>
                            <small>Putar undian lalu klik <strong>Sah! Simpan Pemenang</strong>.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            let eligiblePool = [];
            let skippedNiks = [];
            let isSpinning = false;
            let soundEnabled = true;
            let currentCandidateWinner = null;
            let audioCtx = null;

            // --- SOUND EFFECTS (Web Audio API, Offline Ready) ---
            function getAudioContext() {
                if (!audioCtx) {
                    const AudioContext = window.AudioContext || window.webkitAudioContext;
                    if (AudioContext) audioCtx = new AudioContext();
                }
                if (audioCtx && audioCtx.state === 'suspended') {
                    audioCtx.resume();
                }
                return audioCtx;
            }

            function playTickSound() {
                if (!soundEnabled) return;
                const ctx = getAudioContext();
                if (!ctx) return;

                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(520, ctx.currentTime);
                osc.frequency.exponentialRampToValueAtTime(220, ctx.currentTime + 0.04);

                gain.gain.setValueAtTime(0.08, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.04);

                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start();
                osc.stop(ctx.currentTime + 0.045);
            }

            function playWinSound() {
                if (!soundEnabled) return;
                const ctx = getAudioContext();
                if (!ctx) return;

                const notes = [523.25, 659.25, 783.99, 1046.50]; // C5, E5, G5, C6
                notes.forEach((freq, idx) => {
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.type = 'triangle';
                    const startTime = ctx.currentTime + idx * 0.09;
                    osc.frequency.setValueAtTime(freq, startTime);

                    gain.gain.setValueAtTime(0.14, startTime);
                    gain.gain.exponentialRampToValueAtTime(0.001, startTime + 0.45);

                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.start(startTime);
                    osc.stop(startTime + 0.46);
                });
            }

            // --- CONFETTI (Pure Canvas, Offline Ready) ---
            function launchConfetti() {
                const canvas = document.getElementById('confettiCanvas');
                const ctx = canvas.getContext('2d');
                canvas.width = window.innerWidth;
                canvas.height = window.innerHeight;

                const colors = ['#fbbf24', '#34d399', '#60a5fa', '#f472b6', '#a78bfa', '#ffffff'];
                const pieces = Array.from({ length: 130 }, () => ({
                    x: Math.random() * canvas.width,
                    y: Math.random() * -canvas.height * 0.5,
                    w: Math.random() * 10 + 6,
                    h: Math.random() * 6 + 4,
                    color: colors[Math.floor(Math.random() * colors.length)],
                    vy: Math.random() * 4 + 3,
                    vx: (Math.random() - 0.5) * 4,
                    rot: Math.random() * Math.PI * 2,
                    vRot: (Math.random() - 0.5) * 0.2
                }));

                let frame = 0;
                function animate() {
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    let active = false;

                    pieces.forEach(p => {
                        p.y += p.vy;
                        p.x += p.vx;
                        p.rot += p.vRot;
                        if (p.y < canvas.height + 20) active = true;

                        ctx.save();
                        ctx.translate(p.x, p.y);
                        ctx.rotate(p.rot);
                        ctx.fillStyle = p.color;
                        ctx.fillRect(-p.w / 2, -p.h / 2, p.w, p.h);
                        ctx.restore();
                    });

                    frame++;
                    if (active && frame < 220) {
                        requestAnimationFrame(animate);
                    } else {
                        ctx.clearRect(0, 0, canvas.width, canvas.height);
                    }
                }
                requestAnimationFrame(animate);
            }

            // --- LOAD DATA ---
            function loadDoorprizeData() {
                $.ajax({
                    url: "{{ route('doorprize.data') }}",
                    type: "GET",
                    data: { skipped_niks: skippedNiks },
                    success: function(res) {
                        if (res.status === 'success') {
                            eligiblePool = res.eligible;
                            $('#statTotalEligible').text(res.stats.total_eligible);
                            $('#statRemaining').text(res.stats.remaining);
                            $('#statWinners').text(res.stats.total_winners);
                            $('#eligibleListCount').text(res.stats.remaining);
                            $('#poolStatusBadge').text(res.stats.remaining + ' peserta siap diundi');

                            renderEligibleTable(eligiblePool);
                            renderWinnersList(res.winners);
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Gagal memuat data doorprize.', 'error');
                    }
                });
            }

            function renderEligibleTable(list) {
                const keyword = ($('#searchEligible').val() || '').toLowerCase().trim();
                const filtered = keyword
                    ? list.filter(item =>
                        (item.nama_pegawai || '').toLowerCase().includes(keyword) ||
                        (item.nik || '').toLowerCase().includes(keyword) ||
                        (item.departemen || '').toLowerCase().includes(keyword)
                    )
                    : list;

                const tbody = $('#eligibleTableBody');
                if (filtered.length === 0) {
                    tbody.html('<tr><td colspan="4" class="text-center py-4 text-muted">Tidak ada peserta yang tersisa.</td></tr>');
                    return;
                }

                let html = '';
                filtered.forEach((p, idx) => {
                    html += `
                        <tr>
                            <td class="ps-3 text-muted">${idx + 1}</td>
                            <td><code>${escapeHtml(p.nik)}</code></td>
                            <td class="fw-semibold">${escapeHtml(p.nama_pegawai)}</td>
                            <td><span class="badge bg-light text-dark border">${escapeHtml(p.departemen || '-')}</span></td>
                        </tr>
                    `;
                });
                tbody.html(html);
            }

            function renderWinnersList(winners) {
                const container = $('#winnersListContainer');
                if (!winners || winners.length === 0) {
                    container.html(`
                        <div class="text-center text-muted py-5">
                            <i class="ti ti-gift fs-1 d-block mb-2 opacity-50"></i>
                            <p class="mb-0">Belum ada pemenang yang disimpan.</p>
                            <small>Putar undian lalu klik <strong>Sah! Simpan Pemenang</strong>.</small>
                        </div>
                    `);
                    return;
                }

                let html = '<div class="d-flex flex-column gap-2">';
                winners.forEach((w, idx) => {
                    const nomor = winners.length - idx;
                    const hadiahBadge = w.hadiah && w.hadiah !== '-'
                        ? `<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle mt-1"><i class="ti ti-gift me-1"></i>${escapeHtml(w.hadiah)}</span>`
                        : '';
                    html += `
                        <div class="winner-item p-2 px-3 d-flex align-items-center justify-content-between gap-2">
                            <div class="d-flex align-items-center gap-3 overflow-hidden">
                                <span class="winner-rank-badge">#${nomor}</span>
                                <div class="overflow-hidden">
                                    <div class="fw-bold text-dark text-truncate">${escapeHtml(w.nama_pegawai)}</div>
                                    <div class="small text-muted">
                                        <code>${escapeHtml(w.nik)}</code> • ${escapeHtml(w.departemen)}
                                    </div>
                                    ${hadiahBadge}
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-danger border-0 btn-delete-winner" data-id="${w.id}" title="Hapus pemenang">
                                <i class="ti ti-x"></i>
                            </button>
                        </div>
                    `;
                });
                html += '</div>';
                container.html(html);
            }

            function escapeHtml(text) {
                if (text === null || text === undefined) return '';
                return String(text)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            // --- SPIN / LUCKY DRAW ---
            $('#btnDraw').click(function() {
                if (isSpinning) return;
                if (eligiblePool.length === 0) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Peserta Habis',
                        text: 'Tidak ada lagi peserta layak doorprize yang tersisa untuk diundi.'
                    });
                    return;
                }

                isSpinning = true;
                currentCandidateWinner = null;
                $('#btnDraw').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> MENGUNDI...');
                $('#winnerActions').addClass('d-none');
                $('#doorprizeStage').removeClass('winner-glow');
                $('#slotWindow').removeClass('landed').addClass('spinning');
                $('#slotLabel').text('SEDANG MENGACAK PESERTA...');

                // Minta server memilih 1 pemenang secara acak dan adil
                $.ajax({
                    url: "{{ route('doorprize.draw') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        skipped_niks: skippedNiks
                    },
                    success: function(res) {
                        if (res.status === 'success') {
                            runSlotAnimation(res.winner);
                        } else {
                            resetSpinUI();
                            Swal.fire('Info', res.message || 'Gagal mengundi.', 'info');
                        }
                    },
                    error: function(xhr) {
                        resetSpinUI();
                        const msg = xhr.responseJSON?.message || 'Terjadi kesalahan saat mengundi.';
                        Swal.fire('Error', msg, 'error');
                    }
                });
            });

            function runSlotAnimation(winner) {
                const totalDuration = 3400; // 3.4 detik animasi dramatis
                const startTime = performance.now();
                let lastTickTime = 0;

                function step(now) {
                    const elapsed = now - startTime;
                    const progress = Math.min(elapsed / totalDuration, 1);

                    // Easing melambat secara natural (interval makin lambat mendekati akhir)
                    const currentInterval = 45 + Math.pow(progress, 3) * 260;

                    if (progress < 1) {
                        if (now - lastTickTime >= currentInterval) {
                            const randomItem = eligiblePool[Math.floor(Math.random() * eligiblePool.length)];
                            if (randomItem) {
                                $('#slotName').text(randomItem.nama_pegawai);
                                $('#slotNik').text('NIK: ' + randomItem.nik);
                                $('#slotDept').text('Dept: ' + (randomItem.departemen || '-'));
                                playTickSound();
                            }
                            lastTickTime = now;
                        }
                        requestAnimationFrame(step);
                    } else {
                        // Berhenti tepat di pemenang terpilih
                        currentCandidateWinner = winner;
                        $('#slotName').text(winner.nama_pegawai);
                        $('#slotNik').text('NIK: ' + winner.nik);
                        $('#slotDept').text('Dept: ' + (winner.departemen || '-'));
                        $('#slotLabel').html('<i class="ti ti-confetti me-1"></i> SELAMAT KEPADA PEMENANG!');
                        $('#slotWindow').removeClass('spinning').addClass('landed');
                        $('#doorprizeStage').addClass('winner-glow');

                        playWinSound();
                        launchConfetti();

                        isSpinning = false;
                        $('#btnDraw').prop('disabled', false).html('<i class="ti ti-player-play me-2"></i> PUTAR UNDIAN LAGI');
                        $('#winnerActions').removeClass('d-none');
                    }
                }

                requestAnimationFrame(step);
            }

            function resetSpinUI() {
                isSpinning = false;
                $('#btnDraw').prop('disabled', false).html('<i class="ti ti-player-play me-2"></i> PUTAR UNDIAN');
                $('#slotWindow').removeClass('spinning landed');
                $('#slotLabel').text('MENUNGGU PUTARAN');
            }

            // --- KONFIRMASI SIMPAN PEMENANG ---
            $('#btnConfirmWinner').click(function() {
                if (!currentCandidateWinner) return;

                const btn = $(this);
                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');

                $.ajax({
                    url: "{{ route('doorprize.storeWinner') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        attendance_id: currentCandidateWinner.attendance_id,
                        nik: currentCandidateWinner.nik,
                        nama_pegawai: currentCandidateWinner.nama_pegawai,
                        departemen: currentCandidateWinner.departemen,
                        hadiah: $('#inputHadiah').val()
                    },
                    success: function(res) {
                        btn.prop('disabled', false).html('<i class="ti ti-check me-1"></i> Sah! Simpan Pemenang');
                        if (res.status === 'success') {
                            $('#winnerActions').addClass('d-none');
                            currentCandidateWinner = null;
                            loadDoorprizeData();

                            Swal.fire({
                                icon: 'success',
                                title: 'Tersimpan!',
                                text: 'Pemenang berhasil masuk ke daftar pemenang.',
                                timer: 1600,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function(xhr) {
                        btn.prop('disabled', false).html('<i class="ti ti-check me-1"></i> Sah! Simpan Pemenang');
                        Swal.fire('Error', xhr.responseJSON?.message || 'Gagal menyimpan pemenang.', 'error');
                    }
                });
            });

            // --- SKIP / TIDAK HADIR (UNDI ULANG) ---
            $('#btnSkipWinner').click(function() {
                if (!currentCandidateWinner) return;
                skippedNiks.push(currentCandidateWinner.nik);
                currentCandidateWinner = null;
                $('#winnerActions').addClass('d-none');
                $('#doorprizeStage').removeClass('winner-glow');
                $('#slotWindow').removeClass('landed');
                $('#slotLabel').text('PESERTA DILEWATI - SIAP UNDI ULANG');
                $('#slotName').text('Siap Mengundi');
                $('#slotNik').text('NIK: -');
                $('#slotDept').text('Dept: -');
                loadDoorprizeData();
            });

            // --- HAPUS SATU PEMENANG ---
            $(document).on('click', '.btn-delete-winner', function() {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Hapus pemenang ini?',
                    text: 'Peserta akan dikembalikan ke daftar undian yang belum menang.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('doorprize/winners') }}/" + id,
                            type: "DELETE",
                            data: { _token: "{{ csrf_token() }}" },
                            success: function(res) {
                                loadDoorprizeData();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Dihapus',
                                    text: res.message,
                                    timer: 1400,
                                    showConfirmButton: false
                                });
                            },
                            error: function() {
                                Swal.fire('Error', 'Gagal menghapus pemenang.', 'error');
                            }
                        });
                    }
                });
            });

            // --- RESET SEMUA PEMENANG ---
            $('#btnResetWinners').click(function() {
                Swal.fire({
                    title: 'Reset Semua Pemenang?',
                    text: 'Seluruh daftar pemenang doorprize akan dihapus permanen!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Ya, Reset Semua',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('doorprize.reset') }}",
                            type: "POST",
                            data: { _token: "{{ csrf_token() }}" },
                            success: function(res) {
                                skippedNiks = [];
                                loadDoorprizeData();
                                Swal.fire('Berhasil', res.message, 'success');
                            },
                            error: function() {
                                Swal.fire('Error', 'Gagal mereset data pemenang.', 'error');
                            }
                        });
                    }
                });
            });

            // --- SEARCH ELIGIBLE ---
            $('#searchEligible').on('input', function() {
                renderEligibleTable(eligiblePool);
            });

            // --- SOUND TOGGLE ---
            $('#btnSoundToggle').click(function() {
                soundEnabled = !soundEnabled;
                if (soundEnabled) {
                    $(this).html('<i class="ti ti-volume fs-16"></i> <span>Suara: Aktif</span>');
                } else {
                    $(this).html('<i class="ti ti-volume-off fs-16"></i> <span>Suara: Mati</span>');
                }
            });

            // --- FULLSCREEN STAGE MODE ---
            $('#btnFullscreen').click(function() {
                $('#doorprizeStage').addClass('stage-fullscreen-active');
                $('#btnExitFullscreen').removeClass('d-none');
            });

            $('#btnExitFullscreen').click(function() {
                $('#doorprizeStage').removeClass('stage-fullscreen-active');
                $(this).addClass('d-none');
            });

            // Initial load
            loadDoorprizeData();
        });
    </script>
@endsection
