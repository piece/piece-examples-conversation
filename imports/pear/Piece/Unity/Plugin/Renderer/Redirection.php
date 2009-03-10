<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP versions 4 and 5
 *
 * Copyright (c) 2006-2009 KUBO Atsuhiro <kubo@iteman.jp>,
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
 * @package    Piece_Unity
 * @copyright  2006-2009 KUBO Atsuhiro <kubo@iteman.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    GIT: $Id: a914843d8360400ef4ed9b0d8be41f313939e5e5 $
 * @since      File available since Release 0.6.0
 */

require_once 'Piece/Unity/Plugin/Common.php';
require_once 'Piece/Unity/URI.php';

// {{{ Piece_Unity_Plugin_Renderer_Redirection

/**
 * A renderer which is used to redirect requests.
 *
 * @package    Piece_Unity
 * @copyright  2006-2009 KUBO Atsuhiro <kubo@iteman.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: 1.7.1
 * @since      Class available since Release 0.6.0
 */
class Piece_Unity_Plugin_Renderer_Redirection extends Piece_Unity_Plugin_Common
{

    // {{{ properties

    /**#@+
     * @access public
     */

    /**#@-*/

    /**#@+
     * @access private
     */

    var $_uri;

    /**#@-*/

    /**#@+
     * @access public
     */

    // }}}
    // {{{ invoke()

    /**
     * Invokes the plugin specific code.
     */
    function invoke()
    {
        $this->_replaceSelfNotation();
        $this->_uri = $this->_buildURI();

        if (!headers_sent() && !is_null($this->_uri)) {
            header("Location: {$this->_uri}");
        }
    }

    /**#@-*/

    /**#@+
     * @access private
     */

    // }}}
    // {{{ _initialize()

    /**
     * Defines and initializes extension points and configuration points.
     *
     * @since Method available since Release 0.6.0
     */
    function _initialize()
    {
        $this->_addConfigurationPoint('addSessionID', false);
        $this->_addConfigurationPoint('isExternal', false);
        $this->_addConfigurationPoint('addFlowExecutionTicket', false);
    }

    // }}}
    // {{{ _replaceSelfNotation()

    /**
     * @since Method available since Release 1.5.0
     */
    function _replaceSelfNotation()
    {
        $viewString = $this->_context->getView();
        if (preg_match('!^selfs?://(.*)!', $viewString, $matches)) {
            $config = &$this->_context->getConfiguration();
            $config->setConfiguration('Renderer_Redirection', 'addFlowExecutionTicket', true);
            if (substr($viewString, 0, 7) == 'self://') {
                $this->_context->setView('http://example.org' . $this->_context->getScriptName() . '?' . $matches[1]);
            } elseif (substr($viewString, 0, 8) == 'selfs://') {
                $this->_context->setView('https://example.org' . $this->_context->getScriptName() . '?' . $matches[1]);
            }
        }
    }

    // }}}
    // {{{ _buildURI()

    /**
     * @since Method available since Release 1.5.0
     */
    function _buildURI()
    {
        $isExternal = $this->_getConfiguration('isExternal');
        $viewString = $this->_context->getView();
        $uri = &new Piece_Unity_URI($viewString, $isExternal, true);

        $viewElement = &$this->_context->getViewElement();
        $viewElements = $viewElement->getElements();
        $queryString = $uri->getQueryString();
        foreach (array_keys($queryString) as $elementName) {
            if (array_key_exists($elementName, $viewElements)
                && is_scalar($viewElements[$elementName])
                ) {
                $uri->addQueryString($elementName,
                                     $viewElements[$elementName]
                                     );
            }
        }

        if (!$isExternal) {
            if ($this->_getConfiguration('addSessionID')) {
                $uri->addQueryString($viewElements['__sessionName'],
                                     $viewElements['__sessionID']
                                     );
            }

            if ($this->_getConfiguration('addFlowExecutionTicket')) {
                if (array_key_exists('__flowExecutionTicketKey', $viewElements)) {
                    $uri->addQueryString($viewElements['__flowExecutionTicketKey'],
                                         $viewElements['__flowExecutionTicket']
                                         );
                }
            }

            /*
             * Replaces __eventNameKey with the event name key.
             */
            if (array_key_exists('__eventNameKey', $queryString)) {
                $uri->removeQueryString('__eventNameKey');
                $uri->addQueryString($this->_context->getEventNameKey(), $queryString['__eventNameKey']);
            }
        }

        return $uri->getURI('pass');
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
