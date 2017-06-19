<?php

/**
 * API Server controller.
 *
 * @category   Apps
 * @package    API_Server
 * @subpackage Views
 * @author     Your name <your@e-mail>
 * @copyright  2013 Your name / Company
 * @license    Your license
 */

///////////////////////////////////////////////////////////////////////////////
// D E P E N D E N C I E S
///////////////////////////////////////////////////////////////////////////////

use \clearos\apps\api\API as ClearOS_API;

///////////////////////////////////////////////////////////////////////////////
// C L A S S
///////////////////////////////////////////////////////////////////////////////

/**
 * API Server controller.
 *
 * @category   Apps
 * @package    API_Server
 * @subpackage Controllers
 * @author     Your name <your@e-mail>
 * @copyright  2013 Your name / Company
 * @license    Your license
 */

class Api extends ClearOS_Controller
{
    /**
     * API app enumeration.
     *
     * @return view
     */

    function index()
    {
        // Load dependencies
        //------------------

        $this->load->library('api/API');
        $this->lang->load('api');

        // Load view data
        //---------------

        try {
            $response = $this->api->get_classes();
            $data['classes'] = $response['result'];
        } catch (Exception $e) {
            $this->page->view_exception($e);
            return;
        }

        // Response
        //---------

        $this->page->view_form('classes', $data, lang('api_classes'));
    }

    /**
     * Browse API.
     *
     * @return view
     */

    function browse($app = '', $class = '', $method = '')
    {
        // Load dependencies
        //------------------

        $this->load->library('api/API');
        $this->lang->load('api');

        // Load view data
        //---------------

        try {
            if (empty($app)) {
                $data['result'] = 'TODO';
            } else if (empty($class)) {
                $data['result'] = 'TODO';
            } else if (empty($method)) {
                $response = $this->api->get_methods($app, $class);
                $data['methods'] = $response['result'];
            } else {
                // $parameters = [];
                // $response = $this->api->get_result($app, $class, $method, $parameters);
                // $data['result'] = $response['result'];
                $data['result'] = 'Sanity check';
            }
        } catch (Exception $e) {
            $this->page->view_exception($e);
            return;
        }

        // Response
        //---------

        if (empty($method)) {
            $this->page->view_form('methods', $data, lang('api_methods'));
        } else {
            print_r($data);
        }
    }

    /**
     * Request from API.
     *
     * @return view
     */

    function request($app = '', $class = '', $method = '')
    {
        // Load dependencies
        //------------------

        $this->load->library('api/API', 'json');
        $this->lang->load('api');

        // Very basic authentication
        //--------------------------

        // FIXME: just a quick hack for now
        $api_key = (empty($_REQUEST['api_key'])) ? '' :  $_REQUEST['api_key'];
        $local_api_key = trim(file_get_contents('/etc/clearos/api.key'));

        // Load view data
        //---------------

        if (empty($local_api_key)) {
            $response = $this->api->_response(ClearOS_API::CODE_AUTHENTICATION_FAILED, 'Please set API key in /etc/clearos/api.key on the minion', []);
        } else if (empty($api_key)) {
            $response = $this->api->_response(ClearOS_API::CODE_AUTHENTICATION_FAILED, 'Please set the API key in your API request', []);
        } else if ($api_key != $local_api_key) {
            $response = $this->api->_response(ClearOS_API::CODE_AUTHENTICATION_FAILED, lang('api_authentication_failed'), []);
        } else {
            try {
                if (empty($app)) {
                    $response = $this->api->get_apps();
                } else if (empty($class)) {
                    $response = $this->api->get_classes($app);
                } else if (empty($method)) {
                    $response = $this->api->get_methods($app, $class);
                } else {
                    if (preg_match('/^get_/', $method))
                        $response = $this->api->get_result($app, $class, $method);
                    else
                        $response = $this->api->_response(ClearOS_API::CODE_ERROR, 'Just get() methods for now.', []);
                }
            } catch (Exception $e) {
                $response = $this->api->_response(ClearOS_API::CODE_ERROR, $e->getMessage(), []);
            }
        }

        // Response
        //---------

        header('Cache-Control: no-cache, must-revalidate');
        header('Content-type: application/json');
        echo json_encode($response);
    }
}
