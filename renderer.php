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
 * Knowledge check question renderer class.
 *
 * @package    qtype_knowledgecheck
 * @copyright  (c) The Regents of the University of California
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/knowledgecheck/renderer_format_editor.php');

/**
 * Generates the output for knowledge check questions.
 *
 * @package    qtype_knowledgecheck
 * @copyright  (c) The Regents of the University of California
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_knowledgecheck_renderer extends qtype_renderer
{
    /**
     * {@inheritdoc}
     *
     * @param question_attempt $qa the question attempt to display.
     * @param question_display_options $options controls what should and should not be displayed.
     * @return string HTML fragment.
     */
    public function formulation_and_controls(
        question_attempt $qa,
        question_display_options $options
    ) {

        $question = $qa->get_question();

        $responseoutput = $this->page->get_renderer('qtype_knowledgecheck', 'format_editor');

        // Answer field.
        $step = $qa->get_last_step_with_qt_var('answer');

        if (!$step->has_qt_var('answer') && empty($options->readonly)) {
            // Question has never been answered, fill it with response template.
            $step = new question_attempt_step(['answer' => $question->responsetemplate]);
        }

        if (empty($options->readonly)) {
            $answer = $responseoutput->response_area_input(
                'answer',
                $qa,
                $step,
                $question->responsefieldlines,
                $options->context
            );
        } else {
            $answer = $responseoutput->response_area_read_only(
                'answer',
                $qa,
                $step,
                $question->responsefieldlines,
                $options->context
            );
        }

        $result = '';
        $result .= html_writer::tag(
            'div',
            $question->format_questiontext($qa),
            ['class' => 'qtext']
        );

        $result .= html_writer::start_tag('div', ['class' => 'ablock']);
        $result .= html_writer::tag('div', $answer, ['class' => 'answer']);
        $result .= html_writer::end_tag('div');

        if ($qa->get_state() == question_state::$invalid) {
            $result .= html_writer::nonempty_tag(
                'div',
                $question->get_validation_error(['answer' => $answer]),
                ['class' => 'validationerror']
            );
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     *
     * @param question_attempt $qa the question attempt to display.
     * @return string HTML fragment.
     */
    public function specific_feedback(question_attempt $qa) {
        $question = $qa->get_question();

        $answer = $question->get_matching_answer(['answer' => $qa->get_last_qt_var('answer')]);
        if (!$answer || !$answer->feedback) {
            return '';
        }

        return $question->format_text(
            $answer->feedback,
            $answer->feedbackformat,
            $qa,
            'question',
            'answerfeedback',
            $answer->id
        );
    }
}
