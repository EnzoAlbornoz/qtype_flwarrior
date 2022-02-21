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
        error_log("[formulation_and_controls]:\n".print_r($question->machine_tests,true), 3, "/var/log/php.log");

        /* Fetch Question Text */
        $question_text = $question->format_questiontext($qa);
        /* Create Elements */
        $file_input = $this->files_input($qa, 1, $options);
        $result = <<<HTML
            <div id="question-header" class="qtext">
                <span>{$question_text}</span>
            </div>
            <div id="question-form" class="ablock">
                <div class="attachments">
                    {$file_input}
                </div>
            </div>
        HTML;
        // Add JS
        $PAGE->requires->js_call_amd("qtype_flwarrior/quiz_renderer", 'init');

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

    // From qtype_essay
    public function files_input(
        question_attempt $qa,
        int $num_allowed,
        question_display_options $options
    ): string {
        $filetypeslist = ".jff";
        global $CFG, $COURSE;
        require_once($CFG->dirroot . '/lib/form/filemanager.php');

        $pickeroptions = new stdClass();
        $pickeroptions->mainfile = null;
        $pickeroptions->maxfiles = $num_allowed;
        $pickeroptions->itemid = $qa->prepare_response_files_draft_itemid(
            'machine', $options->context->id);
        $pickeroptions->context = $options->context;
        $pickeroptions->return_types = FILE_INTERNAL | FILE_CONTROLLED_LINK;

        $pickeroptions->itemid = $qa->prepare_response_files_draft_itemid(
            'machine', $options->context->id);
        $pickeroptions->accepted_types = $filetypeslist;

        $fm = new form_filemanager($pickeroptions);
        $fm->options->maxbytes = get_user_max_upload_file_size(
            $this->page->context,
            $CFG->maxbytes,
            $COURSE->maxbytes,
            $qa->get_question()->maxbytes
        );
        $filesrenderer = $this->page->get_renderer('core', 'files');

        $text = '';
        if (!empty($filetypeslist)) {
            $text = html_writer::tag(
                'p',
                get_string('accepted_file_types', 'qtype_flwarrior')
            );
            $filetypesutil = new \core_form\filetypes_util();
            $filetypes = $filetypeslist;
            $filetypedescriptions = $filetypesutil->describe_file_types($filetypes);
            $text .= $this->render_from_template('core_form/filetypes-descriptions', $filetypedescriptions);
        }

        $output = html_writer::start_tag('fieldset');
        $output .= html_writer::tag(
            'legend',
            get_string('answer_files', 'qtype_flwarrior'),
            ['class' => 'sr-only']
        );
        $output .= $filesrenderer->render($fm);
        $output .= html_writer::empty_tag('input', [
            'type' => 'hidden',
            'name' => $qa->get_qt_field_name('machine'),
            'value' => $pickeroptions->itemid,
        ]);
        $output .= $text;
        $output .= html_writer::end_tag('fieldset');

        return $output;
    }
}
