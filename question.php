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
require_once($CFG->dirroot . '/question/type/flwarrior/lib/interop/JFFParser.php');
require_once($CFG->dirroot . '/question/type/flwarrior/lib/fl_machine.php');

/**
 * Represents a FL Warrior question.
 *
 * @copyright 2021 Enzo Coelho Albornoz <enzocoelhoalbornoz@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_flwarrior_question extends question_graded_automatically
{

    // Teacher Defined Variables ===============================================
    /* @type fl_machine_test[] Test List */
    public array $machine_tests;

    public function get_expected_data(): array
    {
        return array('machine' => question_attempt::PARAM_FILES);
    }

    public function apply_attempt_state(question_attempt_step $step)
    {
    }

    /**
     * @throws coding_exception
     */
    public function summarise_response(array $response)
    {
        if (isset($response['machine']) && $response['machine']) {
            $machine_files = $response['machine']->get_files();
            if (array_key_exists(0, $machine_files)) {
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

    public function is_complete_response(array $response): bool
    {
        error_log("Teste!!!!" . print_r($response, true) . "\n", 3, '/var/log/php.log');
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

    public function validate_response(array $response)
    {

    }

    public function get_validation_error(array $response)
    {
        return '';
    }

    public function is_same_response(array $prevresponse, array $newresponse)
    {
        /** @var question_response_files $prev_response_machine */
        $prev_response_machine = $prevresponse['machine'];
        /** @var question_response_files $new_response_machine */
        $new_response_machine = $newresponse['machine'];

        if ($prev_response_machine == null && $new_response_machine == null) {
            return true;
        }

        if (
            ($prev_response_machine == null && $new_response_machine != null) ||
            ($prev_response_machine != null && $new_response_machine == null)
        ) {
            return false;
        }

        /** @var stored_file[] $prev_files */
        $prev_files = array_values($prev_response_machine->get_files());
        /** @var stored_file[] $new_files */
        $new_files = array_values($new_response_machine->get_files());

        if (count($prev_files) != count($new_files)) {
            return false;
        }

        // Check Content Hash
        for ($idx = 0; $idx < count($prev_files); $idx++) {
            if ($prev_files[$idx]->get_contenthash() != $new_files[$idx]->get_contenthash()) {
                return false;
            }
        }

        return true;
    }

    public function get_correct_response()
    {
        return null;
    }

    public function check_file_access(
        $qa,
        $options,
        $component,
        $filearea,
        $args,
        $forcedownload
    )
    {
        if ($component == 'question' && $filearea == 'hint') {
            return $this->check_hint_file_access($qa, $options, $args);

        } else {
            return parent::check_file_access($qa, $options, $component, $filearea,
                $args, $forcedownload);
        }
    }

    public function is_gradable_response(array $response): bool
    {
        return (
            array_key_exists('machine', $response) &&
            $response['machine'] instanceof question_response_files
        );
    }

    public function grade_response(array $response): array
    {
        // Get Machine file
        error_log("[grade_response]", 3, '/var/log/php.log');
        /** @var stored_file[] $machine_files */
        $machine_files = array_values($response['machine']->get_files());
        // Get Machine Content
        $machine_file_content = $machine_files[0]->get_content();
        // Parse Machine
        error_log("[machine_file_content]\n".$machine_file_content, 3, '/var/log/php.log');
        $machine = JFFParser::parse_string($machine_file_content);
        // If there is any error, just grade as 0
        if ($machine == null) {
            return array(0, question_state::graded_state_for_fraction(0));
        }
        // Compute fraction for each test
        error_log("[machine]".print_r($machine, true), 3, '/var/log/php.log');
        $test_fraction_bonus = 1 / count($this->machine_tests);
        // Define initial fraction
        $fraction = 0;
        // Execute tests
        foreach ($this->machine_tests as $raw_test) {
            //Parse Test
            error_log("[$raw_test]".print_r($raw_test, true), 3, '/var/log/php.log');
            $test = fl_machine_test::from_array($raw_test);
            $matches = $machine->matches($test);
            if ($matches) {
                $fraction += $test_fraction_bonus;
            }
        }
        // Return Computed Fraction
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
