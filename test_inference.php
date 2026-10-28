<?php
// Simulate POST request to test inference engine
$_SERVER['REQUEST_METHOD'] = 'GET'; // bypass OPTIONS check

require 'c:/xampp/htdocs/expert-system/api/rules.php';

// Copy normalizeFacts và các hàm từ inference.php
function checkConditions(array $conditions, array $facts): array {
    $details = [];
    foreach ($conditions as $cond) {
        $fact  = $cond['fact'];
        $op    = $cond['op'];
        $value = $cond['value'];
        if (!array_key_exists($fact, $facts)) {
            return [false, "$fact chua co du kien"];
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
        if (!$ok) return [false, "$fact($factVal) $op $value -> sai"];
        $details[] = "$fact($factVal) $op $value OK";
    }
    return [true, implode(', ', $details)];
}

function buildExplanation(array $conditions, array $facts): string {
    $parts = [];
    foreach ($conditions as $cond) {
        $factVal = $facts[$cond['fact']] ?? '?';
        if (is_bool($factVal)) $factVal = $factVal ? 'true' : 'false';
        $parts[] = "{$cond['fact']}({$factVal}) {$cond['op']} {$cond['value']}";
    }
    return implode(' AND ', $parts);
}

function forwardChaining(array $facts, array $rules): array {
    $activatedRules  = [];
    $recommendations = [];
    $chainTrace      = [];
    $workingMemory   = $facts;
    usort($rules, fn($a, $b) => $b['priority'] <=> $a['priority']);
    $changed = true; $iteration = 0;
    while ($changed) {
        $changed = false; $iteration++;
        $chainTrace[] = "-- Vong lap $iteration --";
        foreach ($rules as $rule) {
            $alreadyActivated = array_search($rule['id'], array_column($activatedRules, 'id')) !== false;
            if ($alreadyActivated) continue;
            [$satisfied, $condDetails] = checkConditions($rule['conditions'], $workingMemory);
            if ($satisfied) {
                $activatedRules[] = ['id' => $rule['id'], 'name' => $rule['name']];
                $workingMemory[$rule['conclusion']] = $rule['conclusion_value'];
                $recommendations[] = "[{$rule['id']}] " . $rule['recommendation'];
                $chainTrace[] = "  KICH HOAT [{$rule['id']}] {$rule['name']}";
                $changed = true;
            }
        }
    }
    return ['activated' => $activatedRules, 'recommendations' => $recommendations, 'trace' => $chainTrace];
}

// Test SV01 - Nguyễn Văn An
$sv01 = [
    'diem_tb' => 8.5, 'ky_nang_lap_trinh' => 'tot', 'ky_nang_toan' => 'tot',
    'ky_nang_mem' => 'trung_binh', 'so_thich' => 'ai',
    'so_tin_chi_dang_ky' => 18, 'tin_chi_tich_luy' => 90,
    'biet_html' => true, 'biet_python' => true, 'biet_oop' => true, 'biet_mang' => false,
    'chua_dat_lt_co_ban' => false, 'chua_dat_nhap_mon' => false, 'chua_dat_giai_tich' => false,
    'chua_dat_toan_roi_rac' => false, 'hoan_thanh_ctdl' => true,
    'muon_hoc_xstk' => false, 'muon_hoc_mmt' => false,
];

echo "=== TEST SV01: Nguyen Van An ===\n";
$result = forwardChaining($sv01, getRules());
echo "\nLuat kich hoat (" . count($result['activated']) . "):\n";
foreach ($result['activated'] as $r) echo "  [{$r['id']}] {$r['name']}\n";
echo "\nKhuyen nghi:\n";
foreach ($result['recommendations'] as $rec) echo "  * $rec\n";
echo "\nChain Trace:\n";
foreach ($result['trace'] as $t) echo "  $t\n";

// Test SV02 - Trần Thị Bình
echo "\n\n=== TEST SV02: Tran Thi Binh ===\n";
$sv02 = [
    'diem_tb' => 3.8, 'ky_nang_lap_trinh' => 'yeu', 'ky_nang_toan' => 'yeu',
    'ky_nang_mem' => 'yeu', 'so_thich' => 'web',
    'so_tin_chi_dang_ky' => 22, 'tin_chi_tich_luy' => 12,
    'biet_html' => false, 'biet_python' => false, 'biet_oop' => false, 'biet_mang' => false,
    'chua_dat_lt_co_ban' => true, 'chua_dat_nhap_mon' => true, 'chua_dat_giai_tich' => true,
    'chua_dat_toan_roi_rac' => false, 'hoan_thanh_ctdl' => false,
    'muon_hoc_xstk' => false, 'muon_hoc_mmt' => false,
];
$result2 = forwardChaining($sv02, getRules());
echo "\nLuat kich hoat (" . count($result2['activated']) . "):\n";
foreach ($result2['activated'] as $r) echo "  [{$r['id']}] {$r['name']}\n";
echo "\nKhuyen nghi:\n";
foreach ($result2['recommendations'] as $rec) echo "  * $rec\n";
