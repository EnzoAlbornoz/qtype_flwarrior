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
 * Defines the editing form for the flwarrior question type.
 *
 * @package   qtype_flwarrior
 * @copyright 2021 Enzo Coelho Albornoz <enzocoelhoalbornoz@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * flwarrior question editing form definition.
 *
 * ?Note: This file will define the form when the teacher is creating a question
 *
 * @copyright 2021 Enzo Coelho Albornoz <enzocoelhoalbornoz@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_flwarrior_edit_form extends question_edit_form
{

    const NUM_TESTCASES_START = 5;  // Num empty test cases with new questions.
    const NUM_TESTCASES_ADD = 1;    // Extra empty test cases to add.

    public function qtype(): string
    {
        return 'flwarrior';
    }

    /**
     * @param MoodleQuickForm $mform
     * @throws coding_exception
     */
    protected function definition_inner($mform)
    {
        global $COURSE, $CFG, $DB, $PAGE;
        /* Add Machine Tests Section */
        $mform->addElement('header', 'machine-tests', get_string(
            'question_heading_machine_tests', 'qtype_flwarrior'
        ));

        /* @type qtype_flwarrior_question $question Repeat for each test */
        $question = $this->question;
        /* Define Repeating Groups*/
        $tests_in_question = count($question->machine_tests);
        $repeating_num = max($tests_in_question, 1);
        $repeating_groups = array();
        $repeated_options = array();

        $PAGE->requires->js_call_amd('qtype_flwarrior/debug', 'log', array($tests_in_question, ($repeating_num)));

        /* Define what going to be grouped in repeated elements */
        $repeat_array = array();
        /* @type HTML_QuickForm_text $test_word */
        $test_word = $mform->createElement('text', 'machine-test-word-{no}', '');
        /* @type HTML_QuickForm_checkbox $test_should_match */
        $test_should_match = $mform->createElement(
            'advcheckbox',
            'machine-test-should-match-{no}',
            null,
            'Should Match ?',
            null,
            array(0, 1)
        );
        $test_should_match->setValue(true);
        /* @type HTML_QuickForm_hidden $test_id */
        $test_id = $mform->createElement('hidden', 'machine-test-id-{no}');

        /* Add them to group */
        $repeat_array[] = $test_word;
        $repeat_array[] = $test_should_match;
        $repeat_array[] = $test_id;

        /* Add elements to group*/
        /* @type HTML_QuickForm_group $repeatGroup */
        $repeatGroup = $mform->createElement('group', 'machine-test-{no}', 'Test {no}');
        $repeatGroup->setElements($repeat_array);

        /* Define Group Options*/
        $repeated_options['machine-test-{no}[machine-test-word-{no}]']['type'] = PARAM_TEXT;
        $repeated_options['machine-test-{no}[machine-test-id-{no}]']['type'] = PARAM_INT;
        $repeated_options['machine-test-{no}[machine-test-should-match-{no}]']['type'] = PARAM_BOOL;
        /* Add group to form */
        $repeating_groups[] = $repeatGroup;

        /* Repeat elements */
        $this->repeat_elements(
            $repeating_groups,
            $repeating_num,
            $repeated_options,
            'option_repeats',
            'option_add_fields',
            1,
            'Add test',
            true
        );

        /* For already existing tests, fill the values */
        for ($idx = 0; $idx < $tests_in_question; $idx++) {
            /* Transform array of elements into an object with the elements as values with their name as key */
            $group_elements = flu_object_from_entries(
                array_map(
                    function ($el) {
                        return array($el->getName(), $el);
                    },
                    $mform->getElement('machine-test-{no}[' . $idx . ']')->getElements()
                )
            );
            /* Update values */
            $group_elements->{'machine-test-word-{no}'}->setValue($question->machine_tests[$idx]->word);
            $group_elements->{'machine-test-should-match-{no}'}->setValue($question->machine_tests[$idx]->should_match);
            $group_elements->{'machine-test-id-{no}'}->setValue($question->machine_tests[$idx]->id);
        }

    }

    protected function data_preprocessing($question)
    {
        global $PAGE;
        $question = parent::data_preprocessing($question);
        $question = $this->data_preprocessing_hints($question);


        $PAGE->requires->js_call_amd('qtype_flwarrior/debug', 'log', array($question));

        return $question;
    }
}
