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

//    public function response_file_areas() {
//        return array('machine');
//    }

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


    public function summarise_response(array $response) {
        // TODO.
        error_log("[summarise_response]", 3, '/var/log/php.log');
        return null;
    }

    public function is_complete_response(array $response) {
        error_log("Teste!!!!".strlen($response['answer'] || "")."\n", 3, '/var/log/php.log');
        return $response['answer'] != null && strcmp($response['answer'],"");
    }

    public function validate_response(array $response) {

    }

    public function get_validation_error(array $response) {
        // TODO.
        return '';
    }

    public function is_same_response(array $prevresponse, array $newresponse) {
        error_log("[is_same_response]:\n", 3, '/var/log/php.log');
        return !strcmp($prevresponse['answer'], $newresponse['answer']);
    }

    public function get_correct_response() {
        // TODO.
        return array();
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

    public function grade_response(array $response) {
        // TODO: INSERT HERE THE MACHINE EXECUTION!!!!.
        $fraction = 0;
        return array($fraction, question_state::graded_state_for_fraction($fraction));
    }

    // Return a map from filename to file contents for all the attached files
    // in the given response.
    private function get_attached_files($response) {
        $attachments = array();
        if (array_key_exists('machine', $response) && $response['machine']) {
            $files = $response['machine']->get_files();
            foreach ($files as $file) {
                $attachments[$file->get_filename()] = $file->get_content();
            }
        }
        return $attachments;
    }

    public function compute_final_grade($responses, $totaltries) {
        // TODO.
        return 0;
    }
}
