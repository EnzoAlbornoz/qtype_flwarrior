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
    public function formulation_and_controls(question_attempt $qa,
        question_display_options $options) {
        global $PAGE;

        /* @var qtype_flwarrior_question Fetch Question Data */
        $question = $qa->get_question();
        /* Fetch Question Text */
        $questiontext = $question->format_questiontext($qa);

        $PAGE->requires->js_call_amd('qtype_flwarrior/quiz_debug_data', 'init', array($question->testo));
        /* Create Elements */
        $result = <<<HTML
                <header id="question-header">
                    <span>{$questiontext}</span>
                    <span>{$question->testo}</span>
                    <span>End</span>
                </header>
                <div id="question-form">
                    <label for="machine">Enviar Máquina: </label>
                    <input name="machine" type="file" accept=".jff" />
                </div>
HTML;

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
