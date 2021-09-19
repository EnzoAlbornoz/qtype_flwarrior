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
class qtype_flwarrior_edit_form extends question_edit_form {

    /**
     * @param MoodleQuickForm $mform
     * @throws coding_exception
     */
    protected function definition_inner($mform) {
        global $COURSE, $CFG, $DB, $PAGE;
        $CFG->cachejs = false;
        /* Remove Unused Form Data*/
        /* Add Needed Fields to Process a Machine */
        $mform->addElement('header', 'machine-tests', get_string(
            'questionadd_machinetests', 'qtype_flwarrior'
        ));
        /* Add Test Inputs */
//        $mform->addElement('tags', 'machine-match-true-list', 'Should Match');
        $machinetestgrp[] =& $mform->createElement('text', 'machine-test-word', '');
        $machinetestgrp[] =& $mform->createElement('checkbox', 'machine-test-should-match', null, 'Should Match ?');
//        $machinetestgrp[] =& $mform->createElement('tags', 'machine-test-allowed', 'Tests');
        $mform->addGroup($machinetestgrp, 'machine-test', 'Test 1');
        $mform->addElement('button', 'machine-test-add-test', 'Add New Test');
        $mform->addRule("machine-test", "required", );
        // Define Listeners
        $PAGE->requires->js_call_amd('qtype_flwarrior/qtype_flwarrior', 'setup_edit_tests_button');
    }


    protected function data_preprocessing($question) {
        $question = parent::data_preprocessing($question);
        $question = $this->data_preprocessing_hints($question);
        return $question;
    }

    public function qtype() {
        return 'flwarrior';
    }
}
