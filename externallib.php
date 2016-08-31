<?php

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
 * External Web Service Template
 *
 * @package    localwstemplate
 * @copyright  2011 Moodle Pty Ltd (http://moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->libdir . "/externallib.php");

class local_renwebservices extends external_api {

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function hello_world_parameters() {
        return new external_function_parameters(
                array('welcomemessage' => new external_value(PARAM_TEXT, 'The welcome message. By default it is "Hello world,"', VALUE_DEFAULT, 'Hello world, '))
        );
    }

    /**
     * Returns welcome message
     * @return string welcome message
     */
    public static function hello_world($welcomemessage = 'Hello world, ') {
        global $USER;

        //Parameter validation
        //REQUIRED
        $params = self::validate_parameters(self::hello_world_parameters(),
                array('welcomemessage' => $welcomemessage));

        //Context validation
        //OPTIONAL but in most web service it should present
        $context = get_context_instance(CONTEXT_USER, $USER->id);
        self::validate_context($context);

        //Capability checking
        //OPTIONAL but in most web service it should present
        if (!has_capability('moodle/user:viewdetails', $context)) {
            throw new moodle_exception('cannotviewprofile');
        }

        return $params['welcomemessage'] . $USER->firstname ;;
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function hello_world_returns() {
        return new external_value(PARAM_TEXT, 'The welcome message + user first name');
    }


    public static function create_grade_category_parameters(){
        return new external_function_parameters(
            array(
                'categories' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'courseid' =>
                                new external_value(PARAM_INT, 'The id of the course to create the category in.'),
                            'fullname' =>
                                new external_value(PARAM_TEXT, 'Name of the category.'),
                            'weight' =>
                                new external_value(PARAM_INT, 'the Weight of the category.')
                        )
                    )
                )
            )
        );
    }
    public static function create_grade_category($categories){
        global $CFG, $DB;
        require_once($CFG->dirroot."/lib/weblib.php");
        require_once($CFG->dirroot."/grade/lib.php");
        $params = self::validate_parameters(self::create_grade_category_parameters(), array('categories' => $categories));
        $response = array();
    
        foreach($params['categories'] as $cat){

            $courseid = $cat['courseid'];
            $fullname = $cat['fullname'];

            $course_category = grade_category::fetch_course_category($courseid);
            $grade_category = new grade_category(array('courseid'=>$courseid), false);

            $grade_category->apply_default_settings();
            $grade_category->apply_forced_settings();


            $category = $grade_category->get_record_data();
            $grade_item = new grade_item(array('courseid'=>$courseid, 'itemtype'=>'manual'), false);
            
            foreach ($grade_item->get_record_data() as $key => $value) {
                $category->{"grade_item_$key"} = $value;
            }

            $grade_category->fullname = $fullname;
            $grade_category->insert();
            $grade_category->set_parent($course_category->id, 'RenWeb Service');

            $grade_item = $grade_category->load_grade_item();
            $grade_item->weightoverride = $cat['weight'];
            $grade_item->update();

            $response[] = array(
                id => $grade_category->id
            );
        }

        return $response;
    }
    public static function create_grade_category_returns(){
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id'       => new external_value(PARAM_INT, 'category id'),
                )
            )
        );
    }

}
