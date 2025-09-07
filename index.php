<?php
require_once(__DIR__ . '/../../config.php');

require_login();
$context = context_system::instance();
require_capability('local/padmap:view', $context);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/padmap/index.php'));
$PAGE->set_title(get_string('pluginname', 'local_padmap'));
$PAGE->set_heading(get_string('pluginname', 'local_padmap'));

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('mappinglist', 'local_padmap'));

global $DB;
$records = $DB->get_records('local_padmap', null, 'timemodified DESC');

if ($records) {
    $table = new html_table();
    $table->head = [
        get_string('username'),
        get_string('padurl', 'local_padmap'),
        get_string('timecreated', 'local_padmap'),
        get_string('timemodified', 'local_padmap'),
        get_string('actions'),
    ];

    foreach ($records as $r) {
        $deleteurl = new moodle_url('/local/padmap/manage.php', [
            'action' => 'delete',
            'username' => $r->username
        ]);
        $actions = html_writer::link($deleteurl, get_string('delete'));
        $table->data[] = [
            s($r->username),
            html_writer::link($r->padurl, shorten_text($r->padurl, 80), ['target' => '_blank', 'rel' => 'noopener']),
            userdate($r->timecreated),
            userdate($r->timemodified),
            $actions
        ];
    }
    $purgeurl = new moodle_url('/local/padmap/manage.php', ['action' => 'purgeall']);
    echo html_writer::div(
        html_writer::link($purgeurl, get_string('purgeall', 'local_padmap'), ['class'=>'btn btn-secondary'])
    );

    echo html_writer::table($table);
} else {
    echo $OUTPUT->notification(get_string('nomappings', 'local_padmap'), \core\output\notification::NOTIFY_INFO);
}

echo $OUTPUT->footer();
