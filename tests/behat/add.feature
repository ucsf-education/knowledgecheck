@qtype @qtype_knowledgecheck @qtype_knowledgecheck_add
Feature: Test creating a Knowledge Check question
  As a teacher
  In order to test my students
  I need to be able to create a Knowledge Check question

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

  @javascript
  Scenario: Create a Knowledge Check question
    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    And I add a "Knowledge Check" question filling the form with:
      | Question name     | knowledgecheck-001                         |
      | Question text     | Describe what you see outside your window. |
      | General feedback  | Thanks for answering this question.        |
      | Default mark      | 1                                          |
      | Response template | I see X, Y, and Z.                         |
      | id_feedback_0     | Any answer is fine.                        |
    Then I should see "knowledgecheck-001"
    When I choose "Edit question" action for "knowledgecheck-001" in the question bank
    Then the following fields match these values:
      | Question name     | knowledgecheck-001                         |
      | Question text     | Describe what you see outside your window. |
      | General feedback  | Thanks for answering this question.        |
      | Default mark      | 1                                          |
      | Response template | I see X, Y, and Z.                         |
      | id_feedback_0     | Any answer is fine.                        |
