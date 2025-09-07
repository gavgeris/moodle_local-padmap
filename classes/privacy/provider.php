<?php

namespace local_padmap\privacy;

defined('MOODLE_INTERNAL') || die();

use core_privacy\local\metadata\collection;
use core_privacy\local\request\writer;
use core_privacy\local\request\approved_contextlist;

/**
 * Privacy provider for local_padmap.
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\plugin\provider
{

    public static function get_metadata(collection $items): collection
    {
        $items->add_database_table('local_padmap', [
            'username' => 'privacy:metadata:local_padmap:username',
            'padurl' => 'privacy:metadata:local_padmap:padurl',
            'timecreated' => 'privacy:metadata:local_padmap:timecreated',
            'timemodified' => 'privacy:metadata:local_padmap:timemodified',
        ], 'privacy:metadata:local_padmap');
        return $items;
    }

    public static function get_contexts_for_userid(int $userid): \core_privacy\local\request\contextlist
    {
        global $DB;
        $contextlist = new \core_privacy\local\request\contextlist();

        // Βρες username του user.
        $user = $DB->get_record('user', ['id' => $userid], 'id,username', IGNORE_MISSING);
        if (!$user) {
            return $contextlist;
        }

        if ($DB->record_exists('local_padmap', ['username' => $user->username])) {
            $contextlist->add_system_context();
        }
        return $contextlist;
    }

    public static function export_user_data(approved_contextlist $contextlist)
    {
        global $DB;
        if (!$contextlist->count()) {
            return;
        }
        $user = $DB->get_record('user', ['id' => $contextlist->get_user()->id], 'id,username', IGNORE_MISSING);
        if (!$user) {
            return;
        }
        $rec = $DB->get_record('local_padmap', ['username' => $user->username]);
        if ($rec) {
            $data = (object)[
                'username' => $rec->username,
                'padurl' => $rec->padurl,
                'timecreated' => \core_date::get_user_timezone_object()->format($rec->timecreated),
                'timemodified' => \core_date::get_user_timezone_object()->format($rec->timemodified),
            ];
            $context = \context_system::instance();
            writer::with_context($context)->export_data(
                [get_string('pluginname', 'local_padmap')],
                $data
            );
        }
    }

    public static function delete_data_for_all_users_in_context(\context $context)
    {
        global $DB;
        if ($context->contextlevel !== CONTEXT_SYSTEM) {
            return;
        }
        $DB->delete_records('local_padmap');
    }

    public static function delete_data_for_user(approved_contextlist $contextlist)
    {
        global $DB;
        $user = $DB->get_record('user', ['id' => $contextlist->get_user()->id], 'id,username', IGNORE_MISSING);
        if ($user) {
            $DB->delete_records('local_padmap', ['username' => $user->username]);
        }
    }
}
