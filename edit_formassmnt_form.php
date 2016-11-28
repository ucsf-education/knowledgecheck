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
 * Defines the editing form for the formative assessment question type.
 *
 * @package    qtype
 * @subpackage formassmnt
 * @copyright  2016 The Regents of the University of California

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Formative assessment question editing form definition.
 *
 * @copyright  2016 The Regents of the University of California

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_formassmnt_edit_form extends question_edit_form {

    protected function definition_inner($mform) {
        $mform->addElement('header', 'responsetemplateheader', get_string('responsetemplateheader', 'qtype_formassmnt'));
        $mform->addElement('editor', 'responsetemplate', get_string('responsetemplate', 'qtype_formassmnt'),
            array('rows' => 10),  array_merge($this->editoroptions, array('maxfiles' => 0)));
        $mform->addHelpButton('responsetemplate', 'responsetemplate', 'qtype_formassmnt');
        $this->add_per_answer_fields($mform, get_string('answerno', 'qtype_formassmnt', '{no}'),
            array('1.0' => '100%'), 1, 0);
    }

    public function qtype() {
        return 'formassmnt';
    }
}
