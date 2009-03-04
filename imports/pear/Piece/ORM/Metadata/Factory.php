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
 * @version    SVN: $Id: Factory.php 532 2008-09-29 14:57:12Z iteman $
 * @since      File available since Release 0.1.0
 */

require_once 'MDB2.php';
require_once 'Piece/ORM/Metadata.php';
require_once 'Piece/ORM/Error.php';
require_once 'Cache/Lite.php';
require_once 'Piece/ORM/Context.php';
require_once 'PEAR.php';
require_once 'Piece/ORM/Inflector.php';
require_once 'Piece/ORM/Env.php';

// {{{ GLOBALS

$GLOBALS['PIECE_ORM_Metadata_Instances'] = array();
$GLOBALS['PIECE_ORM_Metadata_CacheDirectory'] = null;

// }}}
// {{{ Piece_ORM_Metadata_Factory

/**
 * A factory class to create a Piece_ORM_Metadata object for a table.
 *
 * @package    Piece_ORM
 * @copyright  2007-2008 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: 1.2.0
 * @since      Class available since Release 0.1.0
 */
class Piece_ORM_Metadata_Factory
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
    // {{{ factory()

    /**
     * Creates a Piece_ORM_Metadata object for the given table.
     *
     * @param string $tableName
     * @return Piece_ORM_Metadata
     * @static
     */
    function &factory($tableName)
    {
        $context = &Piece_ORM_Context::singleton();
        if (!$context->getUseMapperNameAsTableName()) {
            $tableName = Piece_ORM_Inflector::underscore($tableName);
        }

        $tableID = sha1($context->getDSN() . ".$tableName");
        if (!array_key_exists($tableID, $GLOBALS['PIECE_ORM_Metadata_Instances'])) {
            $metadata = &Piece_ORM_Metadata_Factory::_createMetadata($tableName, $tableID);
            if (Piece_ORM_Error::hasErrors()) {
                $return = null;
                return $return;
            }

            $GLOBALS['PIECE_ORM_Metadata_Instances'][$tableID] = &$metadata;
        }

        return $GLOBALS['PIECE_ORM_Metadata_Instances'][$tableID];
    }

    // }}}
    // {{{ clearInstances()

    /**
     * Clears the Piece_ORM_Metadata instances.
     */
    function clearInstances()
    {
        $GLOBALS['PIECE_ORM_Metadata_Instances'] = array();
    }

    // }}}
    // {{{ setCacheDirectory()

    /**
     * Sets a cache directory.
     *
     * @param string $cacheDirectory
     */
    function setCacheDirectory($cacheDirectory)
    {
        $GLOBALS['PIECE_ORM_Metadata_CacheDirectory'] = $cacheDirectory;
    }

    /**#@-*/

    /**#@+
     * @access private
     * @static
     */

    // }}}
    // {{{ _getMetadata()

    /**
     * Gets a Piece_ORM_Metadata object from a cache.
     *
     * @param string $tableName
     * @param string $tableID
     * @return Piece_ORM_Metadata
     */
    function &_getMetadata($tableName, $tableID)
    {
        $cache = &new Cache_Lite(array('cacheDir' => "{$GLOBALS['PIECE_ORM_Metadata_CacheDirectory']}/",
                                       'automaticSerialization' => true,
                                       'errorHandlingAPIBreak' => true)
                                 );

        if (!Piece_ORM_Env::isProduction()) {
            $cache->remove($tableID);
        }

        /*
         * The Cache_Lite class always specifies PEAR_ERROR_RETURN when
         * calling PEAR::raiseError in default.
         */
        $metadata = $cache->get($tableID);
        if (PEAR::isError($metadata)) {
            trigger_error("Cannot read the cache file in the directory [ {$GLOBALS['PIECE_ORM_Metadata_CacheDirectory']} ].",
                          E_USER_WARNING
                          );

            $metadata = &Piece_ORM_Metadata_Factory::_createMetadataFromDatabase($tableName);
            if (Piece_ORM_Error::hasErrors()) {
                $return = null;
                return $return;
            }

            return $metadata;
        }

        if (!$metadata) {
            $metadata = &Piece_ORM_Metadata_Factory::_createMetadataFromDatabase($tableName);
            if (Piece_ORM_Error::hasErrors()) {
                $return = null;
                return $return;
            }

            $result = $cache->save($metadata);
            if (PEAR::isError($result)) {
                trigger_error("Cannot write the Piece_ORM_Metadata object to the cache file in the directory [ {$GLOBALS['PIECE_ORM_Metadata_CacheDirectory']} ].",
                              E_USER_WARNING
                              );
            }
        }

        return $metadata;
    }

    // }}}
    // {{{ _createMetadataFromDatabase()

    /**
     * Creates a Piece_ORM_Metadata object from a database.
     *
     * @param string $tableName
     * @return Piece_ORM_Metadata
     * @throws PIECE_ORM_ERROR_CANNOT_INVOKE
     * @throws PIECE_ORM_ERROR_NOT_FOUND
     */
    function &_createMetadataFromDatabase($tableName)
    {
        $context = &Piece_ORM_Context::singleton();
        $dbh = &$context->getConnection();
        if (Piece_ORM_Error::hasErrors()) {
            $return = null;
            return $return;
        }

        PEAR::staticPushErrorHandling(PEAR_ERROR_RETURN);
        $result = $dbh->setLimit(1);
        PEAR::staticPopErrorHandling();
        if (MDB2::isError($result)) {
            Piece_ORM_Error::pushPEARError($result,
                                           PIECE_ORM_ERROR_CANNOT_INVOKE,
                                           "Failed to invoke MDB2_Driver_{$dbh->phptype}::setLimit() for any reasons."
                                           );
            $return = null;
            return $return;
        }

        PEAR::staticPushErrorHandling(PEAR_ERROR_RETURN);
        $result = $dbh->query('SELECT 1 FROM ' . $dbh->quoteIdentifier($tableName));
        PEAR::staticPopErrorHandling();
        if (MDB2::isError($result)) {
            if ($result->getCode() != MDB2_ERROR_NOSUCHTABLE) {
                Piece_ORM_Error::pushPEARError($result,
                                               PIECE_ORM_ERROR_CANNOT_INVOKE,
                                               "Failed to invoke MDB2_Driver_{$dbh->phptype}::query() for any reasons."
                                               );
                $return = null;
                return $return;
            }

            Piece_ORM_Error::pushPEARError($result,
                                           PIECE_ORM_ERROR_NOT_FOUND,
                                           "Failed to invoke MDB2_Driver_{$dbh->phptype}::query() for any reasons."
                                           );
            $return = null;
            return $return;
        }

        PEAR::staticPushErrorHandling(PEAR_ERROR_RETURN);
        $reverse = &$dbh->loadModule('Reverse');
        PEAR::staticPopErrorHandling();
        if (MDB2::isError($reverse)) {
            Piece_ORM_Error::pushPEARError($reverse,
                                           PIECE_ORM_ERROR_CANNOT_INVOKE,
                                           'Failed to invoke $dbh->loadModule() for any reasons.'
                                           );
            $return = null;
            return $return;
        }

        if ($dbh->phptype == 'mssql') {
            include_once 'Piece/ORM/MDB2/Decorator/Reverse/Mssql.php';
            $reverse = &new Piece_ORM_MDB2_Decorator_Reverse_Mssql($reverse);
        }

        PEAR::staticPushErrorHandling(PEAR_ERROR_RETURN);
        $tableInfo = $reverse->tableInfo($tableName);
        PEAR::staticPopErrorHandling();
        if (MDB2::isError($tableInfo)) {
            Piece_ORM_Error::pushPEARError($tableInfo,
                                           PIECE_ORM_ERROR_CANNOT_INVOKE,
                                           'Failed to invoke $reverse->tableInfo() for any reasons.'
                                           );
            $return = null;
            return $return;
        }

        if ($dbh->phptype == 'mysql') {
            foreach (array_keys($tableInfo) as $fieldName) {
                if ($tableInfo[$fieldName]['nativetype'] == 'datetime'
                    && $tableInfo[$fieldName]['notnull']
                    && $tableInfo[$fieldName]['default'] == '0000-00-00 00:00:00'
                    ) {
                    $tableInfo[$fieldName]['flags'] =
                        str_replace('default_0000-00-00%2000%3A00%3A00',
                                    '',
                                    $tableInfo[$fieldName]['flags']
                                    );
                    $tableInfo[$fieldName]['default'] = '';
                }
            }
        }

        $metadata = &new Piece_ORM_Metadata($tableInfo);
        return $metadata;
    }

    // }}}
    // {{{ _createMetadata()

    /**
     * Creates a Piece_ORM_Metadata object from a cache or a database.
     *
     * @param string $tableName
     * @param string $tableID
     * @return Piece_ORM_Metadata
     */
    function &_createMetadata($tableName, $tableID)
    {
        if (!file_exists($GLOBALS['PIECE_ORM_Metadata_CacheDirectory'])) {
            trigger_error("The cache directory [ {$GLOBALS['PIECE_ORM_Metadata_CacheDirectory']} ] is not found.",
                          E_USER_WARNING
                          );
            return Piece_ORM_Metadata_Factory::_createMetadataFromDatabase($tableName);
        }

        if (!is_readable($GLOBALS['PIECE_ORM_Metadata_CacheDirectory']) || !is_writable($GLOBALS['PIECE_ORM_Metadata_CacheDirectory'])) {
            trigger_error("The cache directory [ {$GLOBALS['PIECE_ORM_Metadata_CacheDirectory']} ] is not readable or writable.",
                          E_USER_WARNING
                          );
            return Piece_ORM_Metadata_Factory::_createMetadataFromDatabase($tableName);
        }

        return Piece_ORM_Metadata_Factory::_getMetadata($tableName, $tableID);
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
