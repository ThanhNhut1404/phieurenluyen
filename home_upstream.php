<?php
    $id_sinh_vien = "";
    if(isset($_SESSION['sv'])){
        $id_sinh_vien = $_SESSION['sv']->id_nguoi_dung;
    } else if(isset($_SESSION['lt'])){
        $id_sinh_vien = $_SESSION['lt']->id_nguoi_dung;
    } else if(isset($_SESSION['bt'])){
        $id_sinh_vien = $_SESSION['bt']->id_nguoi_dung;
    }

    $sv = $sinhvien->sinhvien__Get_By_Id($id_sinh_vien);
    $lop = $lophoc->lophoc__Get_By_Id($sv->id_lop_hoc);
    $nganh = $nganhhoc->nganhhoc__Get_By_Id($lop->id_nganh_hoc);
    $khoa = $khoahoc->khoahoc__Get_By_Id($lop->id_khoa_hoc);

    // Fetch ket qua ren luyen data for the chart
    $list_ketqua = $ketquaxeploai->ketquaxeploai__Get_By_Id_Sinh_Vien_With_HocKy_NamHoc($id_sinh_vien);
    $grouped_ketqua = [];
    foreach ($list_ketqua as $kq) {
        $grouped_ketqua[$kq->ten_nam_hoc][] = $kq;
    }
?>
<div class="dashboard-container">
    
    <div class="row">
        <!-- Th├┤ng tin sinh vi├¬n -->
        <div class="col-md-8">
            <div class="custom-card">
                <h3 class="card-title-custom">Th├┤ng tin sinh vi├¬n</h3>
                <div class="student-info-wrapper">
                    <div class="student-avatar" style="overflow: hidden; display: flex; align-items: center; justify-content: center;">
                        <?php 
                        $avatar_home = "";
                        if (!empty($sv->anh_dai_dien) && file_exists("../../assets/img/avatars/" . $sv->anh_dai_dien)) {
                            $avatar_home = "../../assets/img/avatars/" . $sv->anh_dai_dien;
                        }
                        if ($avatar_home): 
                        ?>
                            <img src="<?php echo $avatar_home; ?>" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">
                        <?php else: ?>
                            <i class="ri-user-3-fill" style="font-size: 80px; color: rgba(255,255,255,0.9); margin-top: 15px;"></i>
                        <?php endif; ?>
                    </div>
                    <div class="student-details">
                        <div class="student-name-title"><?php echo $sv->ten_sinh_vien; ?></div>
                        
                        <p><span class="lbl">MSSV:</span> <span class="val"><?php echo $sv->ma_sinh_vien; ?></span></p>
                        <p><span class="lbl">Hß╗ì t├¬n:</span> <span class="val"><?php echo $sv->ten_sinh_vien; ?></span></p>
                        
                        <p><span class="lbl">Giß╗¢i t├¡nh:</span> <span class="val"><?php echo $sv->gioi_tinh == 1 ? "Nam" : "Nß╗»"; ?></span></p>
                        <p><span class="lbl">Ng├áy sinh:</span> <span class="val"><?php echo $sv->ngay_sinh; ?></span></p>
                        
                        <p><span class="lbl">─Éß╗ïa chß╗ë:</span> <span class="val"><?php echo $sv->dia_chi_thuong_tru; ?></span></p>
                        <p><span class="lbl">Trß║íng th├íi:</span> <span class="val">─Éang hß╗ìc</span></p>
                        
                        <p><span class="lbl">Sinh vi├¬n n─âm thß╗⌐:</span> <span class="val"></span></p>
                        <p><span class="lbl">Lß╗¢p hß╗ìc:</span> <span class="val"><?php echo $lop->ten_lop_hoc; ?></span></p>
                        
                        <p><span class="lbl">Kh├│a hß╗ìc:</span> <span class="val"><?php echo $khoa->ten_khoa_hoc; ?></span></p>
                        <p><span class="lbl">Bß║¡c ─æ├áo tß║ío:</span> <span class="val"></span></p>
                        
                        <p><span class="lbl">Loß║íi h├¼nh ─æ├áo tß║ío:</span> <span class="val"></span></p>
                        <p><span class="lbl">Ng├ánh:</span> <span class="val"><?php echo $nganh->ten_nganh_hoc; ?></span></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Th├┤ng b├ío -->
        <div class="col-md-4">
            <div class="custom-card" style="height: calc(100% - 10px);">
                <h3 class="card-title-custom d-flex justify-content-between align-items-center w-100">
                    <span>
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="width: 16px; height: 16px; color: #1d4ed8; stroke: currentColor; margin-right: 4px; vertical-align: -2px;">
                            <path d="M18 8a6 6 0 1 0-12 0c0 7-3 7-3 7h18s-3 0-3-7Z" stroke-width="2" stroke-linecap="round"/>
                            <path d="M10 21a2 2 0 0 0 4 0" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        Th├┤ng b├ío
                    </span>
                    <a href="#" style="font-size: 0.85rem; font-weight: normal; color: #1d4ed8; text-decoration: none;">Xem chi tiß║┐t <i class="ri-arrow-right-s-line"></i></a>
                </h3>
                <div class="notification-list">
                    <a href="#" class="notification-item">
                        <div class="notif-date">Th4<span>24</span></div>
                        <div class="notif-content">
                            <p class="notif-title">Th├┤ng b├ío cß║¡p nhß║¡t ─æiß╗âm r├¿n luyß╗çn hß╗ìc kß╗│ I</p>
                            <p class="notif-meta">Ph├▓ng CTSV &bull; 08:30</p>
                        </div>
                    </a>
                    <a href="#" class="notification-item">
                        <div class="notif-date">Th4<span>22</span></div>
                        <div class="notif-content">
                            <p class="notif-title">H╞░ß╗¢ng dß║½n ─æ─âng k├╜ hoß║ít ─æß╗Öng ngoß║íi kh├│a</p>
                            <p class="notif-meta">─Éo├án - Hß╗Öi &bull; 14:10</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions-row">
        <!-- LINK TO EVALUATION FORM -->
        <a href="?page=dot-cham-diem" class="action-btn">
            <i class="ri-survey-line"></i>
            <span>Phiß║┐u ─æ├ính gi├í</span>
        </a>
        <a href="index.php?page=ket-qua" class="action-btn">
            <i class="ri-bar-chart-2-line"></i>
            <span>Kß║┐t quß║ú r├¿n luyß╗çn</span>
        </a>
        <a href="#" class="action-btn disabled-btn" title="Chß╗⌐c n─âng sß║╜ ph├ít triß╗ân trong t╞░╞íng lai" onclick="return false;">
            <i class="ri-survey-line"></i>
            <span>─É─âng k├╜ hoß║ít ─æß╗Öng</span>
        </a>
        <a href="#" class="action-btn disabled-btn" title="Chß╗⌐c n─âng sß║╜ ph├ít triß╗ân trong t╞░╞íng lai" onclick="return false;">
            <i class="ri-calendar-event-line"></i>
            <span>Lß╗ïch hoß║ít ─æß╗Öng</span>
        </a>
        <a href="#" class="action-btn disabled-btn" title="Chß╗⌐c n─âng sß║╜ ph├ít triß╗ân trong t╞░╞íng lai" onclick="return false;">
            <i class="ri-add-box-line"></i>
            <span>Hoß║ít ─æß╗Öng ─æ├ú ─æ─âng k├╜</span>
        </a>
        <a href="#" class="action-btn disabled-btn" title="Chß╗⌐c n─âng sß║╜ ph├ít triß╗ân trong t╞░╞íng lai" onclick="return false;">
            <i class="ri-user-follow-line"></i>
            <span>─Éiß╗âm danh</span>
        </a>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <div class="col-md-4">
            <div class="custom-card disabled-card" title="Chß╗⌐c n─âng sß║╜ ph├ít triß╗ân trong t╞░╞íng lai" style="height: calc(100% - 10px);">
                <h3 class="card-title-custom">Hoß║ít ─æß╗Öng ─æ├ú ─æ─âng k├╜</h3>
                <div class="chart-placeholder" style="height: calc(100% - 45px);">
                    <i class="ri-bar-chart-box-line"></i>
                    <p>Ch╞░a c├│ dß╗» liß╗çu thß╗æng k├¬</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="custom-card" style="height: calc(100% - 10px);">
                <div class="card-title-custom d-flex justify-content-between align-items-center mb-3" style="border-bottom: 1px solid #e8ecf3;">
                    <span class="mb-0">Tiß║┐n ─æß╗Ö r├¿n luyß╗çn</span>
                    <?php if(!empty($grouped_ketqua)): ?>
                    <div class="d-flex align-items-center">
                        <span class="mr-1 text-muted" style="font-size: 12px; white-space: nowrap;">N─âm hß╗ìc:</span>
                        <select id="tdYearSelect" class="form-control form-control-sm mr-2" style="width: auto; max-width: 110px; font-size: 12px;" onchange="updateTdSemesterOptions()">
                            <?php foreach(array_keys($grouped_ketqua) as $nam_hoc): ?>
                                <option value="<?= htmlspecialchars($nam_hoc) ?>"><?= htmlspecialchars($nam_hoc) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <span class="mr-1 text-muted" style="font-size: 12px; white-space: nowrap;">Hß╗ìc kß╗│:</span>
                        <select id="tdHkSelect" class="form-control form-control-sm" style="width: auto; max-width: 85px; font-size: 12px;" onchange="updateDoughnutChartStandalone()">
                        </select>
                    </div>
                    <?php endif; ?>
                </div>
                
                <?php if(empty($grouped_ketqua)): ?>
                <div class="chart-placeholder" style="height: calc(100% - 45px);">
                    <i class="ri-pie-chart-line"></i>
                    <p>Ch╞░a c├│ dß╗» liß╗çu thß╗æng k├¬</p>
                </div>
                <?php else: ?>
                <div style="position: relative; height: 170px; width: 100%; display: flex; justify-content: center; align-items: center; margin-top: 10px;">
                    <canvas id="chartTienDo"></canvas>
                    <div id="chartTienDoCenterText" style="position: absolute; text-align: center; font-weight: bold; font-size: 28px; color: #1d4ed8;"></div>
                </div>
                <div id="chartTienDoLabel" class="text-center mt-3 font-weight-bold text-muted" style="font-size: 14px;"></div>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="custom-card" style="height: calc(100% - 10px);">
                <div class="card-title-custom d-flex justify-content-between align-items-center mb-3" style="border-bottom: 1px solid #e8ecf3;">
                    <span class="mb-0">Kß║┐t quß║ú r├¿n luyß╗çn</span>
                    <?php if(!empty($grouped_ketqua)): ?>
                    <div class="d-flex align-items-center">
                        <span class="mr-2 text-muted" style="font-size: 13px; white-space: nowrap;">N─âm hß╗ìc:</span>
                        <select id="yearSelect" class="form-control form-control-sm" style="width: auto; max-width: 130px; font-size: 13px;" onchange="updateChart()">
                            <?php foreach(array_keys($grouped_ketqua) as $nam_hoc): ?>
                                <option value="<?= htmlspecialchars($nam_hoc) ?>"><?= htmlspecialchars($nam_hoc) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>
                </div>
                
                <?php if(empty($grouped_ketqua)): ?>
                <div class="chart-placeholder" style="height: calc(100% - 45px);">
                    <i class="ri-bar-chart-grouped-line"></i>
                    <p>Ch╞░a c├│ dß╗» liß╗çu thß╗æng k├¬</p>
                </div>
                <?php else: ?>
                <div style="position: relative; height: 200px; width: 100%;">
                    <canvas id="chartKetQua"></canvas>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if(!empty($grouped_ketqua)): ?>
<script>
    const chartKetQuaData = <?= json_encode($grouped_ketqua) ?>;
    let myBarChart = null;

    function updateChart() {
        const year = document.getElementById('yearSelect').value;
        const yearData = chartKetQuaData[year] || [];
        
        const labels = ['Hß╗ìc kß╗│ 1', 'Hß╗ìc kß╗│ 2', 'Hß╗ìc kß╗│ 3'];
        const scores = [null, null, null];
        const bgColors = [null, null, null];
        const classifications = [null, null, null];
        
        yearData.forEach(d => {
            let idx = labels.indexOf(d.ten_hoc_ky);
            if(idx === -1) {
                 if(String(d.ten_hoc_ky).includes('1')) idx = 0;
                 else if(String(d.ten_hoc_ky).includes('2')) idx = 1;
                 else if(String(d.ten_hoc_ky).includes('3') || String(d.ten_hoc_ky).toLowerCase().includes('h├¿') || String(d.ten_hoc_ky).toLowerCase().includes('phß╗Ñ')) idx = 2;
            }
            
            if (idx !== -1) {
                const score = parseFloat(d.ket_qua);
                scores[idx] = score;
                
                const xepLoai = d.xep_loai ? d.xep_loai.trim() : 'Ch╞░a xß║┐p loß║íi';
                classifications[idx] = xepLoai;
                const xlLower = xepLoai.toLowerCase();
                
                if(xlLower.includes('xuß║Ñt sß║»c')) bgColors[idx] = 'rgba(40, 167, 69, 0.7)';
                else if(xlLower.includes('tß╗æt')) bgColors[idx] = 'rgba(0, 123, 255, 0.7)';
                else if(xlLower.includes('kh├í')) bgColors[idx] = 'rgba(23, 162, 184, 0.7)';
                else if(xlLower.includes('trung b├¼nh')) bgColors[idx] = 'rgba(255, 193, 7, 0.7)';
                else bgColors[idx] = 'rgba(220, 53, 69, 0.7)';
            }
        });

        if(myBarChart) {
            myBarChart.data.labels = labels;
            myBarChart.data.datasets[0].data = scores;
            myBarChart.data.datasets[0].backgroundColor = bgColors;
            myBarChart.data.datasets[0].classifications = classifications;
            myBarChart.update();
        } else {
            const ctx = document.getElementById('chartKetQua');
            if(!ctx) return;
            
            if(typeof Chart === 'undefined') {
                setTimeout(updateChart, 100);
                return;
            }
            
            myBarChart = new Chart(ctx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: '─Éiß╗âm r├¿n luyß╗çn',
                        data: scores,
                        backgroundColor: bgColors,
                        borderWidth: 0,
                        borderRadius: 4,
                        classifications: classifications
                    }]
                },
                options: {
                    layout: {
                        padding: {
                            top: 20
                        }
                    },
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                stepSize: 20
                            },
                            title: {
                                display: true,
                                text: '─Éiß╗âm sß╗æ',
                                font: { weight: 'bold' }
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Hß╗ìc kß╗│',
                                font: { weight: 'bold' }
                            }
                        }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const score = context.raw;
                                    const classification = context.dataset.classifications[context.dataIndex];
                                    let lines = ['─Éiß╗âm r├¿n luyß╗çn: ' + score];
                                    if(classification) lines.push('Xß║┐p loß║íi: ' + classification);
                                    return lines;
                                }
                            }
                        }
                    }
                },
                plugins: [{
                    id: 'barLabels',
                    afterDatasetsDraw(chart) {
                        const { ctx } = chart;
                        chart.data.datasets.forEach((dataset, i) => {
                            const meta = chart.getDatasetMeta(i);
                            meta.data.forEach((bar, index) => {
                                const data = dataset.data[index];
                                if (data !== null && data !== undefined) {
                                    ctx.save();
                                    ctx.font = 'bold 13px Arial';
                                    ctx.fillStyle = '#333';
                                    ctx.textAlign = 'center';
                                    ctx.textBaseline = 'bottom';
                                    ctx.fillText(data, bar.x, bar.y - 5);
                                    ctx.restore();
                                }
                            });
                        });
                    }
                }]
            });
        }
    }

    let myDoughnutChart = null;
    function updateTdSemesterOptions() {
        const yearSelect = document.getElementById('tdYearSelect');
        const hkSelect = document.getElementById('tdHkSelect');
        if(!yearSelect || !hkSelect) return;
        
        const year = yearSelect.value;
        const yearData = chartKetQuaData[year] || [];
        
        hkSelect.innerHTML = '';
        yearData.forEach(d => {
            const opt = document.createElement('option');
            opt.value = d.ten_hoc_ky;
            opt.text = d.ten_hoc_ky;
            hkSelect.appendChild(opt);
        });
        
        updateDoughnutChartStandalone();
    }
    
    function updateDoughnutChartStandalone() {
        const yearSelect = document.getElementById('tdYearSelect');
        const hkSelect = document.getElementById('tdHkSelect');
        if(!yearSelect || !hkSelect) return;
        
        const year = yearSelect.value;
        const hk = hkSelect.value;
        const yearData = chartKetQuaData[year] || [];
        
        const d = yearData.find(item => item.ten_hoc_ky == hk);
        if(!d) return;
        
        const score = parseFloat(d.ket_qua);
        const remainScore = 100 - score > 0 ? 100 - score : 0;
        
        const xepLoai = d.xep_loai ? d.xep_loai.trim() : 'Ch╞░a xß║┐p loß║íi';
        const xlLower = xepLoai.toLowerCase();
        
        let color = '#28a745';
        if(xlLower.includes('xuß║Ñt sß║»c')) color = 'rgba(40, 167, 69, 0.9)';
        else if(xlLower.includes('tß╗æt')) color = 'rgba(0, 123, 255, 0.9)';
        else if(xlLower.includes('kh├í')) color = 'rgba(23, 162, 184, 0.9)';
        else if(xlLower.includes('trung b├¼nh')) color = 'rgba(255, 193, 7, 0.9)';
        else color = 'rgba(220, 53, 69, 0.9)'; // Yß║┐u/K├⌐m

        const textEl = document.getElementById('chartTienDoCenterText');
        const labelEl = document.getElementById('chartTienDoLabel');
        if(textEl) textEl.innerText = score + '/100';
        if(textEl) textEl.style.color = color;
        if(labelEl) labelEl.innerHTML = 'Tiß║┐n ─æß╗Ö: ' + hk + ' (' + year + ') <br><span style="color:' + color + '; font-size: 15px;">Xß║┐p loß║íi: ' + xepLoai + '</span>';

        if(myDoughnutChart) {
            myDoughnutChart.data.datasets[0].data = [score, remainScore];
            myDoughnutChart.data.datasets[0].backgroundColor = [color, '#e8ecf3'];
            myDoughnutChart.update();
        } else {
            const ctxDo = document.getElementById('chartTienDo');
            if(!ctxDo) return;
            
            if(typeof Chart === 'undefined') {
                setTimeout(updateDoughnutChartStandalone, 100);
                return;
            }
            
            myDoughnutChart = new Chart(ctxDo, {
                type: 'doughnut',
                data: {
                    labels: ['─Éß║ít ─æ╞░ß╗úc', 'C├▓n lß║íi'],
                    datasets: [{
                        data: [score, remainScore],
                        backgroundColor: [color, '#e8ecf3'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '75%',
                    plugins: {
                        legend: { display: false },
                        tooltip: { enabled: true }
                    }
                }
            });
        }
    }

    // Attempt to load chart on DOMContentLoaded
    document.addEventListener('DOMContentLoaded', function() {
        // slight delay to ensure Chart.js is fully parsed
        setTimeout(function() {
            updateChart();
            updateTdSemesterOptions();
        }, 200);
    });
</script>
<?php endif; ?>
