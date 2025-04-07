@qtype @qtype_knowledgecheck @qtype_knowledgecheck_import
Feature: Test importing Knowledge Check questions
  As a teacher
  In order to reuse Knowledge Check questions
  I need to import them

  Background:
    Given the following "users" exist:
      | username |
      | teacher  |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | teacher | C1     | editingteacher |

  @javascript @_file_upload
  Scenario: import question.
    When I am on the "Course 1" "core_question > course question import" page logged in as teacher
    And I set the field "id_format_xml" to "1"
    And I upload "question/type/knowledgecheck/tests/fixtures/testquestion.moodle.xml" file to "Import" filemanager
    And I press "id_submitbutton"
    Then I should see "Parsing questions from import file."
    And I should see "Importing 1 questions from file"
    And I should see "Describe what you see outside your window."
    And I press "Continue"
    Then I should see "knowledgecheck-001"
    When I choose "Edit question" action for "knowledgecheck-001" in the question bank
    Then the following fields match these values:
      | Question name     | knowledgecheck-001                         |
      | Question text     | Describe what you see outside your window. |
      | General feedback  | Thanks for answering this question.        |
      | Default mark      | 1                                          |
      | Response template | I see X, Y, and Z.                         |
      | id_feedback_0     | Any answer is fine.                        |
