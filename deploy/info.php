<?php

/////////////////////////////////////////////////////////////////////////////
// General information
/////////////////////////////////////////////////////////////////////////////

$app['basename'] = 'api';
$app['version'] = '1.0.1';
$app['release'] = '1';
$app['vendor'] = 'ClearFoundation';
$app['packager'] = 'ClearFoundation';
$app['license'] = 'GPLv3';
$app['license_core'] = 'LGPLv3';
$app['description'] = lang('api_app_description');

/////////////////////////////////////////////////////////////////////////////
// App name and categories
/////////////////////////////////////////////////////////////////////////////

$app['name'] = lang('api_app_name');
$app['category'] = lang('base_category_system');
$app['subcategory'] = 'Developer'; // e.g. lang('base_subcategory_settings');

/////////////////////////////////////////////////////////////////////////////
// Packaging
/////////////////////////////////////////////////////////////////////////////

$app['core_requires'] = array(
    'clearos-framework >= 7.4.8'
);


$app['core_file_manifest'] = array(
    'api.acl' => array('target' => '/var/clearos/base/access_control/public/api'),
    'api' => array(
        'target' => '/usr/bin/api',
        'mode' => '0755',
    ),
);
