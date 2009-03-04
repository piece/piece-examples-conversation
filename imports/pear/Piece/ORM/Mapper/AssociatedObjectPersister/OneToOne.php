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
 * @version    SVN: $Id: OneToOne.php 416 2008-07-12 06:41:48Z iteman $
 * @since      File available since Release 0.2.0
 */

require_once 'Piece/ORM/Mapper/AssociatedObjectPersister/Common.php';
require_once 'Piece/ORM/Mapper/Factory.php';
require_once 'Piece/ORM/Error.php';
require_once 'Piece/ORM/Inflector.php';

// {{{ Piece_ORM_Mapper_AssociatedObjectPersister_OneToOne

/**
 * An associated object persister for One-to-One relationships.
 *
 * @package    Piece_ORM
 * @copyright  2007-2008 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: 1.2.0
 * @since      Class available since Release 0.2.0
 */
class Piece_ORM_Mapper_AssociatedObjectPersister_OneToOne extends Piece_ORM_Mapper_AssociatedObjectPersister_Common
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

    // }}}
    // {{{ insert()

    /**
     * Inserts associated objects to a table.
     *
     * @param array $relationship
     */
    function insert($relationship)
    {
        if (!array_key_exists($relationship['mappedAs'], $this->_subject)) {
            return;
        }

        if (!is_object($this->_subject->$relationship['mappedAs'])) {
            return;
        }

        $mapper = &Piece_ORM_Mapper_Factory::factory($relationship['table']);
        if (Piece_ORM_Error::hasErrors()) {
            return;
        }

        $this->_subject->{ $relationship['mappedAs'] }->{ Piece_ORM_Inflector::camelize($relationship['column'], true) } = $this->_subject->{ Piece_ORM_Inflector::camelize($relationship['referencedColumn'], true) };
        $mapper->insert($this->_subject->{ $relationship['mappedAs'] });
    }

    // }}}
    // {{{ update()

    /**
     * Updates associated objects in a table.
     *
     * @param array $relationship
     */
    function update($relationship)
    {
        if (!array_key_exists($relationship['mappedAs'], $this->_subject)) {
            return;
        }

        if (!is_null($this->_subject->$relationship['mappedAs']) && !is_object($this->_subject->$relationship['mappedAs'])) {
            return;
        }

        $mapper = &Piece_ORM_Mapper_Factory::factory($relationship['table']);
        if (Piece_ORM_Error::hasErrors()) {
            return;
        }

        $referencedColumnValue = $this->_subject->{ Piece_ORM_Inflector::camelize($relationship['referencedColumn'], true) };
        $oldObject = $mapper->findWithQuery("SELECT * FROM {$relationship['table']} WHERE {$relationship['column']} = " . $mapper->quote($referencedColumnValue, $relationship['column']));
        if (Piece_ORM_Error::hasErrors()) {
            return;
        }

        if (is_null($oldObject)) {
            if (!is_null($this->_subject->$relationship['mappedAs'])) {
                $this->_subject->$relationship['mappedAs']->{ Piece_ORM_Inflector::camelize($relationship['column'], true) } = $referencedColumnValue;
                $mapper->insert($this->_subject->$relationship['mappedAs']);
            }
        } else {
            if (!is_null($this->_subject->$relationship['mappedAs'])) {
                $mapper->update($this->_subject->$relationship['mappedAs']);
            } else {
                $mapper->delete($oldObject);
            }
        }
    }

    // }}}
    // {{{ delete()

    /**
     * Removes associated objects from a table.
     *
     * @param array $relationship
     */
    function delete($relationship)
    {
        if (!array_key_exists($relationship['mappedAs'], $this->_subject)) {
            return;
        }

        if (!is_null($this->_subject->$relationship['mappedAs']) && !is_object($this->_subject->$relationship['mappedAs'])) {
            return;
        }

        $mapper = &Piece_ORM_Mapper_Factory::factory($relationship['table']);
        if (Piece_ORM_Error::hasErrors()) {
            return;
        }

        $mapper->delete($this->_subject->$relationship['mappedAs']);
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
