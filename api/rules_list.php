<?php
/**
 * API trả về danh sách luật để hiển thị trên giao diện "Tập Luật"
 */
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/rules.php';

$rules = getRules();

// Chỉ trả về thông tin cần thiết cho giao diện
$output = array_map(fn($r) => [
    'id'             => $r['id'],
    'name'           => $r['name'],
    'priority'       => $r['priority'],
    'conditions'     => array_map(fn($c) =>
        "{$c['fact']} {$c['op']} " . (is_bool($c['value']) ? ($c['value'] ? 'true' : 'false') : $c['value']),
        $r['conditions']
    ),
    'conclusion'     => $r['conclusion'],
    'recommendation' => $r['recommendation'],
    'category'       => $r['category'],
], $rules);

echo json_encode($output, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
