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
 * @version    SVN: $Id: Continuation.php 538 2008-06-23 14:20:35Z iteman $
 * @since      File available since Release 1.0.0
 * @deprecated File deprecated in Release 1.14.0
 */

require_once 'Piece/Flow.php';
require_once 'Piece/Flow/Error.php';
require_once 'Piece/Flow/Action/Factory.php';
require_once 'Piece/Flow/Continuation/GC.php';

// {{{ GLOBALS

$GLOBALS['PIECE_FLOW_Continuation_ActiveInstances'] = array();
$GLOBALS['PIECE_FLOW_Continuation_ShutdownRegistered'] = false;

// }}}
// {{{ Piece_Flow_Continuation

/**
 * The continuation server for the Piece_Flow package.
 *
 * @package    Piece_Flow
 * @copyright  2006-2008 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: 1.16.0
 * @since      Class available since Release 1.0.0
 * @deprecated Class deprecated in Release 1.14.0
 */
class Piece_Flow_Continuation
{

    // {{{ properties

    /**#@+
     * @access public
     */

    /**#@-*/

    /**#@+
     * @access private
     */

    var $_flowDefinitions = array();
    var $_enableSingleFlowMode;
    var $_cacheDirectory;
    var $_flowExecutions = array();
    var $_flowExecutionTicketCallback;
    var $_flowNameCallback;
    var $_eventNameCallback;
    var $_exclusiveFlowExecutionTicketsByFlowName = array();
    var $_isFirstTime;
    var $_currentFlowName;
    var $_currentFlowExecutionTicket;
    var $_activated = false;
    var $_exclusiveFlowNamesByFlowExecutionTicket = array();
    var $_gc;
    var $_enableGC = false;

    /**#@-*/

    /**#@+
     * @access public
     */

    // }}}
    // {{{ constructor

    /**
     * Sets whether the continuation server should be work in the single flow
     * mode.
     *
     * @param boolean $enableSingleFlowMode
     * @param integer $enableGC
     * @param integer $gcExpirationTime
     */
    function Piece_Flow_Continuation($enableSingleFlowMode = false, $enableGC = false, $gcExpirationTime = 1440)
    {
        if (!$enableSingleFlowMode) {
            if ($enableGC) {
                $this->_gc = &new Piece_Flow_Continuation_GC($gcExpirationTime);
                $this->_enableGC = true;
            }
        }

        $this->_enableSingleFlowMode = $enableSingleFlowMode;
    }

    // }}}
    // {{{ addFlow()

    /**
     * Adds a flow definition to the Piece_Flow_Continuation object.
     *
     * @param string  $name
     * @param string  $file
     * @param boolean $isExclusive
     * @throws PIECE_FLOW_ERROR_ALREADY_EXISTS
     */
    function addFlow($name, $file, $isExclusive = false)
    {
        if ($this->_enableSingleFlowMode && count($this->_flowDefinitions)) {
            Piece_Flow_Error::push(PIECE_FLOW_ERROR_ALREADY_EXISTS,
                                   'A flow definition already exists in the continuation object.'
                                   );
            return;
        }

        $this->_flowDefinitions[$name] = array('file' => $file,
                                               'isExclusive' => $isExclusive
                                               );
    }

    // }}}
    // {{{ invoke()

    /**
     * Invokes a flow and returns a flow execution ticket.
     *
     * @param mixed   &$payload
     * @param boolean $bindActionsWithFlowExecution
     * @return string
     */
    function invoke(&$payload, $bindActionsWithFlowExecution = false)
    {
        if ($this->_enableGC) {
            $this->_gc->setGCCallback(array(&$this, 'disableFlowExecution'));
            $this->_gc->mark();
        }

        $this->_prepare();
        if (Piece_Flow_Error::hasErrors()) {
            return;
        }

        if (!$this->_isFirstTime) {
            $this->_continue($payload, $bindActionsWithFlowExecution);
        } else {
            $this->_start($payload);
        }

        if (Piece_Flow_Error::hasErrors()) {
            return;
        }

        if ($this->_enableGC && !$this->_isExclusive()) {
            $this->_gc->update($this->_currentFlowExecutionTicket);
        }

        if ($bindActionsWithFlowExecution) {
            $this->_flowExecutions[$this->_currentFlowExecutionTicket]->clearPayload();
            $this->_flowExecutions[$this->_currentFlowExecutionTicket]->setAttribute('_actionInstances', Piece_Flow_Action_Factory::getInstances());
        }

        $GLOBALS['PIECE_FLOW_Continuation_ActiveInstances'][] = &$this;
        if (!$GLOBALS['PIECE_FLOW_Continuation_ShutdownRegistered']) {
            $GLOBALS['PIECE_FLOW_Continuation_ShutdownRegistered'] = true;
            register_shutdown_function(array(__CLASS__, 'shutdown'));
        }

        return $this->_currentFlowExecutionTicket;
    }

    // }}}
    // {{{ getView()

    /**
     * Gets an appropriate view string which corresponding to the current
     * state.
     *
     * @return string
     * @throws PIECE_FLOW_ERROR_INVALID_OPERATION
     */
    function getView()
    {
        if (!$this->_activated()) {
            Piece_Flow_Error::push(PIECE_FLOW_ERROR_INVALID_OPERATION,
                                   __FUNCTION__ . ' method must be called after starting/continuing flows.'
                                   );
            return;
        }

        return $this->_flowExecutions[$this->_currentFlowExecutionTicket]->getView();
    }

    // }}}
    // {{{ setEventNameCallback()

    /**
     * Sets a callback for getting an event name.
     *
     * @param callback $callback
     */
    function setEventNameCallback($callback)
    {
        $this->_eventNameCallback = $callback;
    }

    // }}}
    // {{{ setFlowExecutionTicketCallback()

    /**
     * Sets a callback for getting a flow execution ticket.
     *
     * @param callback $callback
     */
    function setFlowExecutionTicketCallback($callback)
    {
        $this->_flowExecutionTicketCallback = $callback;
    }

    // }}}
    // {{{ setFlowNameCallback()

    /**
     * Sets a callback for getting a flow name.
     *
     * @param callback $callback
     */
    function setFlowNameCallback($callback)
    {
        $this->_flowNameCallback = $callback;
    }

    // }}}
    // {{{ setCacheDirectory()

    /**
     * Sets a cache directory for the flow definitions.
     *
     * @param string $cacheDirectory
     */
    function setCacheDirectory($cacheDirectory)
    {
        $this->_cacheDirectory = $cacheDirectory;
    }

    // }}}
    // {{{ setAttribute()

    /**
     * Sets an attribute for the current flow.
     *
     * @param string $name
     * @param mixed  $value
     * @throws PIECE_FLOW_ERROR_INVALID_OPERATION
     */
    function setAttribute($name, $value)
    {
        if (!$this->_activated()) {
            Piece_Flow_Error::push(PIECE_FLOW_ERROR_INVALID_OPERATION,
                                   __FUNCTION__ . ' method must be called after starting/continuing flows.'
                                   );
            return;
        }

        $this->_flowExecutions[$this->_currentFlowExecutionTicket]->setAttribute($name, $value);
    }

    // }}}
    // {{{ hasAttribute()

    /**
     * Returns whether this flow has an attribute with a given name.
     *
     * @param string $name
     * @return boolean
     * @throws PIECE_FLOW_ERROR_INVALID_OPERATION
     */
    function hasAttribute($name)
    {
        if (!$this->_activated()) {
            Piece_Flow_Error::push(PIECE_FLOW_ERROR_INVALID_OPERATION,
                                   __FUNCTION__ . ' method must be called after starting/continuing flows.'
                                   );
            return;
        }

        return $this->_flowExecutions[$this->_currentFlowExecutionTicket]->hasAttribute($name);
    }

    // }}}
    // {{{ getAttribute()

    /**
     * Gets an attribute for the current flow.
     *
     * @param string $name
     * @return mixed
     * @throws PIECE_FLOW_ERROR_INVALID_OPERATION
     */
    function &getAttribute($name)
    {
        if (!$this->_activated()) {
            Piece_Flow_Error::push(PIECE_FLOW_ERROR_INVALID_OPERATION,
                                   __FUNCTION__ . ' method must be called after starting/continuing flows.'
                                   );
            $return = null;
            return $return;
        }

        return $this->_flowExecutions[$this->_currentFlowExecutionTicket]->getAttribute($name);
    }

    // }}}
    // {{{ shutdown()

    /**
     * Shutdown the continuation server for next events.
     *
     * @static
     */
    function shutdown()
    {
        for ($i = 0, $count = count($GLOBALS['PIECE_FLOW_Continuation_ActiveInstances']); $i < $count; ++$i) {
            $instance = &$GLOBALS['PIECE_FLOW_Continuation_ActiveInstances'][$i];
            if (!is_a($instance, __CLASS__)) {
                unset($GLOBALS['PIECE_FLOW_Continuation_ActiveInstances'][$i]);
                continue;
            }
            $instance->clear();
        }
    }

    // }}}
    // {{{ clear()

    /**
     * Clears some properties for the next use.
     */
    function clear()
    {
        if (array_key_exists($this->_currentFlowExecutionTicket, $this->_flowExecutions)
            && !$this->_enableSingleFlowMode
            && $this->_flowExecutions[$this->_currentFlowExecutionTicket]->isFinalState()
            ) {
            $this->_removeFlowExecution($this->_currentFlowExecutionTicket, $this->_currentFlowName);
        }

        $this->_isFirstTime = null;
        $this->_currentFlowName = null;
        $this->_currentFlowExecutionTicket = null;
        $this->_activated = false;
        if ($this->_enableGC) {
            $this->_gc->sweep();
        }
    }

    // }}}
    // {{{ getCurrentFlowExecutionTicket()

    /**
     * Gets the current flow execution ticket for the current flow.
     *
     * @return string
     * @throws PIECE_FLOW_ERROR_INVALID_OPERATION
     */
    function getCurrentFlowExecutionTicket()
    {
        if (!$this->_activated()) {
            Piece_Flow_Error::push(PIECE_FLOW_ERROR_INVALID_OPERATION,
                                   __FUNCTION__ . ' method must be called after starting/continuing flows.'
                                   );
            return;
        }

        return $this->_currentFlowExecutionTicket;
    }

    // }}}
    // {{{ setAttributeByRef()

    /**
     * Sets an attribute by reference for the current flow.
     *
     * @param string $name
     * @param mixed  &$value
     * @throws PIECE_FLOW_ERROR_INVALID_OPERATION
     * @since Method available since Release 1.6.0
     */
    function setAttributeByRef($name, &$value)
    {
        if (!$this->_activated()) {
            Piece_Flow_Error::push(PIECE_FLOW_ERROR_INVALID_OPERATION,
                                   __FUNCTION__ . ' method must be called after starting/continuing flows.'
                                   );
            return;
        }

        $this->_flowExecutions[$this->_currentFlowExecutionTicket]->setAttributeByRef($name, $value);
    }

    // }}}
    // {{{ getFlowExecutionTicketByFlowName()

    /**
     * Gets a flow execution ticket by the given flow name.
     * This method will be used for getting flow execution ticket else than
     * the current flow execution.
     * This method is only available if the flow execution is exclusive.
     *
     * @param string $flowName
     * @return string
     * @since Method available since Release 1.7.0
     */
    function getFlowExecutionTicketByFlowName($flowName)
    {
        return @$this->_exclusiveFlowExecutionTicketsByFlowName[$flowName];
    }

    // }}}
    // {{{ isExclusive()

    /**
     * Checks whether the curent flow execution is exclusive or not.
     *
     * @return boolean
     * @since Method available since Release 1.8.0
     * @deprecated Method deprecated in Release 1.14.0
     */
    function isExclusive()
    {
        return $this->_isExclusive();
    }

    // }}}
    // {{{ getCurrentFlowName()

    /**
     * Gets the current flow name.
     *
     * @return string
     * @throws PIECE_FLOW_ERROR_INVALID_OPERATION
     */
    function getCurrentFlowName()
    {
        if (!$this->_activated()) {
            Piece_Flow_Error::push(PIECE_FLOW_ERROR_INVALID_OPERATION,
                                   __FUNCTION__ . ' method must be called after starting/continuing flows.'
                                   );
            return;
        }

        return $this->_currentFlowName;
    }

    // }}}
    // {{{ disableFlowExecution()

    /**
     * Disables the flow execution for the given flow execution ticket.
     *
     * @param string $flowExecutionTicket
     * @since Method available since Release 1.11.0
     */
    function disableFlowExecution($flowExecutionTicket)
    {
        $this->_flowExecutions[$flowExecutionTicket] = null;
    }

    // }}}
    // {{{ checkLastEvent()

    /**
     * Returns whether the last event which is given by a user is valid or
     * not.
     *
     * @return boolean
     * @since Method available since Release 1.13.0
     */
    function checkLastEvent()
    {
        if (!$this->_activated()) {
            return true;
        }

        return $this->_flowExecutions[$this->_currentFlowExecutionTicket]->checkLastEvent();
    }

    /**#@-*/

    /**#@+
     * @access private
     */

    // }}}
    // {{{ _generateFlowExecutionTicket()

    /**
     * Generates a flow execution ticket.
     */
    function _generateFlowExecutionTicket()
    {
        return sha1(uniqid(mt_rand(), true));
    }

    // }}}
    // {{{ _prepare()

    /**
     * Prepares a flow execution ticket, a flow name, and whether the
     * flow invocation is the first time or not.
     *
     * @throws PIECE_FLOW_ERROR_FLOW_ID_NOT_GIVEN
     */
    function _prepare()
    {
        $this->_currentFlowExecutionTicket = call_user_func($this->_flowExecutionTicketCallback);
        if ($this->_hasFlowExecutionTicket($this->_currentFlowExecutionTicket)) {
            $flowName = $this->_getFlowName();
            if (!$this->_enableSingleFlowMode) {
                if (is_null($flowName) || !strlen($flowName)) {
                    Piece_Flow_Error::push(PIECE_FLOW_ERROR_FLOW_ID_NOT_GIVEN,
                                           'A flow name must be given in this case.'
                                           );
                    return;
                }
            }

            $this->_currentFlowName = $flowName;
            $this->_isFirstTime = false;
        } else {
            $flowName = $this->_getFlowName();
            if (is_null($flowName) || !strlen($flowName)) {
                Piece_Flow_Error::push(PIECE_FLOW_ERROR_FLOW_ID_NOT_GIVEN,
                                       'A flow name must be given in this case.'
                                       );
                return;
            }

            if (array_key_exists($flowName, $this->_exclusiveFlowExecutionTicketsByFlowName)) {
                trigger_error("Another flow execution of the current flow [ $flowName ] already exists in the flow executions. Starting a new flow execution.",
                              E_USER_WARNING
                              );
                $this->_removeFlowExecution($this->getFlowExecutionTicketByFlowName($flowName), $flowName);
            }

            $this->_currentFlowName = $flowName;
            $this->_isFirstTime = true;
        }
    }

    // }}}
    // {{{ _continue()

    /**
     * Continues a flow execution.
     *
     * @param mixed   &$payload
     * @param boolean $bindActionsWithFlowExecution
     * @throws PIECE_FLOW_ERROR_FLOW_EXECUTION_EXPIRED
     */
    function _continue(&$payload, $bindActionsWithFlowExecution)
    {
        if ($this->_enableGC) {
            if ($this->_gc->isMarked($this->_currentFlowExecutionTicket)) {
                $this->_removeFlowExecution($this->_currentFlowExecutionTicket, $this->_currentFlowName);
                Piece_Flow_Error::push(PIECE_FLOW_ERROR_FLOW_EXECUTION_EXPIRED,
                                       'The flow execution for the given flow execution ticket has expired.'
                                       );
                return;
            }
        }

        $this->_activated = true;
        $this->_flowExecutions[$this->_currentFlowExecutionTicket]->setPayload($payload);

        if ($bindActionsWithFlowExecution) {
            Piece_Flow_Action_Factory::setInstances($this->_flowExecutions[$this->_currentFlowExecutionTicket]->getAttribute('_actionInstances'));
        }

        $this->_flowExecutions[$this->_currentFlowExecutionTicket]->triggerEvent(call_user_func($this->_eventNameCallback));
    }

    // }}}
    // {{{ _start()

    /**
     * Starts a flow execution.
     *
     * @param mixed &$payload
     * @return string
     * @throws PIECE_FLOW_ERROR_NOT_FOUND
     */
    function _start(&$payload)
    {
        if (!array_key_exists($this->_currentFlowName, $this->_flowDefinitions)) {
            Piece_Flow_Error::push(PIECE_FLOW_ERROR_NOT_FOUND,
                                   "The flow name [ {$this->_currentFlowName} ] not found in the flow definitions."
                                   );
            return;
        }

        $flow = &new Piece_Flow();
        $flow->configure($this->_flowDefinitions[$this->_currentFlowName]['file'],
                         null,
                         $this->_cacheDirectory
                         );
        if (Piece_Flow_Error::hasErrors()) {
            return;
        }

        while (true) {
            $flowExecutionTicket = $this->_generateFlowExecutionTicket();
            if (!$this->_hasFlowExecutionTicket($flowExecutionTicket)) {
                $this->_flowExecutions[$flowExecutionTicket] = &$flow;
                break;
            }
        }

        $this->_currentFlowExecutionTicket = $flowExecutionTicket;
        $this->_activated = true;
        $flow->setPayload($payload);
        $flow->start();
        if (Piece_Flow_Error::hasErrors()) {
            return;
        }

        if ($this->_isExclusive()) {
            $this->_exclusiveFlowExecutionTicketsByFlowName[$this->_currentFlowName] = $flowExecutionTicket;
            $this->_exclusiveFlowNamesByFlowExecutionTicket[$flowExecutionTicket] = $this->_currentFlowName;
        }

        return $flowExecutionTicket;
    }

    // }}}
    // {{{ _activated()

    /**
     * Returns whether the current flow has activated or not.
     *
     * @return boolean
     */
    function _activated()
    {
        return $this->_activated;
    }

    // }}}
    // {{{ _hasFlowExecutionTicket()

    /**
     * Returns whether the current flow has the flow execution ticket or not.
     *
     * @param string $flowExecutionTicket
     * @return boolean
     */
    function _hasFlowExecutionTicket($flowExecutionTicket)
    {
        return array_key_exists($flowExecutionTicket, $this->_flowExecutions);
    }

    // }}}
    // {{{ _getFlowName()

    /**
     * Gets a flow name which will be started or continued.
     *
     * @return string
     */
    function _getFlowName()
    {
        if (!$this->_enableSingleFlowMode) {
            return call_user_func($this->_flowNameCallback);
        } else {
            $flowNames = array_keys($this->_flowDefinitions);
            return $flowNames[0];
        }
    }

    // }}}
    // {{{ _removeFlowExecution()

    /**
     * Removes a flow execution.
     *
     * @param string $flowExecutionTicket
     * @param string $flowName
     */
    function _removeFlowExecution($flowExecutionTicket, $flowName)
    {
        $this->_flowExecutions[$flowExecutionTicket] = null;
        unset($this->_flowExecutions[$flowExecutionTicket]);
        if (array_key_exists($flowName, $this->_exclusiveFlowExecutionTicketsByFlowName)) {
            unset($this->_exclusiveFlowExecutionTicketsByFlowName[$flowName]);
            unset($this->_exclusiveFlowNamesByFlowExecutionTicket[$flowExecutionTicket]);
        }
    }

    // }}}
    // {{{ _isExclusive()

    /**
     * Checks whether the curent flow execution is exclusive or not.
     *
     * @return boolean
     * @since Method available since Release 1.14.0
     */
    function _isExclusive()
    {
        if (!$this->_enableSingleFlowMode) {
            return $this->_flowDefinitions[$this->_currentFlowName]['isExclusive'];
        } else {
            return true;
        }
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
