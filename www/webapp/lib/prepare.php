<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

error_reporting(E_ALL);

set_include_path(realpath(dirname(__FILE__)) . PATH_SEPARATOR .
                 realpath(dirname(__FILE__) . '/../actions') . PATH_SEPARATOR .
                 realpath(dirname(__FILE__) . '/../../../src') . PATH_SEPARATOR .
                 realpath(dirname(__FILE__) . '/../../../imports/pear') . PATH_SEPARATOR .
                 realpath(dirname(__FILE__) . '/../../../imports/pear/src') . PATH_SEPARATOR .
                 realpath(dirname(__FILE__) . '/../../../imports/non-pear/src') . PATH_SEPARATOR .
                 realpath(dirname(__FILE__) . '/../../../imports/non-pear/spyc-0.2.5')
                 );

require 'Stagehand/Autoload/PEAR.php';

// }}}
// {{{ configureRuntime()

/**
 * @param Piece_Unity $runtime
 */
function configureRuntime(Piece_Unity $runtime)
{
    $base = dirname(__FILE__) . '/..';
    $runtime->configure("$base/config", "$base/cache");
    $runtime->setConfiguration('Configurator_AppRoot',
                               'appRoot',
                               dirname(__FILE__) . '/../../htdocs'
                               );
}

/*
 * Local Variables:
 * mode: php
 * coding: utf-8
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * indent-tabs-mode: nil
 * End:
 */
