<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Fl Warrior question definition class.
 *
 * @package    qtype_flwarrior
 * @copyright 2021 Enzo Coelho Albornoz <enzocoelhoalbornoz@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/flwarrior/lib/fl_machine_test.php');

/**
 * Represents a FL Warrior question.
 *
 * @copyright 2021 Enzo Coelho Albornoz <enzocoelhoalbornoz@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_flwarrior_question extends question_graded_automatically {

    // Teacher Defined Variables ===============================================
    /* @type fl_machine_test[] Test List*/
    public array $machine_tests;
    // Student Defined Variables ===============================================
    public array $attempt_machine;

    public function start_atempt() {
        global $DB;
    }

    public function get_expected_data(): array
    {
        return array('machine' => question_attempt::PARAM_FILES);
    }

    public function apply_attempt_state(question_attempt_step $step) {
        global $DB;
        error_log("[apply_attempt_state]:\n".print_r($step->get_all_data(), true), 3, '/var/log/php.log');
//        error_log("[apply_attempt_state2]:\n".print_r($step->get_id(), true), 3, '/var/log/php.log');
//        $step->get_qt_var()
//        $DB->get_record('')
    }


    /**
     * @throws coding_exception
     */
    public function summarise_response(array $response) {
        error_log("[summarise_response]", 3, '/var/log/php.log');
        if (isset($response['machine'])  && $response['machine']) {
            $machine_files = $response['machine']->get_files();
            if (array_key_exists(0,$machine_files)) {
                $file = $machine_files[0];
                return get_string(
                    'attached_files',
                    'qtype_flwarrior',
                    implode(
                        ', ',
                        $file->get_filename() . ' (' . display_size($file->get_filesize()) . ')'
                    )
                );
            }
        }
        return '';
    }

    public function is_complete_response(array $response): bool {
        error_log("Teste!!!!".print_r($response, true)."\n", 3, '/var/log/php.log');
        // Check the filetypes.
        /** @type question_file_saver $machine_field */
        $machine_field = $response['machine'];
        $filetypes_util = new \core_form\filetypes_util();
        $allow_list = $filetypes_util->normalize_file_types(".jff");
        // Check Machine is set
        if (!(isset($machine_field) && $machine_field)) {
            error_log("No machine\n", 3, '/var/log/php.log');
            return false;
        }
        // Check Machine has at least 1 machine
        $machine_files = array_values($machine_field->get_files());

        if (!array_key_exists(0, $machine_files)) {
            error_log("No files\n", 3, '/var/log/php.log');
            return false;
        }
        // Check Machine file type
        $file = $machine_files[0];

        return $filetypes_util->is_allowed_file_type($file->get_filename(), $allow_list);
    }

    public function validate_response(array $response) {

    }

    public function get_validation_error(array $response) {
        // TODO.
        return '';
    }

    public function is_same_response(array $prevresponse, array $newresponse) {
        error_log("[is_same_response]:\n", 3, '/var/log/php.log');

        // TODO: Implement Function
        return false;
    }

    public function get_correct_response() {
        return null;
    }

    public function check_file_access(
            $qa,
            $options,
            $component,
            $filearea,
            $args,
            $forcedownload
    ) {
        // TODO.
        if ($component == 'question' && $filearea == 'hint') {
            return $this->check_hint_file_access($qa, $options, $args);

        } else {
            return parent::check_file_access($qa, $options, $component, $filearea,
                    $args, $forcedownload);
        }
    }

    public function is_gradable_response(array $response): bool {
        return (
            array_key_exists('machine', $response) &&
            $response['machine'] instanceof question_response_files
        );
    }

    public function grade_response(array $response): array {
        $machine_file = $response['machine'];
        $fraction = 0;
        return array($fraction, question_state::graded_state_for_fraction($fraction));
    }

    // Return a map from filename to file contents for all the attached files
    // in the given response.
    private function get_attached_files($response): array
    {
        $attachments = array();
        if (array_key_exists('machine', $response) && $response['machine']) {
            $files = $response['machine']->get_files();
            foreach ($files as $file) {
                $attachments[$file->get_filename()] = $file->get_content();
            }
        }
        return $attachments;
    }
}
