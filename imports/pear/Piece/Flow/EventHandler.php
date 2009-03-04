<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP versions 4 and 5
 *
 * Copyright (c) 2006-2008 KUBO Atsuhiro <iteman@users.sourceforge.net>,
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
 * @copyright  2006-2008 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    SVN: $Id: EventHandler.php 533 2008-06-21 19:08:42Z iteman $
 * @since      File available since Release 0.1.0
 */

require_once 'Piece/Flow/Action/Factory.php';
require_once 'Piece/Flow/Error.php';

// {{{ Piece_Flow_EventHandler

/**
 * The invoker for an event handler.
 *
 * @package    Piece_Flow
 * @copyright  2006-2008 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: 1.16.0
 * @since      Class available since Release 0.1.0
 */
class Piece_Flow_EventHandler
{

    // {{{ properties

    /**#@+
     * @access public
     */

    /**#@-*/

    /**#@+
     * @access private
     */

    var $_flow;
    var $_class;
    var $_method;
    var $_actionDirectory;

    /**#@-*/

    /**#@+
     * @access public
     */

    // }}}
    // {{{ constructor

    /**
     * Wraps a action up with a Piece_Flow_EventHandler object.
     *
     * @param Piece_Flow &$flow
     * @param string     $class
     * @param string     $method
     * @param string     $actionDirectory
     */
    function Piece_Flow_EventHandler(&$flow, $class, $method, $actionDirectory)
    {
        $this->_flow = &$flow;

        if (is_null($class) || !strlen($class)) {
            $this->_class = $this->_flow->getName() . 'Action';
        } else {
            $this->_class = $class;
        }

        $this->_method = $method;
        $this->_actionDirectory = $actionDirectory;
    }

    // }}}
    // {{{ invoke()

    /**
     * Invokes an event handler in an action.
     *
     * @param Stagehand_FSM       &$fsm
     * @param Stagehand_FSM_Event &$event
     * @param mixed               &$payload
     * @return mixed
     */
    function invoke(&$fsm, &$event, &$payload)
    {
        return $this->_invokeEventHandler($event->getName(), $payload);
    }

    // }}}
    // {{{ invokeAndTriggerEvent()

    /**
     * Invokes an event handler in an action and triggers an event returned
     * from the action.
     *
     * @param Stagehand_FSM       &$fsm
     * @param Stagehand_FSM_Event &$event
     * @param mixed               &$payload
     * @throws PIECE_FLOW_ERROR_INVALID_EVENT
     */
    function invokeAndTriggerEvent(&$fsm, &$event, &$payload)
    {
        $result = $this->_invokeEventHandler($event->getName(), $payload);
        if (Piece_Flow_Error::hasErrors()) {
            return;
        }

        if (!is_null($result)) {
            if ($fsm->hasEvent($result)) {
                $fsm->queueEvent($result);
            } else {
                Piece_Flow_Error::push(PIECE_FLOW_ERROR_INVALID_EVENT,
                                       "An invalid event [ $result ] is returned from [ {$this->_class}::{$this->_method}() ] method on the state [ " . $this->_flow->getCurrentStateName() . ' ]. Check the flow definition and the action class.',
                                       'exception',
                                       array('event' => $result,
                                             'class' => $this->_class,
                                             'method' => $this->_method)
                                       );
            }
        }
    }

    /**#@-*/

    /**#@+
     * @access private
     */

    // }}}
    // {{{ _invokeEventHandler()

    /**
     * Invokes an event handler in an action.
     *
     * @param string $eventName
     * @param mixed  &$payload
     * @return string
     * @throws PIECE_FLOW_ERROR_NOT_FOUND
     */
    function _invokeEventHandler($eventName, &$payload)
    {
        if (!is_null($this->_actionDirectory)) {
            Piece_Flow_Action_Factory::setActionDirectory($this->_actionDirectory);
        }

        $action = &Piece_Flow_Action_Factory::factory($this->_class);
        if (Piece_Flow_Error::hasErrors()) {
            return;
        }

        if (!method_exists($action, $this->_method)) {
            Piece_Flow_Error::push(PIECE_FLOW_ERROR_NOT_FOUND,
                                   "The method [ {$this->_method} ] does not exist in the action class [ {$this->_class} ]."
                                   );
            return;
        }

        if (method_exists($action, 'setFlow')) {
            $action->setFlow($this->_flow);
        }

        if (method_exists($action, 'setPayload')) {
            $action->setPayload($payload);
        }

        if (method_exists($action, 'setEvent')) {
            $action->setEvent($eventName);
        }

        if (method_exists($action, 'prepare')) {
            $action->prepare();
        }

        $result = call_user_func(array(&$action, $this->_method));

        if (method_exists($action, 'clear')) {
            $action->clear();
        }

        return $result;
    }

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
