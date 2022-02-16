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
 * flwarrior question renderer class.
 *
 * @package   qtype_flwarrior
 * @copyright 2021 Enzo Coelho Albornoz <enzocoelhoalbornoz@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Generates the output for flwarrior questions.
 *
 * @copyright 2021 Enzo Coelho Albornoz <enzocoelhoalbornoz@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_flwarrior_renderer extends qtype_renderer {

    public function formulation_and_controls(
            question_attempt $qa,
            question_display_options $options
    ) {
        global $PAGE;

        /* @var qtype_flwarrior_question Fetch Question Data */
        $question = $qa->get_question();
        $response = $qa->get_last_qt_var("answer");
        $input_name = $qa->get_qt_field_name('answer');
        error_log("[formulation_and_controls]:", print_r(strlen($response), true), 3, "/var/log/php.log");
        /* Fetch Question Text */
        $question_text = $question->format_questiontext($qa);
        /* Create Elements */
        $machine_loaded = $response == null || !strcmp($response, "") ? "Pending" : "Loaded!";
        $result = <<<HTML
            <div id="question-header" class="qtext">
                <span>{$question_text}</span>
            </div>
            <div id="question-form" class="ablock">
                <label for="machine">Enviar Máquina: </label>
                <input name="machine" type="file" accept=".jff" />
                <input type="hidden" name="{$input_name}" id="machine_serialized" required value="{$response}"/>
                <p>Status:</p>
                <p id="machine_log">{$machine_loaded}</p>
            </div>
        HTML;
        // Add JS
        $PAGE->requires->js_call_amd("qtype_flwarrior/quiz_renderer", 'init', array($response));

        return $result;
    }

    public function specific_feedback(question_attempt $qa) {
        // TODO.
        return '';
    }

    public function correct_response(question_attempt $qa) {
        // TODO.
        return '';
    }
}
