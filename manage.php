<?php

require_once(__DIR__ . '/../../config.php');

require_login();
$context = context_system::instance();
require_capability('local/padmap:manage', $context);

$action = optional_param('action', '', PARAM_ALPHA);
$username = optional_param('username', '', PARAM_RAW);
$confirm = optional_param('confirm', 0, PARAM_BOOL);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/padmap/manage.php', ['action' => $action, 'username' => $username]));
$PAGE->set_title(get_string('pluginname', 'local_padmap'));
$PAGE->set_heading(get_string('pluginname', 'local_padmap'));

echo $OUTPUT->header();

if ($action === 'delete' && $username) {
    // Επιβεβαίωση
    if (!$confirm) {
        $yesurl = new moodle_url('/local/padmap/manage.php', [
            'action' => 'delete',
            'username' => $username,
            'confirm' => 1,
            'sesskey' => sesskey()
        ]);
        $nourl = new moodle_url('/local/padmap/index.php');
        echo $OUTPUT->confirm(get_string('confirmdelete', 'local_padmap', s($username)), $yesurl, $nourl);
        echo $OUTPUT->footer();
        exit;
    }
    require_sesskey();
    // Διαγραφή με βάση username (όχι TEXT πεδίο → δεν χρειάζεται sql_compare_text)
    $DB->delete_records('local_padmap', ['username' => $username]);
    \core\notification::success(get_string('deletedmapping', 'local_padmap', s($username)));
    echo $OUTPUT->continue_button(new moodle_url('/local/padmap/index.php'));
    echo $OUTPUT->footer();
    exit;
}

// Προαιρετικά: “Purge all”
if ($action === 'purgeall') {
    if (!$confirm) {
        $yesurl = new moodle_url('/local/padmap/manage.php', [
            'action' => 'purgeall',
            'confirm' => 1,
            'sesskey' => sesskey()
        ]);
        $nourl = new moodle_url('/local/padmap/index.php');
        echo $OUTPUT->confirm(get_string('confirmpurgeall', 'local_padmap'), $yesurl, $nourl);
        echo $OUTPUT->footer();
        exit;
    }
    require_sesskey();
    $DB->delete_records('local_padmap');
    \core\notification::success(get_string('purgedall', 'local_padmap'));
    echo $OUTPUT->continue_button(new moodle_url('/local/padmap/index.php'));
    echo $OUTPUT->footer();
    exit;
}

echo $OUTPUT->notification(get_string('invalidaction', 'local_padmap'), \core\output\notification::NOTIFY_WARNING);
echo $OUTPUT->continue_button(new moodle_url('/local/padmap/index.php'));
echo $OUTPUT->footer();
