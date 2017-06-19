<?php

/**
 * App view.
 *
 * @category   apps
 * @package    api
 * @subpackage views
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2017 ClearFoundation
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/api/
 */

///////////////////////////////////////////////////////////////////////////////
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.  
//  
///////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////
// Load dependencies
///////////////////////////////////////////////////////////////////////////////

$this->lang->load('api');
$this->lang->load('base');

///////////////////////////////////////////////////////////////////////////////
// Headers
///////////////////////////////////////////////////////////////////////////////

$headers = array(
    lang('api_basename'),
    lang('api_class'),
);

///////////////////////////////////////////////////////////////////////////////
// Items
///////////////////////////////////////////////////////////////////////////////

foreach ($classes as $basename => $details) {
    foreach ($details['classes'] as $class) {
        $detail_buttons = button_set(
            array(anchor_custom('/app/api/browse/' . $basename . '/' . $class, lang('api_browse')))
        );

        $item['title'] = $class;
        $item['action'] = '/app/api/browse/' . $basename . '/' . $class;
        $item['anchors'] = $detail_buttons;
        $item['details'] = array(
            $details['name'],
            $class
        );

        $items[] = $item;
    }
}

///////////////////////////////////////////////////////////////////////////////
// Summary table
///////////////////////////////////////////////////////////////////////////////

$options['grouping'] = TRUE;
$options['default_rows'] = 200;

echo summary_table(
    lang('api_class_list'),
    array(),
    $headers,
    $items,
    $options
);
