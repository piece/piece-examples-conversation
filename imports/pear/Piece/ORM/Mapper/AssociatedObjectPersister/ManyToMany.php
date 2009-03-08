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
 * @version    SVN: $Id: ManyToMany.php 436 2008-08-30 17:00:06Z iteman $
 * @since      File available since Release 0.2.0
 */

require_once 'Piece/ORM/Mapper/AssociatedObjectPersister/Common.php';
require_once 'Piece/ORM/Mapper/Factory.php';
require_once 'Piece/ORM/Error.php';
require_once 'Piece/ORM/Inflector.php';

// {{{ Piece_ORM_Mapper_AssociatedObjectPersister_ManyToMany

/**
 * An associated object persister for Many-to-Many relationships.
 *
 * @package    Piece_ORM
 * @copyright  2007-2008 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: 1.2.0
 * @since      Class available since Release 0.2.0
 */
class Piece_ORM_Mapper_AssociatedObjectPersister_ManyToMany extends Piece_ORM_Mapper_AssociatedObjectPersister_Common
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

        if (!is_array($this->_subject->$relationship['mappedAs'])) {
            return;
        }

        $mapper = &Piece_ORM_Mapper_Factory::factory($relationship['through']['table']);
        if (Piece_ORM_Error::hasErrors()) {
            return;
        }

        $referencedColumnValue = $this->_subject->{ Piece_ORM_Inflector::camelize($relationship['through']['referencedColumn'], true) };
        $object = &$mapper->createObject();
        foreach ($this->_subject->$relationship['mappedAs'] as $associatedObject) {
            $object->{ Piece_ORM_Inflector::camelize($relationship['through']['column'], true) } = $referencedColumnValue;
            $object->{ Piece_ORM_Inflector::camelize($relationship['through']['inverseColumn'], true) } = $associatedObject->{ Piece_ORM_Inflector::camelize($relationship['column'], true) };
            $mapper->insert($object);
            if (Piece_ORM_Error::hasErrors()) {
                return;
            }
        }
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

        if (!is_array($this->_subject->$relationship['mappedAs'])) {
            return;
        }

        $mapper = &Piece_ORM_Mapper_Factory::factory($relationship['through']['table']);
        if (Piece_ORM_Error::hasErrors()) {
            return;
        }

        $referencedColumnValue = $this->_subject->{ Piece_ORM_Inflector::camelize($relationship['through']['referencedColumn'], true) };
        $mapper->executeQuery("DELETE FROM {$relationship['through']['table']} WHERE {$relationship['through']['column']} = " . $mapper->quote($referencedColumnValue, $relationship['through']['column']), true);
        if (Piece_ORM_Error::hasErrors()) {
            return;
        }

        $object = &$mapper->createObject();
        foreach ($this->_subject->$relationship['mappedAs'] as $associatedObject) {
            $object->{ Piece_ORM_Inflector::camelize($relationship['through']['column'], true) } = $referencedColumnValue;
            $object->{ Piece_ORM_Inflector::camelize($relationship['through']['inverseColumn'], true) } = $associatedObject->{ Piece_ORM_Inflector::camelize($relationship['column'], true) };
            $mapper->insert($object);
            if (Piece_ORM_Error::hasErrors()) {
                return;
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
        $property = Piece_ORM_Inflector::camelize($relationship['through']['referencedColumn'], true);
        if (!array_key_exists($property, $this->_subject)) {
            return;
        }

        $mapper = &Piece_ORM_Mapper_Factory::factory($relationship['through']['table']);
        if (Piece_ORM_Error::hasErrors()) {
            return;
        }

        $mapper->executeQuery("DELETE FROM {$relationship['through']['table']} WHERE {$relationship['through']['column']} = " .
                              $mapper->quote($this->_subject->$property, $relationship['through']['column']),
                              true
                              );
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
