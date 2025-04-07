@qtype @qtype_knowledgecheck @qtype_knowledgecheck_backuprestore
Feature: Test duplicating a quiz containing a Knowledge Check question
  As a teacher
  In order re-use my courses containing Knowledge Check questions
  I need to be able to backup and restore them

  Background:
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype          | name               | template |
      | Test questions   | knowledgecheck | knowledgecheck-001 | default  |
    And the following "activities" exist:
      | activity | name      | course | idnumber |
      | quiz     | Test quiz | C1     | quiz1    |
    And quiz "Test quiz" contains the following questions:
      | knowledgecheck-001 | 1 |
    And the following config values are set as admin:
      | enableasyncbackup | 0 |

  @javascript
  Scenario: Backup and restore a course containing a Knowledge Check question
    When I am on the "Course 1" course page logged in as admin
    And I backup "Course 1" course using this options:
      | Confirmation | Filename | test_backup.mbz |
    And I restore "test_backup.mbz" backup into a new course using this options:
      | Schema | Course name       | Course 2 |
      | Schema | Course short name | C2       |
    And I am on the "Course 2" "core_question > course question bank" page
    And I choose "Edit question" action for "knowledgecheck-001" in the question bank
    Then the following fields match these values:
      | Question name     | knowledgecheck-001                         |
      | Question text     | Describe what you see outside your window. |
      | General feedback  | Thanks for answering this question.        |
      | Default mark      | 1                                          |
      | Response template | I see X, Y, and Z.                         |
      | id_feedback_0     | Any answer is fine.                        |
