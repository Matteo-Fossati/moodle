<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Plugin version and other meta-data are defined here.
 *
 * @package     local_greetings
 * @copyright   2025 Matteo Fossati <matteofossati@socialthingum.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


 /**
  * Returns a personalized greeting for a given user based on their country.
  *
  * @param stdClass $user The Moodle user object (e.g., $USER global)
  * @return string The personalized greeting message.
  */
function local_greetings_get_greeting($user) {
    if ($user == null) {
        return get_string('greetinguser', 'local_greetings');
    }

    $country = $user->country;
    switch($country) {
        case 'AU':
            $langstr = 'greetinguserau';
            break;
        case 'ES':
            $langstr = 'greetinguseres';
            break;
        case 'FJ':
            $langstr = 'greetinguserfj';
            break;
        case 'NZ':
            $langstr = 'greetingusernz';
            break;
        case 'IT':
            $langstr = 'greetinguserit';
            break;
        default:
            $langstr = 'greetingloggedinuser';
    }

    return get_string($langstr, 'local_greetings', fullname($user));
}
