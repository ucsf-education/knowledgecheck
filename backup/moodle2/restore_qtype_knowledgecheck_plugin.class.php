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
 * @package    qtype_knowledgecheck
 * @subpackage backup-moodle2
 * @copyright  2016 The Regents of the University of California
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * restore plugin class that provides the necessary information
 * needed to restore one knowledgecheck qtype plugin
 *
 * @copyright  2016 The Regents of the University of California
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_qtype_knowledgecheck_plugin extends restore_qtype_plugin {

    /**
     * Returns the paths to be handled by the plugin at question level
     */
    protected function define_question_plugin_structure() {

        $paths = [];

        // This qtype uses question_answers, add them.
        $this->add_question_question_answers($paths);

        // Add own qtype stuff.
        $elename = 'knowledgecheck';
        // We used get_recommended_name() so this works.
        $elepath = $this->get_pathfor('/knowledgecheck');
        $paths[] = new restore_path_element($elename, $elepath);

        return $paths; // And we return the interesting paths.
    }

    /**
     * Process the qtype/knowledgecheck element
     */
    public function process_knowledgecheck($data) {
        global $DB;

        $data = (object)$data;

        if (!isset($data->responsetemplate)) {
            $data->responsetemplate = '';
        }

        $oldid = $data->id;

        // Detect if the question is created or mapped.
        $oldquestionid   = $this->get_old_parentid('question');
        $newquestionid   = $this->get_new_parentid('question');
        $questioncreated = $this->get_mappingid('question_created', $oldquestionid) ? true : false;

        // If the question has been created by restore, we need to create its
        // qtype_knowledgecheck_options too, if they are defined (the gui should ensure this).
        if ($questioncreated) {
            $data->questionid = $newquestionid;

            // It is possible for old backup files to contain unique key violations.
            // We need to check to avoid that.
            if (!$DB->record_exists('qtype_knowledgecheck_options', ['questionid' => $data->questionid])) {
                $newitemid = $DB->insert_record('qtype_knowledgecheck_options', $data);
                $this->set_mapping('qtype_knowledgecheck_options', $oldid, $newitemid);
            }
        }
    }
}
