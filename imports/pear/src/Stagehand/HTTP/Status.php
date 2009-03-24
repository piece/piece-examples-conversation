<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP version 5
 *
 * Copyright (c) 2009 KUBO Atsuhiro <kubo@iteman.jp>,
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
 * @package    Stagehand_HTTP_Status
 * @copyright  2009 KUBO Atsuhiro <kubo@iteman.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @version    Release: 1.1.0
 * @link       http://www.studyinghttp.net/cgi-bin/rfc.cgi?2616#Sec6.1.1
 * @since      File available since Release 1.0.0
 */

// {{{ Stagehand_HTTP_Status

/**
 * A utility which can be used to send a HTTP status line from a status code for
 * the current HTTP response.
 *
 * @package    Stagehand_HTTP_Status
 * @copyright  2009 KUBO Atsuhiro <kubo@iteman.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @version    Release: 1.1.0
 * @link       http://www.studyinghttp.net/cgi-bin/rfc.cgi?2616#Sec6.1.1
 * @since      Class available since Release 1.0.0
 */
class Stagehand_HTTP_Status
{

    // {{{ properties

    /**#@+
     * @access public
     */

    /**#@-*/

    /**#@+
     * @access protected
     */

    /**#@-*/

    /**#@+
     * @access private
     */

    private static $_statusCodes = array(100 => 'Continue',
                                         101 => 'Switching Protocols',
                                         200 => 'OK',
                                         201 => 'Created',
                                         202 => 'Accepted',
                                         203 => 'Non-Authoritative Information',
                                         204 => 'No Content',
                                         205 => 'Reset Content',
                                         206 => 'Partial Content',
                                         300 => 'Multiple Choices',
                                         301 => 'Moved Permanently',
                                         302 => 'Found',
                                         303 => 'See Other',
                                         304 => 'Not Modified',
                                         305 => 'Use Proxy',
                                         307 => 'Temporary Redirect',
                                         400 => 'Bad Request',
                                         401 => 'Unauthorized',
                                         402 => 'Payment Required',
                                         403 => 'Forbidden',
                                         404 => 'Not Found',
                                         405 => 'Method Not Allowed',
                                         406 => 'Not Acceptable',
                                         407 => 'Proxy Authentication Required',
                                         408 => 'Request Time-out',
                                         409 => 'Conflict',
                                         410 => 'Gone',
                                         411 => 'Length Required',
                                         412 => 'Precondition Failed',
                                         413 => 'Request Entity Too Large',
                                         414 => 'Request-URI Too Large',
                                         415 => 'Unsupported Media Type',
                                         416 => 'Requested range not satisfiable',
                                         417 => 'Expectation Failed',
                                         500 => 'Internal Server Error',
                                         501 => 'Not Implemented',
                                         502 => 'Bad Gateway',
                                         503 => 'Service Unavailable',
                                         504 => 'Gateway Time-out',
                                         505 => 'HTTP Version not supported'
                                         );
    private static $_sentStatusLine;

    /**#@-*/

    /**#@+
     * @access public
     */

    // }}}
    // {{{ send()

    /**
     * Sends a HTTP status line like "HTTP/1.1 404 Not Found".
     *
     * @param integer $statusCode
     * @throws Stagehand_HTTP_Status_Exception
     */
    public static function send($statusCode)
    {
        if (!array_key_exists($statusCode, self::$_statusCodes)) {
            throw new Stagehand_HTTP_Status_Exception("Unknown status code [ $statusCode ], be sure the status code is correct");
        }

        @header(self::_createStatusLine($statusCode));
    }

    /**#@-*/

    /**#@+
     * @access protected
     */

    /**#@-*/

    /**#@+
     * @access private
     */

    // }}}
    // {{{ _createStatusLine()

    /**
     * Creates a HTTP status line like "HTTP/1.1 404 Not Found".
     *
     * @param integer $statusCode
     */
    private static function _createStatusLine($statusCode)
    {
        self::$_sentStatusLine = sprintf('%s %d %s',
                                         $_SERVER['SERVER_PROTOCOL'],
                                         $statusCode,
                                         self::$_statusCodes[ $statusCode ]
                                         );
        return self::$_sentStatusLine;
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
