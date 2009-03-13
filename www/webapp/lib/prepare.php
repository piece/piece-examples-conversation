<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP versions 5
 *
 * Copyright (c) 2009 Piece Project, All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package    Piece_Examples_Conversation
 * @copyright  2009 Piece Project
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: 1.0.0
 * @since      File available since Release 1.0.0
 */

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

Stagehand_LegacyError_PEARErrorStack::enableConversion();

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
