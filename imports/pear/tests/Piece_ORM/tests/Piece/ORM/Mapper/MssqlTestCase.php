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
 * @version    SVN: $Id: MssqlTestCase.php 427 2008-08-11 15:03:38Z iteman $
 * @since      File available since Release 0.4.0
 */

if (substr(PHP_OS, 0, 3) != 'WIN') {
    return;
}

require_once dirname(__FILE__) . '/CompatibilityTests.php';

// {{{ Piece_ORM_Mapper_MssqlTestCase

/**
 * TestCase for Microsoft SQL Server.
 *
 * @package    Piece_ORM
 * @copyright  2007-2008 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: 1.2.0
 * @since      Class available since Release 0.4.0
 */
class Piece_ORM_Mapper_MssqlTestCase extends Piece_ORM_Mapper_CompatibilityTests
{

    // {{{ properties

    /**#@+
     * @access public
     */

    /**#@-*/

    /**#@+
     * @access private
     */

    var $_dsn = 'mssql://piece:piece@pieceorm:1433/piece';

    /**#@-*/

    /**#@+
     * @access public
     */

    /**
     * @since Method available since Release 1.0.0
     */
    function testShouldSetAFunctionToGetTheCurrentTimestampToTheCreatedatFieldWhenExecutingInsert() {}

    /**
     * @since Method available since Release 1.2.0
     */
    function testShouldProvideTheDefaultValueOfAGivenField()
    {
        $mapper = &Piece_ORM_Mapper_Factory::factory('Employees');

        $this->assertNull($mapper->getDefault('id'));
        $this->assertNull($mapper->getDefault('firstName'));
        $this->assertNull($mapper->getDefault('lastName'));
        $this->assertNull($mapper->getDefault('note'));
        $this->assertNull($mapper->getDefault('departmentsId'));
        $this->assertEquals('getdate', $mapper->getDefault('createdAt'));
        $this->assertEquals('getdate', $mapper->getDefault('updatedAt'));
        $this->assertEquals('0', $mapper->getDefault('lockVersion'));
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
