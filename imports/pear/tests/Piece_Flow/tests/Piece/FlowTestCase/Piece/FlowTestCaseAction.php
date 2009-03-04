<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP versions 4 and 5
 *
 * Copyright (c) 2006-2007 KUBO Atsuhiro <iteman@users.sourceforge.net>,
 * All rights reserved.
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
 * @package    Piece_Flow
 * @copyright  2006-2007 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    SVN: $Id: FlowTestCaseAction.php 533 2008-06-21 19:08:42Z iteman $
 * @see        Piece_FlowTestCase
 * @since      File available since Release 1.0.0
 */

require_once 'Piece/Flow/Action.php';

// {{{ Piece_FlowTestCaseAction

/**
 * A class for unit tests.
 *
 * @package    Piece_Flow
 * @copyright  2006-2007 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: 1.16.0
 * @see        Piece_FlowTestCase
 * @since      Class available since Release 1.0.0
 */
class Piece_FlowTestCaseAction extends Piece_Flow_Action
{

    // {{{ properties

    /**#@+
     * @access public
     */

    /**#@-*/

    /**#@+
     * @access private
     */

    /**#@-*/

    /**#@+
     * @access public
     */

    function validateInput()
    {
        $GLOBALS['validateInputCalled'] = true;

        if (array_key_exists('hasErrors', $GLOBALS)
            && $GLOBALS['hasErrors']
            ) {
            return 'raiseError';
        }

        return 'succeed';
    }

    function validateConfirmation()
    {
        $GLOBALS['validateConfirmationCalled'] = true;

        if (array_key_exists('hasErrors', $GLOBALS)
            && $GLOBALS['hasErrors']
            ) {
            return 'raiseError';
        }

        return 'succeed';
    }

    function register()
    {
        return 'succeed';
    }

    function isPermitted()
    {
        return true;
    }

    function setupForm()
    {
        $GLOBALS['setupFormCalled'] = true;
    }

    function teardownForm()
    {
        $GLOBALS['teardownFormCalled'] = true;
    }

    function countDisplay()
    {
        if (array_key_exists('displayCounter', $GLOBALS)) {
            ++$GLOBALS['displayCounter'];
        }
    }

    function initialize() {}

    function finalize() {}

    function prepare()
    {
        $GLOBALS['prepareCalled'] = true;
    }

    /**#@-*/

    /**#@+
     * @access private
     */

    /**#@-*/

    // }}}
}

// }}}

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
