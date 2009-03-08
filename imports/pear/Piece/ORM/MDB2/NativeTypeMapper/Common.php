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
 * @version    SVN: $Id: Common.php 413 2008-07-12 05:27:38Z iteman $
 * @since      File available since Release 0.7.0
 */

// {{{ GLOBALS

$GLOBALS['PIECE_ORM_MDB2_NativeTypeMap'] = array();

// }}}
// {{{ Piece_ORM_MDB2_NativeTypeMapper_Common

/**
 * A helper class to map native datatypes of the DBMS to MDB2 datatypes.
 *
 * @package    Piece_ORM
 * @copyright  2007-2008 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: 1.2.0
 * @since      Class available since Release 0.7.0
 */
class Piece_ORM_MDB2_NativeTypeMapper_Common
{

    // {{{ properties

    /**#@+
     * @access public
     */

    /**#@-*/

    /**#@+
     * @access private
     */

    var $_driverName;

    /**#@-*/

    /**#@+
     * @access public
     */

    // }}}
    // {{{ constructor

    /**
     * Sets the driver name to the property.
     */
    function Piece_ORM_MDB2_NativeTypeMapper_Common()
    {
        $this->_driverName = strtolower(substr(strrchr(get_class($this), '_'), 1));
    }

    // }}}
    // {{{ mapNativeType()

    /**
     * Maps a native datatype of the DBMS to a MDB2 datatype.
     *
     * @param MDB2_Driver_Common &$dbh
     */
    function mapNativeType(&$dbh)
    {
        if (!array_key_exists($this->_driverName, $GLOBALS['PIECE_ORM_MDB2_NativeTypeMap'])) {
            return;
        }

        $callbacks = array();
        foreach (array_keys($GLOBALS['PIECE_ORM_MDB2_NativeTypeMap'][ $this->_driverName ]) as $type) {
            $callbacks[$type] = array(&$this, 'getMDB2TypeInfo');
        }

        $dbh->setOption('nativetype_map_callback', $callbacks);
    }

    // }}}
    // {{{ getMDB2TypeInfo()

    /**
     * Gets the MDB2 datatype information of a native array description of
     * a field.
     *
     * @param MDB2_Driver_Common &$dbh
     * @param array              $field
     * @return array
     */
    function getMDB2TypeInfo(&$dbh, $field)
    {
        return array(array($GLOBALS['PIECE_ORM_MDB2_NativeTypeMap'][ $this->_driverName ][ $field['type'] ]),
                     null,
                     null,
                     null
                     );
    }

    // }}}
    // {{{ addMap()

    /**
     * Adds an element to the map.
     *
     * @param string $driverName
     * @param string $nativeType
     * @param string $mdb2Type
     * @static
     */
    function addMap($driverName, $nativeType, $mdb2Type)
    {
        $GLOBALS['PIECE_ORM_MDB2_NativeTypeMap'][$driverName][$nativeType] = $mdb2Type;
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
