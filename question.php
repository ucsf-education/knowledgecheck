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
 * Knowledge check question definition class.
 *
 * @package    qtype_knowledgecheck
 * @copyright  (c) The Regents of the University of California
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Represents a knowledge check question.
 *
 * @package    qtype_knowledgecheck
 * @copyright  (c) The Regents of the University of California
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_knowledgecheck_question extends question_graded_by_strategy
        implements question_response_answer_comparer {
    /**
     * @var string
     */
    public $responsetemplate = null;

    /**
     * @var int
     */
    public $responsefieldlines = 15;

    /**
     * @var question_answer[]
     */
    public $answers = [];

    /**
     * {@inheritdoc}
     */
    public function __construct() {
        parent::__construct(new question_first_matching_answer_grading_strategy($this));
    }

    /**
     * {@inheritdoc}
     *
     * @return array A structure defining what data is expected in the response to this question.
     */
    public function get_expected_data() {
        return ['answer' => PARAM_RAW_TRIMMED];
    }

    /**
     * {@inheritdoc}
     *
     * @param array $response A given response.
     * @return string|null A plain text summary of that response, that could be used in reports.
     */
    public function summarise_response(array $response) {
        if (isset($response['answer'])) {
            return $response['answer'];
        } else {
            return null;
        }
    }

    /**
     * {@inheritdoc}
     *
     * @param array $response A list of responses.
     * @return bool whether this response is a complete answer to this question.
     */
    public function is_complete_response(array $response) {
        return array_key_exists('answer', $response) &&
        ($response['answer'] || $response['answer'] === '0');
    }

    /**
     * {@inheritdoc}
     *
     * @param array $response The given response
     * @return string the validation error message.
     */
    public function get_validation_error(array $response) {
        return get_string('pleaseenterananswer', 'qtype_knowledgecheck');
    }

    /**
     * {@inheritdoc}
     *
     * @param array $prevresponse the responses previously recorded for this question.
     * @param array $newresponse the new responses, in the same format.
     * @return bool whether the two sets of responses are the same - that is
     *      whether the new set of responses can safely be discarded.
     */
    public function is_same_response(array $prevresponse, array $newresponse) {
        return question_utils::arrays_same_at_key_missing_is_blank(
                $prevresponse, $newresponse, 'answer');
    }

    /**
     * Returns a list of possible answers to this question.
     *
     * @return array A list of possible answers to this question.
     */
    public function get_answers() {
        return $this->answers;
    }

    /**
     * Compares the given response with a given possible answer.
     *
     * @param array $response the response.
     * @param question_answer $answer an answer.
     * @return bool whether the response matches the answer.
     */
    public function compare_response_with_answer(array $response, question_answer $answer) {
        if (!array_key_exists('answer', $response) || is_null($response['answer'])) {
            return false;
        }

        // Whatever the answer is - it's always correct.
        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @param question_attempt $qa the question attempt being displayed.
     * @param question_display_options $options the options that control display of the question.
     * @param string $component the name of the component we are serving files for.
     * @param string $filearea the name of the file area.
     * @param array $args the remaining bits of the file path.
     * @param bool $forcedownload whether the user must be forced to download the file.
     * @return bool true if the user can access this file.
     */
    public function check_file_access($qa, $options, $component, $filearea,
        $args, $forcedownload) {
        if ($component == 'question' && $filearea == 'answerfeedback') {
            $currentanswer = $qa->get_last_qt_var('answer');
            $answer = $this->get_matching_answer(['answer' => $currentanswer]);
            $answerid = reset($args); // Itemid is answer id.
            return $options->feedback && $answer && $answerid == $answer->id;

        } else if ($component == 'question' && $filearea == 'hint') {
            return $this->check_hint_file_access($qa, $options, $args);

        } else {
            return parent::check_file_access($qa, $options, $component, $filearea,
                $args, $forcedownload);
        }
    }
}
