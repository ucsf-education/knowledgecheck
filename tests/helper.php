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
 * Test helper code for the knowledgecheck question type.
 *
 * @package    qtype_knowledgecheck
 * @copyright  (c) The Regents of the University of California
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


use core_question\local\bank\question_version_status;

/**
 * Test helper class for the knowledgecheck question type.
 *
 * @package    qtype_knowledgecheck
 * @copyright  (c) The Regents of the University of California
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_knowledgecheck_test_helper extends question_test_helper
{
    /**
     * {@inheritDoc}
     * @return array A list of example question names.
     */
    public function get_test_questions(): array
    {
        return ['default'];
    }

    /**
     * Makes an essay question.
     *
     * @return qtype_knowledgecheck_question
     */
    public static function make_knowledgecheck_question_default(): qtype_knowledgecheck_question
    {
        question_bank::load_question_definition_classes('knowledgecheck');
        $q = new qtype_knowledgecheck_question();
        test_question_maker::initialise_a_question($q);
        $q->name = 'Knowledgecheck test question';
        $q->questiontext = 'Describe what you see outside your window.';
        $q->generalfeedback = 'Thanks for answering this question.';
        $q->responsetemplate = 'I see X, Y, and Z.';
        $q->qtype = question_bank::get_qtype('knowledgecheck');
        $q->answers = [
            1 => new question_answer(1, '', 1.0, 'Any answer is fine.', FORMAT_HTML),
        ];
        return $q;
    }

    /**
     * Get the question data, as it would be loaded by <code>get_question_options()</code>,
     * for the question returned by {@see make_knowledgecheck_question_default()}.
     * @return object
     */
    public static function get_knowledgecheck_question_data_default(): object
    {
        global $USER;

        $qdata = new stdClass();
        $qdata->id = 0;
        $qdata->contextid = 0;
        $qdata->category = 0;
        $qdata->parent = 0;
        $qdata->stamp = make_unique_id_code();
        $qdata->timecreated = time();
        $qdata->timemodified = time();
        $qdata->createdby = $USER->id;
        $qdata->modifiedby = $USER->id;
        $qdata->qtype = 'knowledgecheck';
        $qdata->name = 'Knowledgecheck test question';
        $qdata->questiontext = 'Describe what you see outside your window.';
        $qdata->questiontextformat = FORMAT_HTML;
        $qdata->generalfeedback = 'Thanks for answering this question.';
        $qdata->generalfeedbackformat = FORMAT_HTML;
        $qdata->status = question_version_status::QUESTION_STATUS_READY;
        $qdata->options = new stdClass();
        $qdata->options->responsetemplate = 'I see X, Y, and Z.';
        $qdata->options->answers = [
            1 => new question_answer(1, '', 1.0, 'Any answer is fine.', FORMAT_HTML),
        ];
        return $qdata;
    }


    /**
     * Make the data what would be received from the editing form for a knowledgecheck question.
     *
     * @return object
     */
    public static function get_knowledgecheck_question_form_data_default(): object
    {
        $fromform = new stdClass();
        $fromform->name = 'Knowledgecheck';
        $fromform->questiontext = [
            'text' => 'Describe what you see outside your window.',
            'format' => FORMAT_HTML,
        ];
        $fromform->defaultmark = 1.0;
        $fromform->generalfeedback = ['text' => 'Thanks for answering this question.', 'format' => FORMAT_HTML];
        $fromform->responsetemplate = ['text' => 'I see X, Y, and Z.', 'format' => FORMAT_HTML];
        $fromform->answer = [''];
        $fromform->fraction = ['1.0'];
        $fromform->feedback = [
            ['text' => 'Any answer is fine.', 'format' => FORMAT_HTML],
        ];
        $fromform->status = question_version_status::QUESTION_STATUS_READY;
        return $fromform;
    }
}
