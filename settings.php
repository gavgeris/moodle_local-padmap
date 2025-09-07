<?php
defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $ADMIN->add('localplugins', new admin_category('local_padmap_cat', get_string('pluginname', 'local_padmap')));

    $settings = new admin_settingpage('local_padmap_settings', get_string('settings', 'local_padmap'));
    // (Δεν έχουμε ρυθμίσεις προς το παρόν.)
    $ADMIN->add('local_padmap_cat', $settings);

    // Σύνδεσμος προβολής.
    $ADMIN->add('local_padmap_cat', new admin_externalpage(
        'local_padmap_index',
        get_string('mappinglist', 'local_padmap'),
        new moodle_url('/local/padmap/index.php'),
        'local/padmap:view'
    ));
}
