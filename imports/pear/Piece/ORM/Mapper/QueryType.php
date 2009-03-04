<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP versions 4 and 5
 *
 * Copyright (c) 2008 KUBO Atsuhiro <iteman@users.sourceforge.net>,
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
 * @package    Piece_ORM
 * @copyright  2008 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    SVN: $Id: QueryType.php 413 2008-07-12 05:27:38Z iteman $
 * @since      File available since Release 1.0.0
 */

// {{{ Piece_ORM_Mapper_QueryType

/**
 * A class to handle query types itself.
 *
 * @package    Piece_ORM
 * @copyright  2008 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: 1.2.0
 * @since      Class available since Release 1.0.0
 */
class Piece_ORM_Mapper_QueryType
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
     * @static
     */

    // }}}
    // {{{ isFindAll()

    /**
     * Checks whether or not the query type of a given method name is findAll.
     *
     * @param string $methodName
     * @return boolean
     */
    function isFindAll($methodName)
    {
        return (boolean)preg_match('/^findAll.*$/i', $methodName);
    }

    // }}}
    // {{{ isFindOne()

    /**
     * Checks whether or not the query type of a given method name is findOne.
     *
     * @param string $methodName
     * @return boolean
     */
    function isFindOne($methodName)
    {
        return (boolean)preg_match('/^findOne.+$/i', $methodName);
    }

    // }}}
    // {{{ isFind()

    /**
     * Checks whether or not the query type of a given method name is find.
     *
     * @param string $methodName
     * @return boolean
     */
    function isFind($methodName)
    {
        return (boolean)preg_match('/^find.+$/i', $methodName);
    }

    // }}}
    // {{{ isInsert()

    /**
     * Checks whether or not the query type of a given method name is insert.
     *
     * @param string $methodName
     * @return boolean
     */
    function isInsert($methodName)
    {
        return (boolean)preg_match('/^insert.*$/i', $methodName);
    }

    // }}}
    // {{{ isUpdate()

    /**
     * Checks whether or not the query type of a given method name is update.
     *
     * @param string $methodName
     * @return boolean
     */
    function isUpdate($methodName)
    {
        return (boolean)preg_match('/^update.*$/i', $methodName);
    }

    // }}}
    // {{{ isDelete()

    /**
     * Checks whether or not the query type of a given method name is delete.
     *
     * @param string $methodName
     * @return boolean
     */
    function isDelete($methodName)
    {
        return (boolean)preg_match('/^delete.*$/i', $methodName);
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
