@qtype @qtype_knowledgecheck @qtype_knowledgecheck_export
Feature: Test exporting Knowledge Check questions
  As a teacher
  In order to be able to reuse my Knowledge Check questions
  I need to export them

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
      | questioncategory | qtype          | name               | template |
      | Test questions   | knowledgecheck | knowledgecheck-001 | default  |

  Scenario: Export a Knowledge Check question
    When I am on the "Course 1" "core_question > course question export" page logged in as teacher
    And I set the field "id_format_xml" to "1"
    And I press "Export questions to file"
    Then following "click here" should download a file that:
      | Has mimetype                 | text/xml                                   |
      | Contains text in xml element | knowledgecheck-001                         |
      | Contains text in xml element | I see X, Y, and Z.                         |
      | Contains text in xml element | Describe what you see outside your window. |
      | Contains text in xml element | Any answer is fine.                        |
    # If the download step is the last in the scenario then we can sometimes run
    # into the situation where the download page causes a http redirect but behat
    # has already conducted its reset (generating an error). By putting a logout
    # step we avoid behat doing the reset until we are off that page.
    And I log out
