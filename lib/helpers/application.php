<?php

use Cranberry\Core\File;

/*
 * Set config
 */
$app->registerCommandObject( 'repoURL', $config['url'] );

/*
 * Set data directory
 */
// $dataDirectory = new File\Directory( '...' );
// $app->setDataDirectory( $dataDirectory );
