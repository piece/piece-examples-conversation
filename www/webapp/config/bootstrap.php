<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP version 5
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
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    Release: @package_version@
 * @since      File available since Release 0.1.0
 */

error_reporting(E_ALL);

$projectRoot = realpath(dirname(__FILE__) . '/../../..');
set_include_path("$projectRoot/src" . PATH_SEPARATOR .
                 "$projectRoot/imports/pear" . PATH_SEPARATOR .
                 "$projectRoot/imports/pear/src" . PATH_SEPARATOR .
                 "$projectRoot/imports/non-pear/spyc-0.2.5" . PATH_SEPARATOR .
                 "$projectRoot/imports/non-pear/src"
                 );
unset($projectRoot);

require 'Stagehand/Autoload/PEAR.php';

Piece_Unity_Service_ExceptionHandler::register(new Piece_Unity_Service_ExceptionHandler_DebugInfo());
// Piece_Unity_Service_ExceptionHandler::register(new Piece_Unity_Service_ExceptionHandler_InternalServerError());
Piece_Unity_Service_ExceptionHandler::register(new Piece_Unity_Service_ExceptionHandler_ErrorLog());
Piece_Unity_Service_ExceptionHandler::enable();
Piece_Unity::setConfigurationCallback('configure');

function configure(Piece_Unity $runtime)
{
    $appRoot = realpath(dirname(__FILE__) . '/..');
    $runtime->configure("$appRoot/config", "$appRoot/cache");
    $runtime->setConfiguration('Configurator_AppRoot', 'appRoot', $appRoot);
}

/*
 * Local Variables:
 * mode: php
 * coding: iso-8859-1
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * indent-tabs-mode: nil
 * End:
 */
