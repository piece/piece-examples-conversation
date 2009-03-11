<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP versions 4 and 5
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
 * @package    Piece_Examples_CRUD
 * @copyright  2009 Piece Project
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    SVN: $Id$
 * @since      File available since Release 1.0.0
 */

require_once 'Piece/Unity/Service/FlowAction.php';
require_once 'Piece/ORM.php';

// {{{ OrderAction

/**
 * Action class for the flow Order.
 *
 * @package    Piece_Examples_CRUD
 * @copyright  2009 Piece Project
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: 1.0.0
 * @since      Class available since Release 1.0.0
 */
class OrderAction extends Piece_Unity_Service_FlowAction
{
    // {{{ properties

    /**#@+
     * @access public
     */

    /**#@-*/

    /**#@+
     * @access private
     */

    var $_order;

    /**#@-*/

    /**#@+
     * @access public
     */

    function onValidationMainMenu()
    {
        $validation = &$this->_context->getValidation();
        if (!$validation->validate('MainMenu', $this->_order)) {
            return 'invalid';
        }

        return 'valid';
    }

    function onValidationSideMenu()
    {
        $validation = &$this->_context->getValidation();
        if (!$validation->validate('SideMenu', $this->_order)) {
            return 'invalid';
        }

        return 'valid';
    }

    function onConfirmation()
    {
        $mainMenu = array('1' => 'ジャーマンポテトバーガー',
                          '2' => 'ポテトコロッケバーガー',
                          '3' => '肉じゃがバーガー'
                          );
        $sideMenu = array('1' => 'フライドポテト',
                          '2' => 'ポテトサラダ',
                          '3' => 'スイートポテト'
                          );
        $prices = array('1' => 650,
                        '2' => 600,
                        '3' => 700
                        );

        $viewElement = &$this->_context->getViewElement();
        $viewElement->setElement('main',  $mainMenu[$this->_order->main]);
        $viewElement->setElement('side',  $sideMenu[$this->_order->side]);
        $viewElement->setElement('price', $prices[$this->_order->main]);
    }

    function onRegistration()
    {
        $mapper = Piece_ORM::getMapper('Orders');
        $mapper->insert($this->_order);

        return 'finish';
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
