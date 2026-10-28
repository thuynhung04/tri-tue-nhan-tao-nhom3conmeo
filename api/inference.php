<?php
// CORS headers – phải đặt trước mọi output
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept');
header('Access-Control-Max-Age: 3600');
header('Content-Type: application/json; charset=utf-8');

if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

/**
 * INFERENCE ENGINE - Cơ chế suy luận
 * Hệ Tư Vấn Lộ Trình Học Tập Cho Sinh Viên
 * Hỗ trợ: Forward Chaining & Backward Chaining
 */

require_once __DIR__ . '/rules.php';

// ─── Nhận dữ liệu (chỉ chạy khi được gọi trực tiếp qua HTTP) ────────────────
if (php_sapi_name() !== 'cli' || !empty($rawInput = file_get_contents('php://input'))) {
    $rawInput = file_get_contents('php://input');
    $data     = json_decode($rawInput, true);

    if (!$data) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Dữ liệu JSON không hợp lệ']);
        exit();
    }

    $facts  = normalizeFacts($data['facts'] ?? []);
    $method = $data['method'] ?? 'forward';
    $goal   = $data['goal']   ?? 'goi_y_ai_ml';

    if ($method === 'backward') {
        $result = backwardChaining($facts, $goal);
    } else {
        $result = forwardChaining($facts);
    }

    echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit();
}

// ════════════════════════════════════════════════════════════════════════════
// 1. FORWARD CHAINING (Suy luận tiến)
// ════════════════════════════════════════════════════════════════════════════
function forwardChaining(array $facts): array
{
    $rules = getRules();
    usort($rules, fn($a, $b) => $b['priority'] <=> $a['priority']);

    $workingMemory   = $facts;
    $activatedRules  = [];
    $recommendations = [];
    $warnings        = [];
    $chainTrace      = [];
    $firedRuleIds    = [];

    $chainTrace[] = "=== BẮT ĐẦU SUY LUẬN TIẾN (FORWARD CHAINING) ===";
    $chainTrace[] = "Dữ kiện ban đầu: " . count($facts) . " facts được nạp vào Working Memory.";

    $changed = true;
    $step    = 1;

    while ($changed) {
        $changed = false;
        $chainTrace[] = "── Bước lặp $step ─────────────────────────────";

        foreach ($rules as $rule) {
            if (in_array($rule['id'], $firedRuleIds)) continue;

            [$satisfied, $condDetails] = checkConditions($rule['conditions'], $workingMemory);

            if ($satisfied) {
                $activatedRules[] = [
                    'id'          => $rule['id'],
                    'name'        => $rule['name'],
                    'explanation' => buildExplanation($rule['conditions'], $workingMemory),
                    'conclusion'  => $rule['conclusion'],
                    'category'    => $rule['category'],
                ];

                $workingMemory[$rule['conclusion']] = $rule['conclusion_value'];

                $recommendations[] = [
                    'rule_id'  => $rule['id'],
                    'text'     => $rule['recommendation'],
                    'category' => $rule['category'],
                ];

                if ($rule['category'] === 'warning' || $rule['category'] === 'danger') {
                    $warnings[] = $rule['recommendation'];
                }

                $chainTrace[] = "  ✅ [{$rule['id']}] {$rule['name']} → KÍCH HOẠT";
                $chainTrace[] = "     Điều kiện: $condDetails";
                $chainTrace[] = "     → Thêm '{$rule['conclusion']}' = " . var_export($rule['conclusion_value'], true) . " vào Working Memory";

                $firedRuleIds[] = $rule['id'];
                $changed = true;
            } else {
                $chainTrace[] = "  ❌ [{$rule['id']}] {$rule['name']} → Không thỏa ($condDetails)";
            }
        }
        $step++;
    }

    return [
        'success'          => true,
        'method'           => 'forward',
        'activated_rules'  => $activatedRules,
        'recommendations'  => $recommendations,
        'warnings'         => $warnings,
        'chain_trace'      => $chainTrace,
        'working_memory'   => $workingMemory,
        'total_activated'  => count($activatedRules),
        'human_summary'    => generateHumanSummary($recommendations, $facts),
    ];
}

// ════════════════════════════════════════════════════════════════════════════
// 2. BACKWARD CHAINING (Suy luận lùi)
// ════════════════════════════════════════════════════════════════════════════
function backwardChaining(array $facts, string $goal): array
{
    $rules = getRules();
    $workingMemory   = $facts;
    $activatedRules  = [];
    $trace           = [];

    $trace[] = "=== BẮT ĐẦU SUY LUẬN LÙI (BACKWARD CHAINING) ===";
    $trace[] = "Mục tiêu cần chứng minh: '$goal'";

    $proved = backwardProve($goal, $workingMemory, $rules, $activatedRules, $trace, 0);

    // Kiểm tra thêm các môn học kỳ tới nếu sinh viên có chọn
    $courseGoals = [
        'muon_hoc_xstk'         => ['co_the_hoc_xstk',         'hoc_toan_cao_cap_truoc'],
        'muon_hoc_mmt'          => ['co_the_hoc_mmt',          'hoc_toan_cao_cap_truoc_mmt'],
        'muon_hoc_csdl'         => ['co_the_hoc_csdl',         'hoc_nhap_mon_truoc_csdl'],
        'muon_hoc_hdt'          => ['co_the_hoc_hdt',          'hoc_ktlt_truoc_hdt'],
        'muon_hoc_ltdt'         => ['co_the_hoc_ltdt',         'hoc_toan_truoc_ltdt'],
        'muon_hoc_cnpm'         => ['co_the_hoc_cnpm',         'hoc_oop_truoc_cnpm'],
        'muon_hoc_ttnt'         => ['co_the_hoc_ttnt',         'hoc_toan_truoc_ttnt', 'hoc_ctdl_truoc_ttnt'],
        'muon_hoc_bmtt'         => ['co_the_hoc_bmtt',         'hoc_mang_truoc_bmtt'],
        'muon_hoc_ptud_desktop' => ['co_the_hoc_ptud_desktop', 'hoc_oop_truoc_desktop'],
        'muon_hoc_java'         => ['co_the_hoc_java',         'hoc_nhap_mon_truoc_java'],
        'muon_hoc_mobile'       => ['co_the_hoc_mobile',       'hoc_oop_truoc_mobile'],
    ];

    $hasRegisteredCourses = false;
    foreach ($courseGoals as $factKey => $subGoals) {
        if (!empty($facts[$factKey])) {
            $hasRegisteredCourses = true;
            break;
        }
    }

    if ($hasRegisteredCourses) {
        $trace[] = "───────────────────────────────────────────────────────";
        $trace[] = "Kiểm tra điều kiện tiên quyết cho các môn muốn học kỳ tới:";
        foreach ($courseGoals as $factKey => $subGoals) {
            if (!empty($facts[$factKey])) {
                foreach ($subGoals as $subGoal) {
                    backwardProve($subGoal, $workingMemory, $rules, $activatedRules, $trace, 1);
                }
            }
        }
    }

    // Lọc lại các rule đã kích hoạt (loại bỏ trùng lặp)
    $uniqueActivated = [];
    $recommendations = [];
    $warnings        = [];

    foreach ($activatedRules as $r) {
        if (!isset($uniqueActivated[$r['id']])) {
            $uniqueActivated[$r['id']] = $r;
            $recommendations[] = [
                'rule_id'  => $r['id'],
                'text'     => $r['recommendation'],
                'category' => $r['category'],
            ];
            if ($r['category'] === 'warning' || $r['category'] === 'danger') {
                $warnings[] = $r['recommendation'];
            }
        }
    }

    return [
        'success'         => true,
        'method'          => 'backward',
        'goal'            => $goal,
        'proved'          => $proved,
        'activated_rules' => array_values($uniqueActivated),
        'recommendations' => $recommendations,
        'warnings'        => $warnings,
        'chain_trace'     => $trace,
        'working_memory'  => $workingMemory,
        'total_activated' => count($uniqueActivated),
        'human_summary'   => generateHumanSummary($recommendations, $facts),
    ];
}

/**
 * Đệ quy chứng minh mục tiêu theo đúng chuẩn Backward Chaining
 */
function backwardProve(
    string $goal,
    array  &$workingMemory,
    array  $rules,
    array  &$activatedRules,
    array  &$trace,
    int    $depth
): bool {
    $indent = str_repeat('  ', $depth);

    // Trường hợp 1: Nếu mục tiêu đã được suy diễn thành công và lưu true trong Working Memory
    if (isset($workingMemory[$goal]) && $workingMemory[$goal] === true) {
        $trace[] = "{$indent}✔ Mục tiêu '$goal' đã được xác thực trước đó trong Working Memory.";
        return true;
    }

    $trace[] = "{$indent}? Cần chứng minh mục tiêu: '$goal'";

    // Trường hợp 2: Tìm các luật có conclusion === $goal
    $matchingRules = [];
    foreach ($rules as $rule) {
        if ($rule['conclusion'] === $goal) {
            $matchingRules[] = $rule;
        }
    }

    if (empty($matchingRules)) {
        $trace[] = "{$indent}✗ Không có luật nào trong cơ sở tri thức có kết luận là '$goal'";
        return false;
    }

    foreach ($matchingRules as $rule) {
        $trace[] = "{$indent}→ Thử luật [{$rule['id']}]: {$rule['name']}";
        $allSatisfied = true;

        foreach ($rule['conditions'] as $cond) {
            $condFact = $cond['fact'];
            $op       = $cond['op'];
            $reqVal   = $cond['value'];

            // Nếu dữ kiện đã có trong Working Memory (Terminal Fact do người dùng nhập)
            if (array_key_exists($condFact, $workingMemory)) {
                $actualVal = $workingMemory[$condFact];

                $ok = match($op) {
                    '=='  => $actualVal == $reqVal,
                    '!='  => $actualVal != $reqVal,
                    '>'   => $actualVal >  $reqVal,
                    '>='  => $actualVal >= $reqVal,
                    '<'   => $actualVal <  $reqVal,
                    '<='  => $actualVal <= $reqVal,
                    default => false,
                };

                $actDisp = is_bool($actualVal) ? ($actualVal ? 'true' : 'false') : $actualVal;
                $reqDisp = is_bool($reqVal) ? ($reqVal ? 'true' : 'false') : $reqVal;

                if ($ok) {
                    $trace[] = "{$indent}  ✔ Dữ kiện '$condFact' = '$actDisp' thỏa mãn điều kiện ($op $reqDisp)";
                } else {
                    $trace[] = "{$indent}  ❌ Dữ kiện '$condFact' = '$actDisp' KHÔNG thỏa mãn điều kiện (yêu cầu $op $reqDisp)";
                    $allSatisfied = false;
                    break; // Luật này thất bại, chuyển sang luật tiếp theo
                }
            } else {
                // Fact chưa có trong workingMemory -> Cần đệ quy chứng minh như một sub-goal
                $subProved = backwardProve($condFact, $workingMemory, $rules, $activatedRules, $trace, $depth + 1);
                if (!$subProved) {
                    $trace[] = "{$indent}  ❌ Không thể chứng minh điều kiện con '$condFact' cho luật [{$rule['id']}]";
                    $allSatisfied = false;
                    break;
                }
            }
        }

        if ($allSatisfied) {
            // Đã thỏa mãn tất cả điều kiện của luật
            $workingMemory[$goal] = $rule['conclusion_value'];
            $activatedRules[] = [
                'id'            => $rule['id'],
                'name'          => $rule['name'],
                'explanation'   => buildExplanation($rule['conditions'], $workingMemory),
                'conclusion'    => $rule['conclusion'],
                'category'      => $rule['category'],
                'recommendation'=> $rule['recommendation'],
            ];
            $trace[] = "{$indent}✅ CHỨNG MINH THÀNH CÔNG: Mục tiêu '$goal' đạt được qua luật [{$rule['id']}]: {$rule['name']}";
            return true;
        } else {
            $trace[] = "{$indent}  → Bỏ qua luật [{$rule['id']}] do không thỏa điều kiện.";
        }
    }

    $trace[] = "{$indent}✗ Thất bại: Không thể chứng minh mục tiêu '$goal' (các điều kiện tiên quyết chưa đáp ứng).";
    return false;
}

// ════════════════════════════════════════════════════════════════════════════
// 3. HELPERS
// ════════════════════════════════════════════════════════════════════════════

function checkConditions(array $conditions, array $facts): array
{
    $details = [];
    foreach ($conditions as $cond) {
        $fact  = $cond['fact'];
        $op    = $cond['op'];
        $value = $cond['value'];

        if (!array_key_exists($fact, $facts)) {
            $details[] = "$fact chưa có dữ kiện";
            return [false, implode(', ', $details)];
        }

        $factVal = $facts[$fact];
        $ok = match($op) {
            '=='  => $factVal == $value,
            '!='  => $factVal != $value,
            '>'   => $factVal >  $value,
            '>='  => $factVal >= $value,
            '<'   => $factVal <  $value,
            '<='  => $factVal <= $value,
            default => false,
        };

        $actDisp = is_bool($factVal) ? ($factVal ? 'true' : 'false') : $factVal;
        $reqDisp = is_bool($value) ? ($value ? 'true' : 'false') : $value;

        if (!$ok) {
            $details[] = "$fact($actDisp) $op $reqDisp → sai";
            return [false, implode(', ', $details)];
        }
        $details[] = "$fact($actDisp) $op $reqDisp ✓";
    }
    return [true, implode(', ', $details)];
}

function buildExplanation(array $conditions, array $facts): string
{
    $parts = [];
    foreach ($conditions as $cond) {
        $factVal = $facts[$cond['fact']] ?? '?';
        if (is_bool($factVal)) $factVal = $factVal ? 'true' : 'false';
        $reqDisp = is_bool($cond['value']) ? ($cond['value'] ? 'true' : 'false') : $cond['value'];
        $parts[] = "{$cond['fact']}({$factVal}) {$cond['op']} {$reqDisp}";
    }
    return implode(' AND ', $parts);
}

function normalizeFacts(array $facts): array
{
    $boolKeys = [
        // Kỹ năng & công nghệ
        'biet_html','biet_python','biet_oop','biet_mang',
        // Môn chưa đạt (nợ)
        'chua_dat_lt_co_ban','chua_dat_nhap_mon','chua_dat_toan_cao_cap',
        'chua_dat_ctdl','chua_dat_ktlt','chua_dat_oop','chua_dat_oop_mon',
        // Môn đã hoàn thành
        'hoan_thanh_nhap_mon','hoan_thanh_toan_cao_cap','hoan_thanh_ktlt',
        'hoan_thanh_oop','hoan_thanh_ctdl',
        // Môn muốn học kỳ tới
        'muon_hoc_xstk','muon_hoc_mmt','muon_hoc_hdt','muon_hoc_csdl',
        'muon_hoc_ltdt','muon_hoc_cnpm','muon_hoc_ttnt','muon_hoc_bmtt',
        'muon_hoc_ptud_desktop','muon_hoc_java','muon_hoc_mobile',
    ];
    $numKeys = ['diem_tb','so_tin_chi_dang_ky','tin_chi_tich_luy'];

    foreach ($boolKeys as $k) {
        if (isset($facts[$k])) {
            $facts[$k] = filter_var($facts[$k], FILTER_VALIDATE_BOOLEAN);
        } else {
            $facts[$k] = false;
        }
    }
    foreach ($numKeys as $k) {
        if (isset($facts[$k])) {
            $facts[$k] = (float) $facts[$k];
        }
    }
    return $facts;
}

/**
 * Tạo câu tư vấn "văn nói" hoàn chỉnh, tổng hợp định hướng và tình trạng môn đăng ký
 */
function generateHumanSummary(array $recommendations, array $facts): string
{
    $categories = array_column($recommendations, 'category');
    $hasDanger   = in_array('danger',   $categories);
    $hasWarning  = in_array('warning',  $categories);
    $hasAI       = in_array('ai',       $categories);
    $hasData     = in_array('data',     $categories);
    $hasWeb      = in_array('web',      $categories);
    $hasSoftware = in_array('software', $categories);
    $hasSecurity = in_array('security', $categories);
    $hasSystem   = in_array('system',   $categories);
    $hasSuccess  = in_array('success',  $categories);
    $dtb         = $facts['diem_tb'] ?? 0;
    $soThich     = $facts['so_thich'] ?? '';

    // 1. Phân loại theo sở thích
    $directionMap = [
        'ai'       => ['name' => 'Trí Tuệ Nhân Tạo / Machine Learning',
                       'advice' => 'Sinh viên nên ưu tiên học vững Toán cao cấp, Xác suất thống kê, Nhập môn AI rồi tiến đến Machine Learning, Deep Learning. Cần thực hành thường xuyên với Python và các thư viện NumPy, Pandas.'],
        'data'     => ['name' => 'Khoa Học Dữ Liệu (Data Science)',
                       'advice' => 'Sinh viên nên học vững Toán cao cấp, Xác suất thống kê, Cơ sở dữ liệu, sau đó tiến đến Data Mining và Trực quan hóa dữ liệu. Cần thành thạo Python và SQL.'],
        'web'      => ['name' => 'Lập Trình Web (Web Development)',
                       'advice' => 'Sinh viên nên nắm chắc HTML/CSS/JavaScript, sau đó chọn một framework hiện đại như Vue.js/React (frontend) hoặc Laravel/Node.js (backend) kết hợp xây dựng RESTful API.'],
        'software' => ['name' => 'Kỹ Nghệ Phần Mềm (Software Engineering)',
                       'advice' => 'Sinh viên nên hoàn thành tốt Lập trình hướng đối tượng, Cấu trúc dữ liệu, Công nghệ phần mềm. Tiếp đó tìm hiểu Design Pattern, kiểm thử phần mềm và mô hình Scrum.'],
        'security' => ['name' => 'An Toàn Thông Tin (Cybersecurity)',
                       'advice' => 'Sinh viên nên học chắc Mạng máy tính, Hệ điều hành, sau đó tiến đến An toàn mạng, Mật mã học và tham gia các cuộc thi CTF thực chiến.'],
        'system'   => ['name' => 'Hệ Thống / DevOps',
                       'advice' => 'Sinh viên nên nắm vững Hệ điều hành, Mạng máy tính, tiếp theo học Linux, Docker, CI/CD và triển khai ứng dụng trên Cloud.'],
    ];

    if ($hasDanger) {
        return "🚨 Sinh viên cần gặp cố vấn học tập ngay lập tức! Điểm TB hiện tại ({$dtb}) ở mức nguy hiểm – có nguy cơ bị cảnh báo học vụ. Ưu tiên hàng đầu là cải thiện điểm số các môn nợ, tuyệt đối chưa nên đăng ký thêm môn học mới.";
    }

    $detectedDir = '';
    if ($hasAI || $hasData)  $detectedDir = ($hasAI ? 'ai' : 'data');
    elseif ($hasWeb)         $detectedDir = 'web';
    elseif ($hasSoftware)    $detectedDir = 'software';
    elseif ($hasSecurity)    $detectedDir = 'security';
    elseif ($hasSystem)      $detectedDir = 'system';

    $finalDir = $detectedDir ?: $soThich;
    $dirInfo  = $directionMap[$finalDir] ?? null;

    $level = $dtb >= 8.0 ? 'xuất sắc' : ($dtb >= 7.0 ? 'khá tốt' : ($dtb >= 5.0 ? 'trung bình' : 'còn yếu'));
    $summary = "Sinh viên có học lực {$level} (ĐTB {$dtb}).";

    if ($dirInfo) {
        $summary .= " Dựa trên hồ sơ và sở thích, sinh viên nên theo đuổi hướng {$dirInfo['name']}. {$dirInfo['advice']}";
    }

    // Tổng hợp tình trạng các môn học kỳ tới được tư vấn
    $approvedCourses = [];
    $blockedCourses  = [];
    foreach ($recommendations as $rec) {
        $text = $rec['text'];
        if ($rec['category'] === 'success' && str_contains($text, 'Đủ điều kiện đăng ký môn')) {
            preg_match('/Đủ điều kiện đăng ký môn (.*?)(?: trong| –|\.)/', $text, $matches);
            if (!empty($matches[1])) $approvedCourses[] = trim($matches[1]);
        } elseif ($rec['category'] === 'warning' && str_contains($text, 'Cần hoàn thành')) {
            $blockedCourses[] = $text;
        }
    }

    if (!empty($approvedCourses)) {
        $summary .= " 👉 Về môn học kỳ tới: Sinh viên đủ điều kiện đăng ký các môn: " . implode(', ', array_unique($approvedCourses)) . ".";
    }
    if (!empty($blockedCourses)) {
        $summary .= " ⚠️ Cần đặc biệt lưu ý: " . implode(' ', array_unique($blockedCourses));
    }

    return $summary;
}
