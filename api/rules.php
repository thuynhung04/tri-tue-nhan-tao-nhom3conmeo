<?php
/**
 * KNOWLEDGE BASE - Tập luật IF-THEN
 * Hệ Tư Vấn Lộ Trình Học Tập Cho Sinh Viên
 * Bao gồm 41 luật IF-THEN đầy đủ cho định hướng, cảnh báo học vụ và tư vấn môn học tiên quyết
 */

function getRules(): array {
    return [
        // ── Nhóm 1: Cảnh báo học vụ & năng lực cơ sở (R01 - R08) ─────────────
        [
            'id'          => 'R01',
            'name'        => 'Chưa đạt Lập trình cơ bản',
            'priority'    => 5,
            'conditions'  => [
                ['fact' => 'chua_dat_lt_co_ban', 'op' => '==', 'value' => true]
            ],
            'conclusion'  => 'khong_nen_hoc_lt_nang_cao',
            'conclusion_value' => true,
            'recommendation' => 'Chưa nên học Lập trình nâng cao – cần hoàn thành Lập trình cơ bản trước.',
            'category'    => 'warning'
        ],
        [
            'id'          => 'R02',
            'name'        => 'Điểm TB dưới 5.0',
            'priority'    => 5,
            'conditions'  => [
                ['fact' => 'diem_tb', 'op' => '<', 'value' => 5.0]
            ],
            'conclusion'  => 'nen_giam_tin_chi',
            'conclusion_value' => true,
            'recommendation' => 'Nên giảm số tín chỉ đăng ký trong học kỳ tới để tập trung nâng điểm.',
            'category'    => 'warning'
        ],
        [
            'id'          => 'R03',
            'name'        => 'Điểm TB tốt và lập trình giỏi',
            'priority'    => 3,
            'conditions'  => [
                ['fact' => 'diem_tb',         'op' => '>=', 'value' => 7.0],
                ['fact' => 'ky_nang_lap_trinh','op' => '==', 'value' => 'tot']
            ],
            'conclusion'  => 'co_the_dang_ky_nang_cao',
            'conclusion_value' => true,
            'recommendation' => 'Có thể đăng ký các môn học nâng cao đã đủ điều kiện tiên quyết – năng lực học tập đáp ứng tốt.',
            'category'    => 'success'
        ],
        [
            'id'          => 'R04',
            'name'        => 'Toán tốt và thích Data',
            'priority'    => 3,
            'conditions'  => [
                ['fact' => 'ky_nang_toan', 'op' => '==', 'value' => 'tot'],
                ['fact' => 'so_thich',     'op' => '==', 'value' => 'data']
            ],
            'conclusion'  => 'goi_y_data_science',
            'conclusion_value' => true,
            'recommendation' => 'Gợi ý hướng Data Science – kỹ năng toán và sở thích phù hợp.',
            'category'    => 'data'
        ],
        [
            'id'          => 'R05',
            'name'        => 'Thích Web và biết HTML',
            'priority'    => 3,
            'conditions'  => [
                ['fact' => 'so_thich',  'op' => '==', 'value' => 'web'],
                ['fact' => 'biet_html', 'op' => '==', 'value' => true]
            ],
            'conclusion'  => 'goi_y_web_dev',
            'conclusion_value' => true,
            'recommendation' => 'Gợi ý hướng Web Development – có nền tảng công nghệ phù hợp.',
            'category'    => 'web'
        ],
        [
            'id'          => 'R06',
            'name'        => 'Kỹ năng lập trình yếu',
            'priority'    => 5,
            'conditions'  => [
                ['fact' => 'ky_nang_lap_trinh', 'op' => '==', 'value' => 'yeu']
            ],
            'conclusion'  => 'nen_on_lt_co_ban',
            'conclusion_value' => true,
            'recommendation' => 'Nên ôn lại Lập trình cơ bản trước khi học các môn chuyên sâu.',
            'category'    => 'warning'
        ],
        [
            'id'          => 'R07',
            'name'        => 'Điểm cao và thích AI',
            'priority'    => 3,
            'conditions'  => [
                ['fact' => 'diem_tb',  'op' => '>=', 'value' => 8.0],
                ['fact' => 'so_thich', 'op' => '==', 'value' => 'ai']
            ],
            'conclusion'  => 'goi_y_ai_ml',
            'conclusion_value' => true,
            'recommendation' => 'Gợi ý hướng AI/Machine Learning – nền tảng học thuật và sở thích phù hợp.',
            'category'    => 'ai'
        ],
        [
            'id'          => 'R08',
            'name'        => 'Tín chỉ nhiều nhưng điểm thấp',
            'priority'    => 4,
            'conditions'  => [
                ['fact' => 'so_tin_chi_dang_ky', 'op' => '>=', 'value' => 24],
                ['fact' => 'diem_tb',            'op' => '<',  'value' => 6.0]
            ],
            'conclusion'  => 'nen_giam_tin_chi',
            'conclusion_value' => true,
            'recommendation' => 'Đang đăng ký nhiều tín chỉ nhưng điểm TB chưa cao – nên giảm xuống còn 15–18 tín chỉ.',
            'category'    => 'warning'
        ],

        // ── Nhóm 2: Cảnh báo môn tiên quyết nợ (R09, R12, R15, R21 - R30) ────
        [
            'id'          => 'R09',
            'name'        => 'Chưa đạt Toán cao cấp, muốn học XSTK',
            'priority'    => 5,
            'conditions'  => [
                ['fact' => 'chua_dat_toan_cao_cap',  'op' => '==', 'value' => true],
                ['fact' => 'muon_hoc_xstk',           'op' => '==', 'value' => true]
            ],
            'conclusion'  => 'hoc_toan_cao_cap_truoc',
            'conclusion_value' => true,
            'recommendation' => 'Cần hoàn thành môn Toán cao cấp trước khi học Xác suất Thống kê.',
            'category'    => 'warning'
        ],
        [
            'id'          => 'R10',
            'name'        => 'Hoàn thành CTDL và biết OOP',
            'priority'    => 3,
            'conditions'  => [
                ['fact' => 'hoan_thanh_ctdl', 'op' => '==', 'value' => true],
                ['fact' => 'biet_oop',         'op' => '==', 'value' => true]
            ],
            'conclusion'  => 'co_the_hoc_thiet_ke_tt',
            'conclusion_value' => true,
            'recommendation' => 'Có thể học Thiết kế Thuật toán – đã đủ nền tảng CTDL và OOP.',
            'category'    => 'success'
        ],
        [
            'id'          => 'R11',
            'name'        => 'Thích Software và biết OOP',
            'priority'    => 3,
            'conditions'  => [
                ['fact' => 'so_thich', 'op' => '==', 'value' => 'software'],
                ['fact' => 'biet_oop', 'op' => '==', 'value' => true]
            ],
            'conclusion'  => 'goi_y_software_engineering',
            'conclusion_value' => true,
            'recommendation' => 'Gợi ý hướng Software Engineering – sở thích và kỹ năng OOP phù hợp.',
            'category'    => 'software'
        ],
        [
            'id'          => 'R12',
            'name'        => 'Chưa đạt Nhập môn lập trình',
            'priority'    => 5,
            'conditions'  => [
                ['fact' => 'chua_dat_nhap_mon', 'op' => '==', 'value' => true]
            ],
            'conclusion'  => 'hoc_lai_nhap_mon_truoc',
            'conclusion_value' => true,
            'recommendation' => 'Cần học lại Nhập môn Lập trình trước khi tiến lên các môn tiếp theo.',
            'category'    => 'warning'
        ],
        [
            'id'          => 'R13',
            'name'        => 'Điểm tốt, thích hệ thống',
            'priority'    => 2,
            'conditions'  => [
                ['fact' => 'diem_tb',  'op' => '>=', 'value' => 7.0],
                ['fact' => 'so_thich', 'op' => '==', 'value' => 'system']
            ],
            'conclusion'  => 'goi_y_devops',
            'conclusion_value' => true,
            'recommendation' => 'Gợi ý hướng System/DevOps – phù hợp với sở thích hệ thống và năng lực học tập.',
            'category'    => 'system'
        ],
        [
            'id'          => 'R14',
            'name'        => 'Biết Python và thích Data',
            'priority'    => 2,
            'conditions'  => [
                ['fact' => 'biet_python', 'op' => '==', 'value' => true],
                ['fact' => 'so_thich',    'op' => '==', 'value' => 'data']
            ],
            'conclusion'  => 'goi_y_data_science',
            'conclusion_value' => true,
            'recommendation' => 'Gợi ý hướng Data Science – biết Python và có định hướng dữ liệu.',
            'category'    => 'data'
        ],
        [
            'id'          => 'R15',
            'name'        => 'Chưa đạt Toán cao cấp, muốn học MMT',
            'priority'    => 5,
            'conditions'  => [
                ['fact' => 'chua_dat_toan_cao_cap', 'op' => '==', 'value' => true],
                ['fact' => 'muon_hoc_mmt',           'op' => '==', 'value' => true]
            ],
            'conclusion'  => 'hoc_toan_cao_cap_truoc_mmt',
            'conclusion_value' => true,
            'recommendation' => 'Cần hoàn thành Toán cao cấp trước khi đăng ký môn Mạng máy tính.',
            'category'    => 'warning'
        ],
        [
            'id'          => 'R16',
            'name'        => 'Toán tốt và lập trình tốt',
            'priority'    => 3,
            'conditions'  => [
                ['fact' => 'ky_nang_toan',      'op' => '==', 'value' => 'tot'],
                ['fact' => 'ky_nang_lap_trinh', 'op' => '==', 'value' => 'tot']
            ],
            'conclusion'  => 'phu_hop_ai_data',
            'conclusion_value' => true,
            'recommendation' => 'Phù hợp hướng AI/Data – có đủ cả nền tảng toán và tư duy thuật toán.',
            'category'    => 'ai'
        ],
        [
            'id'          => 'R17',
            'name'        => 'Đủ điều kiện đăng ký luận văn',
            'priority'    => 2,
            'conditions'  => [
                ['fact' => 'diem_tb',           'op' => '>=', 'value' => 6.0],
                ['fact' => 'tin_chi_tich_luy',  'op' => '>=', 'value' => 100]
            ],
            'conclusion'  => 'co_the_dang_ky_luan_van',
            'conclusion_value' => true,
            'recommendation' => 'Đủ điều kiện đăng ký Luận văn / Khóa luận tốt nghiệp.',
            'category'    => 'success'
        ],
        [
            'id'          => 'R18',
            'name'        => 'Kỹ năng mềm yếu',
            'priority'    => 1,
            'conditions'  => [
                ['fact' => 'ky_nang_mem', 'op' => '==', 'value' => 'yeu']
            ],
            'conclusion'  => 'nen_tham_gia_clb',
            'conclusion_value' => true,
            'recommendation' => 'Nên tham gia câu lạc bộ, nhóm học tập để cải thiện kỹ năng mềm và giao tiếp.',
            'category'    => 'info'
        ],
        [
            'id'          => 'R19',
            'name'        => 'Thích bảo mật và biết mạng',
            'priority'    => 3,
            'conditions'  => [
                ['fact' => 'so_thich',   'op' => '==', 'value' => 'security'],
                ['fact' => 'biet_mang',  'op' => '==', 'value' => true]
            ],
            'conclusion'  => 'goi_y_cybersecurity',
            'conclusion_value' => true,
            'recommendation' => 'Gợi ý hướng Cybersecurity – sở thích và kiến thức mạng phù hợp.',
            'category'    => 'security'
        ],
        [
            'id'          => 'R20',
            'name'        => 'Điểm TB nguy hiểm',
            'priority'    => 6,
            'conditions'  => [
                ['fact' => 'diem_tb', 'op' => '<', 'value' => 4.0]
            ],
            'conclusion'  => 'canh_bao_hoc_vu',
            'conclusion_value' => true,
            'recommendation' => 'CẢNH BÁO: Điểm TB dưới 4.0 – nguy cơ bị cảnh báo học vụ. Cần gặp cố vấn học tập ngay.',
            'category'    => 'danger'
        ],

        // ── Nhóm cảnh báo tiên quyết cho các môn kỳ tới (R21 - R30) ──────────
        [
            'id'          => 'R21',
            'name'        => 'Chưa đạt Nhập môn LT, muốn học CSDL',
            'priority'    => 5,
            'conditions'  => [
                ['fact' => 'chua_dat_nhap_mon', 'op' => '==', 'value' => true],
                ['fact' => 'muon_hoc_csdl',     'op' => '==', 'value' => true]
            ],
            'conclusion'  => 'hoc_nhap_mon_truoc_csdl',
            'conclusion_value' => true,
            'recommendation' => 'Cần hoàn thành môn Nhập môn Lập trình trước khi đăng ký Cơ sở dữ liệu.',
            'category'    => 'warning'
        ],
        [
            'id'          => 'R22',
            'name'        => 'Chưa đạt Kỹ thuật LT, muốn học Hệ điều hành',
            'priority'    => 5,
            'conditions'  => [
                ['fact' => 'chua_dat_ktlt',    'op' => '==', 'value' => true],
                ['fact' => 'muon_hoc_hdt',     'op' => '==', 'value' => true]
            ],
            'conclusion'  => 'hoc_ktlt_truoc_hdt',
            'conclusion_value' => true,
            'recommendation' => 'Cần hoàn thành môn Kỹ thuật lập trình trước khi học môn Hệ điều hành.',
            'category'    => 'warning'
        ],
        [
            'id'          => 'R23',
            'name'        => 'Chưa đạt Toán cao cấp, muốn học Lý thuyết đồ thị',
            'priority'    => 5,
            'conditions'  => [
                ['fact' => 'chua_dat_toan_cao_cap', 'op' => '==', 'value' => true],
                ['fact' => 'muon_hoc_ltdt',          'op' => '==', 'value' => true]
            ],
            'conclusion'  => 'hoc_toan_truoc_ltdt',
            'conclusion_value' => true,
            'recommendation' => 'Cần hoàn thành Toán cao cấp trước khi học môn Lý thuyết đồ thị.',
            'category'    => 'warning'
        ],
        [
            'id'          => 'R24',
            'name'        => 'Chưa đạt Lập trình HĐT, muốn học Công nghệ phần mềm',
            'priority'    => 5,
            'conditions'  => [
                ['fact' => 'chua_dat_oop_mon', 'op' => '==', 'value' => true],
                ['fact' => 'muon_hoc_cnpm',    'op' => '==', 'value' => true]
            ],
            'conclusion'  => 'hoc_oop_truoc_cnpm',
            'conclusion_value' => true,
            'recommendation' => 'Cần hoàn thành môn Lập trình HĐT trước khi đăng ký môn Công nghệ phần mềm.',
            'category'    => 'warning'
        ],
        [
            'id'          => 'R25',
            'name'        => 'Chưa đạt Toán cao cấp, muốn học Trí tuệ nhân tạo',
            'priority'    => 5,
            'conditions'  => [
                ['fact' => 'chua_dat_toan_cao_cap', 'op' => '==', 'value' => true],
                ['fact' => 'muon_hoc_ttnt',          'op' => '==', 'value' => true]
            ],
            'conclusion'  => 'hoc_toan_truoc_ttnt',
            'conclusion_value' => true,
            'recommendation' => 'Cần hoàn thành môn Toán cao cấp trước khi đăng ký môn Trí tuệ nhân tạo.',
            'category'    => 'warning'
        ],
        [
            'id'          => 'R26',
            'name'        => 'Chưa đạt CTDL, muốn học Trí tuệ nhân tạo',
            'priority'    => 5,
            'conditions'  => [
                ['fact' => 'chua_dat_ctdl',  'op' => '==', 'value' => true],
                ['fact' => 'muon_hoc_ttnt',  'op' => '==', 'value' => true]
            ],
            'conclusion'  => 'hoc_ctdl_truoc_ttnt',
            'conclusion_value' => true,
            'recommendation' => 'Cần hoàn thành môn Cấu trúc dữ liệu & Giải thuật trước khi học Trí tuệ nhân tạo.',
            'category'    => 'warning'
        ],
        [
            'id'          => 'R27',
            'name'        => 'Chưa đạt Lập trình HĐT, muốn học Ứng dụng Desktop',
            'priority'    => 5,
            'conditions'  => [
                ['fact' => 'chua_dat_oop_mon',       'op' => '==', 'value' => true],
                ['fact' => 'muon_hoc_ptud_desktop',  'op' => '==', 'value' => true]
            ],
            'conclusion'  => 'hoc_oop_truoc_desktop',
            'conclusion_value' => true,
            'recommendation' => 'Cần hoàn thành Lập trình HĐT trước khi học môn Phát triển ứng dụng Desktop.',
            'category'    => 'warning'
        ],
        [
            'id'          => 'R28',
            'name'        => 'Chưa đạt Nhập môn LT, muốn học Công nghệ Java',
            'priority'    => 5,
            'conditions'  => [
                ['fact' => 'chua_dat_nhap_mon', 'op' => '==', 'value' => true],
                ['fact' => 'muon_hoc_java',     'op' => '==', 'value' => true]
            ],
            'conclusion'  => 'hoc_nhap_mon_truoc_java',
            'conclusion_value' => true,
            'recommendation' => 'Cần hoàn thành Nhập môn Lập trình trước khi học môn Công nghệ Java.',
            'category'    => 'warning'
        ],
        [
            'id'          => 'R29',
            'name'        => 'Chưa đạt Lập trình HĐT, muốn học Lập trình di động',
            'priority'    => 5,
            'conditions'  => [
                ['fact' => 'chua_dat_oop_mon', 'op' => '==', 'value' => true],
                ['fact' => 'muon_hoc_mobile',  'op' => '==', 'value' => true]
            ],
            'conclusion'  => 'hoc_oop_truoc_mobile',
            'conclusion_value' => true,
            'recommendation' => 'Cần hoàn thành Lập trình HĐT trước khi học môn Lập trình thiết bị di động.',
            'category'    => 'warning'
        ],
        [
            'id'          => 'R30',
            'name'        => 'Chưa biết Mạng, muốn học Bảo mật thông tin',
            'priority'    => 5,
            'conditions'  => [
                ['fact' => 'biet_mang',     'op' => '==', 'value' => false],
                ['fact' => 'muon_hoc_bmtt', 'op' => '==', 'value' => true]
            ],
            'conclusion'  => 'hoc_mang_truoc_bmtt',
            'conclusion_value' => true,
            'recommendation' => 'Cần nắm vững kiến thức Mạng máy tính trước khi đăng ký Bảo mật thông tin.',
            'category'    => 'warning'
        ],

        // ── Nhóm khuyến nghị đủ điều kiện học các môn kỳ tới (R31 - R41) ─────
        [
            'id'          => 'R31',
            'name'        => 'Đủ điều kiện học Xác suất Thống kê',
            'priority'    => 3,
            'conditions'  => [
                ['fact' => 'muon_hoc_xstk',          'op' => '==', 'value' => true],
                ['fact' => 'chua_dat_toan_cao_cap', 'op' => '==', 'value' => false]
            ],
            'conclusion'  => 'co_the_hoc_xstk',
            'conclusion_value' => true,
            'recommendation' => 'Đủ điều kiện đăng ký môn Xác suất Thống kê trong học kỳ tới.',
            'category'    => 'success'
        ],
        [
            'id'          => 'R32',
            'name'        => 'Đủ điều kiện học Mạng máy tính',
            'priority'    => 3,
            'conditions'  => [
                ['fact' => 'muon_hoc_mmt',           'op' => '==', 'value' => true],
                ['fact' => 'chua_dat_toan_cao_cap', 'op' => '==', 'value' => false]
            ],
            'conclusion'  => 'co_the_hoc_mmt',
            'conclusion_value' => true,
            'recommendation' => 'Đủ điều kiện đăng ký môn Mạng máy tính trong học kỳ tới.',
            'category'    => 'success'
        ],
        [
            'id'          => 'R33',
            'name'        => 'Đủ điều kiện học Cơ sở dữ liệu',
            'priority'    => 3,
            'conditions'  => [
                ['fact' => 'muon_hoc_csdl',        'op' => '==', 'value' => true],
                ['fact' => 'chua_dat_nhap_mon',    'op' => '==', 'value' => false]
            ],
            'conclusion'  => 'co_the_hoc_csdl',
            'conclusion_value' => true,
            'recommendation' => 'Đủ điều kiện đăng ký môn Cơ sở dữ liệu – nền tảng dữ liệu quan trọng.',
            'category'    => 'success'
        ],
        [
            'id'          => 'R34',
            'name'        => 'Đủ điều kiện học Hệ điều hành',
            'priority'    => 3,
            'conditions'  => [
                ['fact' => 'muon_hoc_hdt',     'op' => '==', 'value' => true],
                ['fact' => 'chua_dat_ktlt',    'op' => '==', 'value' => false]
            ],
            'conclusion'  => 'co_the_hoc_hdt',
            'conclusion_value' => true,
            'recommendation' => 'Đủ điều kiện đăng ký môn Hệ điều hành trong học kỳ tới.',
            'category'    => 'success'
        ],
        [
            'id'          => 'R35',
            'name'        => 'Đủ điều kiện học Lý thuyết đồ thị',
            'priority'    => 3,
            'conditions'  => [
                ['fact' => 'muon_hoc_ltdt',          'op' => '==', 'value' => true],
                ['fact' => 'chua_dat_toan_cao_cap', 'op' => '==', 'value' => false]
            ],
            'conclusion'  => 'co_the_hoc_ltdt',
            'conclusion_value' => true,
            'recommendation' => 'Đủ điều kiện đăng ký môn Lý thuyết đồ thị – bổ trợ rất tốt cho thuật toán.',
            'category'    => 'success'
        ],
        [
            'id'          => 'R36',
            'name'        => 'Đủ điều kiện học Công nghệ phần mềm',
            'priority'    => 3,
            'conditions'  => [
                ['fact' => 'muon_hoc_cnpm',    'op' => '==', 'value' => true],
                ['fact' => 'chua_dat_oop_mon', 'op' => '==', 'value' => false]
            ],
            'conclusion'  => 'co_the_hoc_cnpm',
            'conclusion_value' => true,
            'recommendation' => 'Đủ điều kiện đăng ký môn Công nghệ phần mềm trong học kỳ tới.',
            'category'    => 'success'
        ],
        [
            'id'          => 'R37',
            'name'        => 'Đủ điều kiện học Trí tuệ nhân tạo',
            'priority'    => 3,
            'conditions'  => [
                ['fact' => 'muon_hoc_ttnt',          'op' => '==', 'value' => true],
                ['fact' => 'chua_dat_toan_cao_cap', 'op' => '==', 'value' => false],
                ['fact' => 'chua_dat_ctdl',          'op' => '==', 'value' => false]
            ],
            'conclusion'  => 'co_the_hoc_ttnt',
            'conclusion_value' => true,
            'recommendation' => 'Đủ điều kiện đăng ký môn Trí tuệ nhân tạo – kiến thức toán và thuật toán đạt chuẩn.',
            'category'    => 'success'
        ],
        [
            'id'          => 'R38',
            'name'        => 'Đủ điều kiện học Bảo mật thông tin',
            'priority'    => 3,
            'conditions'  => [
                ['fact' => 'muon_hoc_bmtt', 'op' => '==', 'value' => true],
                ['fact' => 'biet_mang',     'op' => '==', 'value' => true]
            ],
            'conclusion'  => 'co_the_hoc_bmtt',
            'conclusion_value' => true,
            'recommendation' => 'Đủ điều kiện đăng ký môn Bảo mật thông tin – kiến thức mạng đáp ứng tốt.',
            'category'    => 'success'
        ],
        [
            'id'          => 'R39',
            'name'        => 'Đủ điều kiện học Ứng dụng Desktop',
            'priority'    => 3,
            'conditions'  => [
                ['fact' => 'muon_hoc_ptud_desktop', 'op' => '==', 'value' => true],
                ['fact' => 'chua_dat_oop_mon',      'op' => '==', 'value' => false]
            ],
            'conclusion'  => 'co_the_hoc_ptud_desktop',
            'conclusion_value' => true,
            'recommendation' => 'Đủ điều kiện đăng ký môn Phát triển ứng dụng Desktop.',
            'category'    => 'success'
        ],
        [
            'id'          => 'R40',
            'name'        => 'Đủ điều kiện học Công nghệ Java',
            'priority'    => 3,
            'conditions'  => [
                ['fact' => 'muon_hoc_java',      'op' => '==', 'value' => true],
                ['fact' => 'chua_dat_nhap_mon',  'op' => '==', 'value' => false]
            ],
            'conclusion'  => 'co_the_hoc_java',
            'conclusion_value' => true,
            'recommendation' => 'Đủ điều kiện đăng ký môn Công nghệ Java trong học kỳ tới.',
            'category'    => 'success'
        ],
        [
            'id'          => 'R41',
            'name'        => 'Đủ điều kiện học Lập trình di động',
            'priority'    => 3,
            'conditions'  => [
                ['fact' => 'muon_hoc_mobile',   'op' => '==', 'value' => true],
                ['fact' => 'chua_dat_oop_mon',  'op' => '==', 'value' => false]
            ],
            'conclusion'  => 'co_the_hoc_mobile',
            'conclusion_value' => true,
            'recommendation' => 'Đủ điều kiện đăng ký môn Lập trình thiết bị di động.',
            'category'    => 'success'
        ],
    ];
}
