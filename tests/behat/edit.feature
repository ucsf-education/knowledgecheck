@qtype @qtype_knowledgecheck @qtype_knowledgecheck_edit
Feature: Test editing a Knowledge Check question
  As a teacher
  In order to be able to update my Knowledge Check question
  I need to edit them

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
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype          | name                           | template |
      | Test questions   | knowledgecheck | knowledgecheck-001 for editing | default  |

  @javascript @_switch_window
  Scenario: Edit a Knowledge Check question
    When I am on the "knowledgecheck-001 for editing" "core_question > edit" page logged in as teacher
    # Trigger form validation error on empty question name.
    And I set the following fields to these values:
      | Question name |  |
    And I press "id_submitbutton"
    And I should see "You must supply a value here."
    # Change question name.
    And I set the following fields to these values:
      | Question name | Edited knowledgecheck-001 name |
    And I press "id_submitbutton"
    Then I should see "Edited knowledgecheck-001 name"
    # Preview question.
    And I choose "Preview" action for "Edited knowledgecheck-001 name" in the question bank
    And I should see "Describe what you see outside your window."
    # Set behaviour options
    And I set the following fields to these values:
      | behaviour | immediatefeedback |
    And I press "Save preview options and start again"
    # Switch into the WYSIWYG editor's iframe element.
    And I switch to "tox-edit-area__iframe" class iframe
    # Check that the response template text is applied.
    Then I should see "I see X, Y, and Z."
    # Get out of the iframe again.
    And I switch to the main frame
    And I press "Check"
    # Check answer-specific feedback.
    And I should see "Any answer is fine."
    # Check general feedback.
    And I should see "Thanks for answering this question."
