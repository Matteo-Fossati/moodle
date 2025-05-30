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
 * Plugin administration pages are defined here.
 *
 * @package     filter_textsubstitute
 * @category    admin
 * @copyright   2025 Matteo Fossati <matteofossati@socialthingum.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace filter_textsubstitute;

class text_filter extends \core_filters\text_filter {

    public function filter($text, array $options = []) {
        $config = get_config('filter_textsubstitute');
        $searchterm = $config->searchterm;
        $replacewith = $config->substituteterm;
        //If the format is not specified or search term il empty, we do nothing.
        if(!isset($options['originalformat']) || empty($searchterm)) {
            return $text;
        }

        if (in_array($options['originalformat'], explode(',', get_config('filter_textsubstitute', 'formats')))) {
            //Return the modified test
            return $this->substitute_term($text, $searchterm, $replacewith);
        }

        return $text;
    }

    protected function substitute_term($text, string $searchterm, string $replacewith) {
        $text = str_replace($searchterm, $replacewith, $text);
        return $text;
    }
}