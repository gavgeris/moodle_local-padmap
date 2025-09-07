<?php
require_once(__DIR__ . '/../../config.php');

require_login();
\core\session\manager::write_close();

$PAGE->set_context(context_system::instance());

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die;
}

$input = json_decode(file_get_contents('php://input'), true) ?? [];
if (!isset($input['sesskey']) || !confirm_sesskey($input['sesskey'])) {
    http_response_code(403);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status' => 'error', 'message' => 'invalidsesskey']);
    exit;
}

$action   = $input['action']   ?? '';
$username = $input['username'] ?? '';
$pad      = $input['pad']      ?? '';

header('Content-Type: application/json; charset=utf-8');

if ($action !== 'claim' || empty($username) || empty($pad)) {
    echo json_encode(['status'=>'error','message'=>'bad request']); exit;
}

global $DB;
$now   = time();
$trans = $DB->start_delegated_transaction();

try {
    // Αν ο χρήστης έχει ήδη αντιστοίχιση, επιστρέφουμε αυτό.
    if ($existing = $DB->get_record('local_padmap', ['username' => $username])) {
        $trans->allow_commit();
        echo json_encode(['status'=>'already','pad'=>$existing->padurl]);
        exit;
    }

    // === ΣΗΜΑΝΤΙΚΟ: σύγκριση TEXT με sql_compare_text() ===
    $compare = $DB->sql_compare_text('padurl', 1024) . ' = ' . $DB->sql_compare_text(':pad', 1024);
    $taken = $DB->record_exists_select('local_padmap', $compare, ['pad' => $pad]);
    if ($taken) {
        $trans->allow_commit();
        echo json_encode(['status'=>'taken']);
        exit;
    }

    $rec = (object)[
        'username'     => $username,
        'padurl'       => $pad,
        'timecreated'  => $now,
        'timemodified' => $now,
    ];
    $DB->insert_record('local_padmap', $rec);

    $trans->allow_commit();
    echo json_encode(['status'=>'ok','pad'=>$pad]);

} catch (Throwable $e) {
    $trans->rollback($e);
    echo json_encode(['status'=>'taken']);
}
