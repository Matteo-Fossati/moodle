<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Plugin version and other meta-data are defined here.
 *
 * @package   local_greetings
 * @copyright 2025 Matteo Fossati <matteofossati@socialthingum.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->dirroot.'/local/greetings/lib.php');

$context = context_system::instance();
$PAGE->set_context($context);

$PAGE->set_url(new moodle_url('/local/greetings/index.php'));

$PAGE->set_pagelayout('standard');

$PAGE->set_title(get_string('pluginname', 'local_greetings'));
$PAGE->set_heading(get_string('pluginname', 'local_greetings'));

require_login();
if (isguestuser()) {
    throw new moodle_exception('noguest');
}

$allowpost = has_capability('local/greetings:postmessages', $context);// Controlla se un utente ha la capability di fare un post e mette il risultato in una var.
$deleteanypost = has_capability('local/greetings:deleteanymessage', $context);
$deletepost = has_capability('local/greetings:deleteownmessage', $context);
$allowview = has_capability('local/greetings:viewmessages', $context);

$messageform = new \local_greetings\form\message_form();

$action = optional_param('action', '', PARAM_TEXT);

if ($action == 'del') {

    require_sesskey();// Metto una protezione contro il CSRF attack.
    $id = required_param('id', PARAM_INT);
    if ($deleteanypost || $deletepost) {
        $params = ['id' => $id];

        // Gli utenti senza permessi possono eliminare solo i loro messaggi.
        if (!$deleteanypost) {
            $params += ['userid' => $USER->id];
        }
        $DB->delete_records('local_greetings_messages', $params);
        redirect($PAGE->url);
    }
}
echo $OUTPUT->header();

$usergreeting = local_greetings_get_greeting($USER);




$templatedata = ['usergreeting' => $usergreeting];
echo $OUTPUT->render_from_template(
    'local_greetings/greeting_message',
    $templatedata
);

if ($allowpost) {
    $messageform->display();
}
if ($allowview) {
    $userfields = \core_user\fields::for_name()->with_identity($context);
    $userfieldssql = $userfields->get_sql('u');

    $sql = "SELECT m.id, m.message, m.timecreated, m.userid {$userfieldssql->selects}
            FROM {local_greetings_messages} m
        LEFT JOIN {user} u ON u.id = m.userid
        ORDER BY timecreated DESC";


    $messages = $DB->get_records_sql($sql);

    foreach ($messages as $m) {
        // L'utente puo eliminare questo post?
        // Attacco il flag del perche non si puo fare nel mustache.
        $m->candelete = ($deleteanypost || ($deletepost && $m->userid == $USER->id));
    }

    $cardbackgroundcolor = get_config('local_greetings', 'messagecardbgcolor');
    $templatedata = [
        'messages' => array_values($messages),
        'cardbackgroundcolor' => $cardbackgroundcolor,
    ];
    echo $OUTPUT->render_from_template('local_greetings/messages', $templatedata);
}


if ($data = $messageform->get_data()) {
    require_capability('local/greetings:postmessages', $context);
    $message = required_param('message', PARAM_TEXT);

    if (!empty($message)) {
        $record = new stdClass;
        $record->message = $message;
        $record->timecreated = time();
        $record->userid = $USER->id;


        $DB->insert_record('local_greetings_messages', $record);

        redirect($PAGE->url);// Ricarico la pagina per avere il form vuoto.
    }
}
echo $OUTPUT->footer();

