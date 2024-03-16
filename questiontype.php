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
 * Question type class for the knowledge check question type.
 *
 * @package    qtype_knowledgecheck
 * @copyright  (c) The Regents of the University of California
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/questionlib.php');
require_once($CFG->dirroot . '/question/engine/lib.php');
require_once($CFG->dirroot . '/question/type/knowledgecheck/question.php');


/**
 * The knowledge check question type.
 *
 * @package    qtype_knowledgecheck
 * @copyright  (c) The Regents of the University of California
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_knowledgecheck extends question_type {

    /**
     * {@inheritdoc}
     *
     * @param int $questionid the question being moved.
     * @param int $oldcontextid the context it is moving from.
     * @param int $newcontextid the context it is moving to.
     */
    public function move_files($questionid, $oldcontextid, $newcontextid) {
        parent::move_files($questionid, $oldcontextid, $newcontextid);
        $this->move_files_in_hints($questionid, $oldcontextid, $newcontextid);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $questionid the question being deleted.
     * @param int $contextid the context the question is in.
     */
    protected function delete_files($questionid, $contextid) {
        parent::delete_files($questionid, $contextid);
        $this->delete_files_in_hints($questionid, $contextid);
    }

    /**
     * {@inheritdoc}
     *
     * @param object $question  This holds the information from the editing form,
     *      it is not a standard question object.
     * @return object $result->error or $result->notice
     */
    public function save_question_options($question) {
        global $DB;
        $options = $DB->get_record('qtype_knowledgecheck_options', ['questionid' => $question->id]);
        if (!$options) {
            $options = new stdClass();
            $options->questionid = $question->id;
            $options->id = $DB->insert_record('qtype_knowledgecheck_options', $options);
        }

        $options->responsetemplate = $question->responsetemplate['text'];
        $DB->update_record('qtype_knowledgecheck_options', $options);
        $this->save_question_answers($question);
        $this->save_hints($question);
    }

    /**
     * {@inheritdoc}
     *
     * @param question_definition $question the question_definition we are creating.
     * @param object $questiondata the question data loaded from the database.
     */
    protected function initialise_question_instance(question_definition $question, $questiondata) {
        parent::initialise_question_instance($question, $questiondata);
        $this->initialise_question_answers($question, $questiondata);

    }

    /**
     * {@inheritdoc}
     *
     * @param object $questiondata the question definition data.
     * @return array keys are subquestionid, values are arrays of possible
     *      responses to that subquestion.
     */
    public function get_possible_responses($questiondata) {
        $responses = [];

        foreach ($questiondata->options->answers as $aid => $answer) {
            $responses[$aid] = new question_possible_response($answer->answer, $answer->fraction);
        }

        $responses[0] = new question_possible_response(get_string('didnotmatchanyanswer', 'question'), 0);
        $responses[null] = question_possible_response::no_response();

        return [$questiondata->id => $responses];
    }

    /**
     * {@inheritdoc}
     *
     * @return mixed array as above, or null to tell the base class to do nothing.
     */
    public function extra_question_fields() {
        return ['qtype_knowledgecheck_options', 'responsetemplate'];
    }

    /**
     * {@inheritdoc}
     *
     * @return bool override this to return false if this is not really a
     *      question type, for example the description question type is not
     *      really a question type.
     */
    public function is_real_question_type() {
        return false;
    }
}
