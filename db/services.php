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
 * Web service local plugin template external functions and service definitions.
 *
 * @package    localwstemplate
 * @copyright  2011 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// We defined the web service functions to install.
$functions = array(
        'local_renwebservices_hello_world' => array(
                'classname'   => 'local_renwebservices',
                'methodname'  => 'hello_world',
                'classpath'   => 'local/renwebservices/externallib.php',
                'description' => 'Return Hello World FIRSTNAME. Can change the text (Hello World) sending a new text as parameter',
                'type'        => 'read',
        ),
        'local_renwebservices_create_grade_category' => array(
                'classname'   => 'local_renwebservices',
                'methodname'  => 'create_grade_category',
                'classpath'   => 'local/renwebservices/externallib.php',
                'description' => 'Creates a Grade Category in a course.',
                'type'        => 'write',
        )
);

// We define the services to install as pre-build services. A pre-build service is not editable by administrator.
$services = array(
        'RenWeb Services' => array(
                'functions' => array (
                        'local_renwebservices_hello_world',
                        'local_renwebservices_create_grade_category',
                        'core_group_create_groups',
                        'core_group_create_groupings',
                        'core_course_create_categories',
                        'core_course_create_courses',
                        'core_group_assign_grouping'
                ),
                'restrictedusers' => 0,
                'enabled'=>1,
                'shortname'=>'RenWebServices'
        )
);