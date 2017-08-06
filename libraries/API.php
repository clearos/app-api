<?php

/**
 * API helper class.
 *
 * @category   apps
 * @package    api
 * @subpackage libraries
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2017 ClearFoundation
 * @license    http://www.gnu.org/copyleft/lgpl.html GNU Lesser General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/api/
 */

///////////////////////////////////////////////////////////////////////////////
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU Lesser General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU Lesser General Public License for more details.
//
// You should have received a copy of the GNU Lesser General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
///////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////
// N A M E S P A C E
///////////////////////////////////////////////////////////////////////////////

namespace clearos\apps\api;

///////////////////////////////////////////////////////////////////////////////
// B O O T S T R A P
///////////////////////////////////////////////////////////////////////////////

$bootstrap = getenv('CLEAROS_BOOTSTRAP') ? getenv('CLEAROS_BOOTSTRAP') : '/usr/clearos/framework/shared';
require_once $bootstrap . '/bootstrap.php';

///////////////////////////////////////////////////////////////////////////////
// T R A N S L A T I O N S
///////////////////////////////////////////////////////////////////////////////

clearos_load_language('api');
clearos_load_language('base');

///////////////////////////////////////////////////////////////////////////////
// D E P E N D E N C I E S
///////////////////////////////////////////////////////////////////////////////

use \clearos\apps\base\Engine as Engine;
use \clearos\apps\base\Folder as Folder;

clearos_load_library('base/Engine');
clearos_load_library('base/Folder');

///////////////////////////////////////////////////////////////////////////////
// C L A S S
///////////////////////////////////////////////////////////////////////////////

/**
 * API helper class.
 *
 * @category   apps
 * @package    api
 * @subpackage libraries
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2017 ClearFoundation
 * @license    http://www.gnu.org/copyleft/lgpl.html GNU Lesser General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/api/
 */

class API extends Engine
{
    ///////////////////////////////////////////////////////////////////////////////
    // C O N S T A N T S
    ///////////////////////////////////////////////////////////////////////////////

    const CODE_OK = 0;
    const CODE_VALIDATION_ERROR = 1;
    const CODE_ERROR = 2;
    const CODE_AUTHENTICATION_FAILED = 3;

    ///////////////////////////////////////////////////////////////////////////////
    // M E T H O D S
    ///////////////////////////////////////////////////////////////////////////////

    /**
     * API constructor.
     */

    public function __construct()
    {
        clearos_profile(__METHOD__, __LINE__);
    }

    /**
     * Returns list of available apps.
     *
     * @return array list of available apps
     */

    function get_apps()
    {
        clearos_profile(__METHOD__, __LINE__);

        $app_list = clearos_get_apps();
        $apps = [];

        foreach ($app_list as $basename => $details)
            $apps[$basename]['name'] = $details['name'];

        ksort($apps);

        return $this->_response(API::CODE_OK, lang('base_success'), $apps);
    }

    /**
     * Returns list of available classes.
     *
     * @return array list of available classes
     */

    function get_classes($app = '')
    {
        clearos_profile(__METHOD__, __LINE__);

        $all_apps = clearos_get_apps();

        if (empty($app))
            $app_list = $all_apps;
        else if (array_key_exists($app, $all_apps))
            $app_list[$app] = $all_apps[$app];
        else
            return $this->_response(1, 'Invalid app specified', []);

        $classes = [];

        foreach ($app_list as $basename => $details) {
            $lib_path = clearos_app_base($basename) . '/libraries';

            $folder = new Folder($lib_path);

            if ($folder->exists()) {
                $files = $folder->get_listing();
                if (!empty($files)) {
                    $class_files = [];

                    foreach ($files as $file) {
                        if (!preg_match('/_Exception.php$/', $file))
                            $class_files[] = preg_replace('/\.php$/', '', $file);
                    }

                    if (!empty($class_files)) {
                        $classes[$basename]['name'] = $details['name'];
                        $classes[$basename]['classes'] = $class_files;
                    }
                }
            }
        }

        if (!empty($app))
            $classes = $classes[$app]['classes'];

        return $this->_response(API::CODE_OK, lang('base_success'), $classes);
    }

    /**
     * Returns list of available methods
     *
     * @return array list of available apps
     */

    function get_methods($app, $class)
    {
        clearos_profile(__METHOD__, __LINE__);

        require_once clearos_app_base($app) . '/libraries/' . $class . '.php';

        $class_path = '\clearos\apps\\' . $app . '\\' . $class;
        $object = new $class_path();

        $all_methods = get_class_methods($object);
        $methods = [];

        foreach ($all_methods as $method) {
            if (!preg_match('/^_/', $method))
                $methods[] = $method;
        }

        sort($methods);

        return $this->_response(API::CODE_OK, lang('base_success'), $methods);
    }

    /**
     * Returns API response.
     *
     * @return array API response
     */

    function get_result($app, $class, $method, $params = [])
    {
        clearos_profile(__METHOD__, __LINE__);

        if (!preg_match('/^get_/', $method))
            return $this->_response(API::CODE_ERROR, 'Just get() methods for now.', []);

        require_once clearos_app_base($app) . '/libraries/' . $class . '.php';

        $class_path = '\clearos\apps\\' . $app . '\\' . $class;
        $object = new $class_path();

        if (!method_exists($object, $method))
            return $this->_response(API::CODE_VALIDATION_ERROR, lang('api_method_does_not_exist'), '');

        $result = $object->$method();

        return $this->_response(API::CODE_OK, lang('base_success'), $result);
    }

    ///////////////////////////////////////////////////////////////////////////////
    // P R I V A T E  M E T H O D S
    ///////////////////////////////////////////////////////////////////////////////

    /**
     * API response.
     *
     * @return array API response.
     */

    function _response($code, $message, $result)
    {
        clearos_profile(__METHOD__, __LINE__);

        $payload['code'] = $code;
        $payload['message'] = $message;
        $payload['result'] = $result;

        return $payload;
    }
}
