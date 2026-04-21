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
 * Privacy provider tests.
 *
 * @package    qtype_knowledgecheck
 * @copyright  (c) The Regents of the University of California
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace qtype_knowledgecheck\privacy;

use context_system;
use core_privacy\local\metadata\collection;
use core_privacy\local\metadata\types\user_preference;
use core_privacy\local\request\user_preference_provider;
use core_privacy\local\request\writer;
use core_privacy\tests\provider_testcase;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/knowledgecheck/classes/privacy/provider.php');

/**
 * Privacy provider tests class.
 *
 * @package    qtype_knowledgecheck
 * @copyright  (c) The Regents of the University of California
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \qtype_knowledgecheck\privacy\provider
 */
final class provider_test extends provider_testcase
{
    /**
     * Check meta-data declared by this privacy provider.
     */
    public function test_get_metadata(): void {
        $collection = new collection('qtype_knowledgecheck');
        $actual = provider::get_metadata($collection);
        $this->assertEquals($collection, $actual);
        $types = $actual->get_collection();
        $this->assertCount(1, $types);
        $this->assertInstanceOf(user_preference::class, $types[0]);
        $this->assertEquals('qtype_knowledgecheck_defaultmark', $types[0]->get_name());
        $this->assertEquals('privacy:preference:defaultmark', $types[0]->get_summary());
    }

    /**
     * Test the export_user_preferences without input.
     */
    public function test_export_user_preferences_no_pref(): void {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        provider::export_user_preferences($user->id);
        $writer = writer::with_context(context_system::instance());
        $this->assertFalse($writer->has_any_data());
    }

    /**
     * Test the export_user_preferences given different inputs for preferences set by this plugin.
     * @dataProvider user_preference_provider

     * @param string $name The name of the user preference to get/set.
     * @param string $value The value stored in the database.
     * @param string $expected The expected transformed value.
     */
    public function test_export_user_preferences($name, $value, $expected): void {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        set_user_preference("qtype_knowledgecheck_$name", $value, $user);
        provider::export_user_preferences($user->id);
        $writer = writer::with_context(context_system::instance());
        $this->assertTrue($writer->has_any_data());
        $preferences = $writer->get_user_preferences('qtype_knowledgecheck');
        foreach ($preferences as $key => $pref) {
            $preference = get_user_preferences("qtype_knowledgecheck_{$key}", null, $user->id);
            if ($preference === null) {
                continue;
            }
            $desc = get_string("privacy:preference:{$key}", 'qtype_knowledgecheck');
            $this->assertEquals($expected, $pref->value);
            $this->assertEquals($desc, $pref->description);
        }
    }

    /**
     * Create an array of valid user preferences for knowledgecheck question type.
     *
     * @return array Array of valid user preferences.
     */
    public static function user_preference_provider(): array {
        return [
            ['defaultmark', 2, 2],
        ];
    }
}
