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
 * Question type class for the flwarrior question type.
 *
 * @package   qtype_flwarrior
 * @copyright 2021 Enzo Coelho Albornoz <enzocoelhoalbornoz@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->libdir . '/questionlib.php');
require_once($CFG->dirroot . '/question/engine/lib.php');
require_once($CFG->dirroot . '/question/type/flwarrior/question.php');
require_once($CFG->dirroot . '/question/type/flwarrior/lib/fl_machine_test.php');
require_once($CFG->dirroot . '/question/type/flwarrior/lib/utils.php');

/**
 * The flwarrior question type.
 *
 * @copyright 2021 Enzo Coelho Albornoz <enzocoelhoalbornoz@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_flwarrior extends question_type {

    public function response_file_areas() {
        return array('machine');
    }

    /**
     * @param qtype_flwarrior_question $question
     * @return void
     * @throws dml_exception
     */
    public function save_question_options($question) {
        global $DB;

        $this->save_hints($question);

        error_log("[save_question_options]\n", 3, "/var/log/php.log");

        // Save Tests in Database
        foreach ($question->{'machine-test-{no}'} as $test_form) {
            $id = array_key_exists('machine-test-id-{no}', $test_form)
                ? $test_form['machine-test-id-{no}']
                : null;
            $word = array_key_exists('machine-test-word-{no}', $test_form)
                ? $test_form['machine-test-word-{no}']
                : '';
            $should_match = boolval(
                array_key_exists('machine-test-should-match-{no}', $test_form)
                    ? $test_form['machine-test-should-match-{no}']
                    : 0
            );

            $test = new fl_machine_test($id, $word, $should_match, 1000, $question->id);

            if ($test->id != null && $DB->record_exists('qtype_flwarrior_tests', array('id' => $test->id))) {
                // Update
                $DB->update_record('qtype_flwarrior_tests', $test);
            } else {
                /** @var int $inserted_id Create new entry */
                $inserted_id = $DB->insert_record('qtype_flwarrior_tests', $test);
                $test->id = $inserted_id;
            }
        }

    }

    /**
     * @param qtype_flwarrior_question $question
     * @return bool
     */
    public function get_question_options($question)
    {
        global $CFG, $DB, $OUTPUT;
        try {
            if (parent::get_question_options($question)) {

                // Fetch and Parse Tests from DB (indexed by id)
                $tests = array_map(
                    function($db_test) { return fl_machine_test::from_db_entry($db_test); },
                    $DB->get_records('qtype_flwarrior_tests', array('question_id' => $question->id))
                );

                // Insert tests into question object
                $question->machine_tests = $tests ? [...$tests] : array();

                return true;
            }
        } catch (dml_exception $e) {
            error_log($e->getMessage() . ":" . $e->getTraceAsString());
        } // Just Ignore and Return
        return false;
    }

    protected function initialise_question_instance(question_definition $question, $questiondata) {
        // TODO.
        parent::initialise_question_instance($question, $questiondata);
    }

    /**
     * @throws dml_exception
     */
    public function delete_question($questionid, $contextid) {
        global $DB;

        $success = $DB->delete_records('qtype_flwarrior_tests', array('question_id' => $questionid));


        return $success && parent::delete_question();
    }

    public function get_random_guess_score($questiondata) {
        // TODO.
        return 0;
    }

    public function get_possible_responses($questiondata) {
        // TODO.
        return array();
    }

    public function move_files($questionid, $oldcontextid, $newcontextid) {
        parent::move_files($questionid, $oldcontextid, $newcontextid);
        $fs = get_file_storage();
        $fs->move_area_files_to_new_context($oldcontextid,
            $newcontextid, 'qtype_essay', 'graderinfo', $questionid);
    }

    protected function delete_files($questionid, $contextid) {
        parent::delete_files($questionid, $contextid);
        $fs = get_file_storage();
        $fs->delete_area_files($contextid, 'qtype_essay', 'graderinfo', $questionid);
    }
}
