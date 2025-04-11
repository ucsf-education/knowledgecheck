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

namespace qtype_knowledgecheck;

use advanced_testcase;
use qtype_knowledgecheck_question;
use question_answer;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/knowledgecheck/question.php');
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');

/**
 * Unit tests for the knowledgecheck question type class.
 *
 * @package    qtype_knowledgecheck
 * @copyright  (c) The Regents of the University of California
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers \qtype_knowledgecheck_question
 */
final class question_test extends advanced_testcase {

    /**
     * @var qtype_knowledgecheck_question The question object under test.
     */
    protected qtype_knowledgecheck_question $question;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void {
        parent::setUp();
        $this->question = new qtype_knowledgecheck_question();
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown(): void {
        parent::tearDown();
        unset($this->question);
    }

    /**
     * Checks the expected data structure to be returned in response to this question.
     */
    public function test_get_expected_data(): void {
        $this->assertEquals(
            ['answer' => PARAM_RAW_TRIMMED],
            $this->question->get_expected_data()
        );
    }

    /**
     * Checks the plain text summary returned to a given response to this question.
     *
     * @dataProvider summarize_response_provider
     * @param array $response The response test fixture.
     * @param mixed $expected The expected output of the method under test.
     */
    public function test_summarize_response(array $response, mixed $expected): void {
        $this->assertEquals($expected, $this->question->summarise_response($response));
    }

    /**
     * Data provider for {@see test_summarize_response()}.
     * @return array An array of arrays, containing response test fixtures and expected summaries.
     */
    public static function summarize_response_provider(): array {
        return [
            [['answer' => 'lorem ipsum'], 'lorem ipsum'],
            [['answer' => ''], ''],
            [['answer' => '0'], '0'],
            [[], null],
        ];
    }

    /**
     * Checks if a given response to this question is complete.
     *
     * @dataProvider summarize_is_complete_response_provider
     * @param array $response The response test fixture.
     * @param bool $expected TRUE if the given response is expected to be complete, FALSE otherwise.
     */
    public function test_is_complete_response(array $response, bool $expected): void {
        $this->assertEquals($expected, $this->question->is_complete_response($response));
    }

    /**
     * Data provider for {@see test_is_complete_response()}.
     * @return array An array of arrays, containing response test fixtures and expected completion status.
     */
    public static function summarize_is_complete_response_provider(): array {
        return [
            [['answer' => 'lorem ipsum'], true],
            [['answer' => '0'], true],
            [['answer' => '    '], true],
            [['answer' => ''], false],
            [['answer' => null], false],
            [[], false],
        ];
    }

    /**
     * Checks the returned validation error on a given response to the question.
     *
     * @dataProvider get_validation_error_provider
     * @param array $response The response test fixture.
     */
    public function test_get_validation_error(array $response): void {
        // Regardless of what the given response is, the validation error is emitted is always the same.
        // This is OK, b/c in order for this method to be invoked,
        // the question state has already been determined to be invalid.
        // In other words, this method is never called on a "good" answer.
        $this->assertEquals('Please enter an answer.', $this->question->get_validation_error($response));
    }

    /**
     * Data provider for {@see test_get_validation_error()}.
     * @return array An array of arrays, containing response test fixtures.
     */
    public static function get_validation_error_provider(): array {
        return [
            [['answer' => 'lorem ipsum']],
            [['answer' => '0']],
            [['answer' => '    ']],
            [['answer' => '']],
            [['answer' => null]],
            [[]],
        ];
    }

    /**
     * Tests sameness comparison of previous and new responses to this question.
     * @dataProvider is_same_response_provider
     * @param array $prevresponse The given previous repsonse to the question.
     * @param array $newresponse The given new response to the question.
     * @param bool $expected TRUE if both responses are expected to contain the same answer, FALSE othewise.
     * @return void
     */
    public function test_is_same_response(array $prevresponse, array $newresponse, bool $expected): void {
        $this->assertEquals($expected, $this->question->is_same_response($prevresponse, $newresponse));
    }

    /**
     * Data provider for {@see test_is_same_response()}.
     * @return array An array of arrays, containing response test fixtures and expected comparison value.
     */
    public static function is_same_response_provider(): array {
        return [
            [[], [], true],
            [['answer' => ''], ['answer' => ''], true],
            [[], ['answer' => ''], true],
            [['answer' => ''], ['answer' => '0'], false],
            [[], ['answer' => '    '], false],
            [[], ['answer' => 'foo'], false],
            [['answer' => 'foo'], [], false],
            [['answer' => 'foo'], ['answer' => 'bar'], false],
        ];
    }

    /**
     * Tests the answers getter on this question.
     */
    public function test_get_answers(): void {
        $this->assertEquals([], $this->question->get_answers());
        $answers = ['whatever', 'is', 'fine', 'here'];
        $this->question->answers = $answers;
        $this->assertEquals($answers, $this->question->get_answers());
    }

    /**
     * Tests response/answer comparison.
     *
     * @dataProvider compare_response_with_answer_provider
     * @param array $response The given response to this question.
     * @param question_answer $answer The given answer of this question.
     * @param bool $expected TRUE if the given response "matches" the given answer, FALSE otherwise.
     * @return void
     */
    public function test_compare_response_with_answer(array $response, question_answer $answer, $expected): void {
        $this->assertEquals($expected, $this->question->compare_response_with_answer($response, $answer));
    }

    /**
     * Data provider for {@see test_compare_response_with_answer()}.
     * @return array An array of arrays, each containing a response, and answer, and their expected comparison value.
     */
    public static function compare_response_with_answer_provider(): array {
        // The answer doesn't really matter here, the response is never compared to it.
        // As long as the response contains a non-NULL answer, even if it's blank,
        // the expected outcome of the response/answer comparison is TRUE.
        $answer = new question_answer(1, '', 1.0, '', FORMAT_PLAIN);
        return [
            [['answer' => 'sure thing'], $answer, true],
            [['answer' => '', $answer], $answer, true],
            [['answer' => '   '], $answer, true],
            [['answer' => null], $answer, false],
            [[], $answer, false],
        ];
    }

    /**
     * Checks file access for question attempts for this question.
     * Todo: Implement this test [ST 2025/04/10].
     */
    public function test_check_file_access(): void {
        $this->markTestIncomplete('To be implemented.');
    }
}
