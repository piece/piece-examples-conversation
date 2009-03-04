<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP versions 4 and 5
 *
 * Copyright (c) 2007-2008 KUBO Atsuhiro <iteman@users.sourceforge.net>,
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
 * @copyright  2007-2008 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    SVN: $Id: InflectorTestCase.php 413 2008-07-12 05:27:38Z iteman $
 * @since      File available since Release 0.1.0
 */

require_once realpath(dirname(__FILE__) . '/../../prepare.php');
require_once 'PHPUnit.php';
require_once 'Piece/ORM/Inflector.php';

// {{{ Piece_ORM_InflectorTestCase

/**
 * TestCase for Piece_ORM_Inflector
 *
 * @package    Piece_ORM
 * @copyright  2007-2008 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: 1.2.0
 * @since      Class available since Release 0.1.0
 */
class Piece_ORM_InflectorTestCase extends PHPUnit_TestCase
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

    function testWordShouldUnderscore()
    {
        $this->assertEquals('keyword_id', Piece_ORM_Inflector::underscore('keywordId'));
        $this->assertEquals('keyword1_id', Piece_ORM_Inflector::underscore('keyword1_id'));
        $this->assertEquals('keyword1_id', Piece_ORM_Inflector::underscore('keyword1Id'));
        $this->assertEquals('foo_123', Piece_ORM_Inflector::underscore('Foo_123'));
        $this->assertEquals('foo1_23', Piece_ORM_Inflector::underscore('Foo1_23'));
        $this->assertEquals('foo123', Piece_ORM_Inflector::underscore('Foo123'));
        $this->assertEquals('unusualname1_2_unusualname_12', Piece_ORM_Inflector::underscore('Unusualname1_2_unusualname_12'));
        $this->assertEquals('unusualname1_2_unusualname_12', Piece_ORM_Inflector::underscore('Unusualname1_2Unusualname_12'));
    }

    /**
     * @since Method available since Release 0.8.1
     */
    function testWordShouldCamelize()
    {
        $this->assertEquals('keywordId', Piece_ORM_Inflector::camelize('keyword_id', true));
        $this->assertEquals('keyword1_id', Piece_ORM_Inflector::camelize('keyword1_id', true));
        $this->assertEquals('Foo_123', Piece_ORM_Inflector::camelize('foo_123', false));
        $this->assertEquals('Foo1_23', Piece_ORM_Inflector::camelize('foo1_23', false));
        $this->assertEquals('Foo123', Piece_ORM_Inflector::camelize('foo123', false));
        $this->assertEquals('foo_123', Piece_ORM_Inflector::camelize('foo_123', true));
        $this->assertEquals('foo1_23', Piece_ORM_Inflector::camelize('foo1_23', true));
        $this->assertEquals('foo123', Piece_ORM_Inflector::camelize('foo123', true));
        $this->assertEquals('Unusualname1_2_unusualname_12', Piece_ORM_Inflector::camelize('unusualname1_2_unusualname_12', false));
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
