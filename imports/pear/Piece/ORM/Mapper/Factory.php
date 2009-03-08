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
 * @version    SVN: $Id: Factory.php 416 2008-07-12 06:41:48Z iteman $
 * @since      File available since Release 0.1.0
 */

require_once 'Piece/ORM/Error.php';
require_once 'PEAR.php';
require_once 'Cache/Lite/File.php';
require_once 'Piece/ORM/Context.php';
require_once 'Piece/ORM/Mapper/Common.php';
require_once 'Piece/ORM/Mapper/Generator.php';
require_once 'Piece/ORM/Metadata/Factory.php';
require_once 'Piece/ORM/Inflector.php';

if (version_compare(phpversion(), '5.0.0', '<')) {
    require_once 'spyc.php';
} else {
    require_once 'spyc.php5';
}

require_once 'Piece/ORM/Env.php';

// {{{ GLOBALS

$GLOBALS['PIECE_ORM_Mapper_Instances'] = array();
$GLOBALS['PIECE_ORM_Mapper_ConfigDirectory'] = null;
$GLOBALS['PIECE_ORM_Mapper_CacheDirectory'] = null;

// }}}
// {{{ Piece_ORM_Mapper_Factory

/**
 * A factory class for creating mapper objects.
 *
 * @package    Piece_ORM
 * @copyright  2007-2008 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: 1.2.0
 * @since      Class available since Release 0.1.0
 */
class Piece_ORM_Mapper_Factory
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
    // {{{ factory()

    /**
     * Creates a mapper object for a given mapper name.
     *
     * @param string $mapperName
     * @return Piece_ORM_Mapper_Common
     * @throws PIECE_ORM_ERROR_INVALID_MAPPER
     */
    function &factory($mapperName)
    {
        $context = &Piece_ORM_Context::singleton();
        if (!$context->getUseMapperNameAsTableName()) {
            $mapperName = Piece_ORM_Inflector::camelize($mapperName);
        }

        $mapperID = sha1($context->getDSN() . ".$mapperName." . realpath($GLOBALS['PIECE_ORM_Mapper_ConfigDirectory']));
        if (!array_key_exists($mapperID, $GLOBALS['PIECE_ORM_Mapper_Instances'])) {
            Piece_ORM_Mapper_Factory::_load($mapperID, $mapperName);
            if (Piece_ORM_Error::hasErrors()) {
                $return = null;
                return $return;
            }

            $metadata = &Piece_ORM_Metadata_Factory::factory($mapperName);
            if (Piece_ORM_Error::hasErrors()) {
                $return = null;
                return $return;
            }

            $mapperClass = Piece_ORM_Mapper_Factory::_getMapperClass($mapperID);
            $mapper = &new $mapperClass($metadata);
            if (!is_subclass_of($mapper, 'Piece_ORM_Mapper_Common')) {
                Piece_ORM_Error::push(PIECE_ORM_ERROR_INVALID_MAPPER,
                                      "The mapper class for [ $mapperName ] is invalid."
                                      );
                $return = null;
                return $return;
            }

            $GLOBALS['PIECE_ORM_Mapper_Instances'][$mapperID] = &$mapper;
        }

        $dbh = &$context->getConnection();
        if (Piece_ORM_Error::hasErrors()) {
            $return = null;
            return $return;
        }

        $GLOBALS['PIECE_ORM_Mapper_Instances'][$mapperID]->setConnection($dbh);

        return $GLOBALS['PIECE_ORM_Mapper_Instances'][$mapperID];
    }

    // }}}
    // {{{ clearInstances()

    /**
     * Clears the mapper instances.
     */
    function clearInstances()
    {
        $GLOBALS['PIECE_ORM_Mapper_Instances'] = array();
    }

    // }}}
    // {{{ setConfigDirectory()

    /**
     * Sets a config directory.
     *
     * @param string $configDirectory
     */
    function setConfigDirectory($configDirectory)
    {
        $GLOBALS['PIECE_ORM_Mapper_ConfigDirectory'] = $configDirectory;
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
        $GLOBALS['PIECE_ORM_Mapper_CacheDirectory'] = $cacheDirectory;
    }

    /**#@-*/

    /**#@+
     * @access private
     * @static
     */

    // }}}
    // {{{ _getMapperSource()

    /**
     * Gets a mapper source by either generating from a configuration file or
     * getting from a cache.
     *
     * @param string $mapperID
     * @param string $mapperName
     * @param string $configFile
     * @return string
     * @throws PIECE_ORM_ERROR_CANNOT_READ
     * @throws PIECE_ORM_ERROR_CANNOT_WRITE
     */
    function _getMapperSource($mapperID, $mapperName, $configFile)
    {
        $cache = &new Cache_Lite_File(array('cacheDir' => "{$GLOBALS['PIECE_ORM_Mapper_CacheDirectory']}/",
                                            'masterFile' => $configFile,
                                            'automaticSerialization' => true,
                                            'errorHandlingAPIBreak' => true)
                                      );

        if (!Piece_ORM_Env::isProduction()) {
            $cache->remove($mapperID);
        }

        /*
         * The Cache_Lite class always specifies PEAR_ERROR_RETURN when
         * calling PEAR::raiseError in default.
         */
        $mapperSource = $cache->get($mapperID);
        if (PEAR::isError($mapperSource)) {
            Piece_ORM_Error::push(PIECE_ORM_ERROR_CANNOT_READ,
                                  "Cannot read the mapper source file in the directory [ {$GLOBALS['PIECE_ORM_Mapper_CacheDirectory']} ]."
                                  );
            return;
        }

        if (!$mapperSource) {
            $mapperSource = Piece_ORM_Mapper_Factory::_generateMapperSource($mapperID, $mapperName, $configFile);
            if (Piece_ORM_Error::hasErrors()) {
                return;
            }

            $result = $cache->save($mapperSource);
            if (PEAR::isError($result)) {
                Piece_ORM_Error::push(PIECE_ORM_ERROR_CANNOT_WRITE,
                                      "Cannot write the mapper source to the cache file in the directory [ {$GLOBALS['PIECE_ORM_Mapper_CacheDirectory']} ]."
                                      );
                return;
            }
        }

        return $mapperSource;
    }

    // }}}
    // {{{ _generateMapperSource()

    /**
     * Generates a mapper source from the given configuration file.
     *
     * @param string $mapperID
     * @param string $mapperName
     * @param string $configFile
     * @return string
     */
    function _generateMapperSource($mapperID, $mapperName, $configFile)
    {
        $metadata = &Piece_ORM_Metadata_Factory::factory($mapperName);
        if (Piece_ORM_Error::hasErrors()) {
            return;
        }

        $generator = &new Piece_ORM_Mapper_Generator(Piece_ORM_Mapper_Factory::_getMapperClass($mapperID), $mapperName, Spyc::YAMLLoad($configFile), $metadata, get_class_methods('Piece_ORM_Mapper_Common'));
        return $generator->generate();
    }

    // }}}
    // {{{ _loaded()

    /**
     * Returns whether the mapper class for a given mapper ID has already
     * been loaded or not.
     *
     * @param string $mapperID
     * @return boolean
     */
    function _loaded($mapperID)
    {
        $mapperClass = Piece_ORM_Mapper_Factory::_getMapperClass($mapperID);
        if (version_compare(phpversion(), '5.0.0', '<')) {
            return class_exists($mapperClass);
        } else {
            return class_exists($mapperClass, false);
        }
    }

    // }}}
    // {{{ _load()

    /**
     * Loads a mapper class based on the given information.
     *
     * @param string $mapperID
     * @param string $mapperName
     * @throws PIECE_ORM_ERROR_INVALID_OPERATION
     * @throws PIECE_ORM_ERROR_NOT_FOUND
     * @throws PIECE_ORM_ERROR_NOT_READABLE
     */
    function _load($mapperID, $mapperName)
    {
        if (Piece_ORM_Mapper_Factory::_loaded($mapperID)) {
            return;
        }

        if (is_null($GLOBALS['PIECE_ORM_Mapper_ConfigDirectory'])) {
            Piece_ORM_Error::push(PIECE_ORM_ERROR_INVALID_OPERATION,
                                  'The configuration directory must be specified.'
                                  );
            return;
        }

        if (!file_exists($GLOBALS['PIECE_ORM_Mapper_ConfigDirectory'])) {
            Piece_ORM_Error::push(PIECE_ORM_ERROR_NOT_FOUND,
                                  "The configuration directory [ {$GLOBALS['PIECE_ORM_Mapper_ConfigDirectory']} ] not found."
                                  );
            return;
        }

        if (is_null($GLOBALS['PIECE_ORM_Mapper_CacheDirectory'])) {
            Piece_ORM_Error::push(PIECE_ORM_ERROR_INVALID_OPERATION,
                                  'The cache directory must be specified.'
                                  );
            return;
        }

        if (!file_exists($GLOBALS['PIECE_ORM_Mapper_CacheDirectory'])) {
            Piece_ORM_Error::push(PIECE_ORM_ERROR_NOT_FOUND,
                                  "The cache directory [ {$GLOBALS['PIECE_ORM_Mapper_CacheDirectory']} ] not found."
                                  );
            return;
        }

        if (!is_readable($GLOBALS['PIECE_ORM_Mapper_CacheDirectory']) || !is_writable($GLOBALS['PIECE_ORM_Mapper_CacheDirectory'])) {
            Piece_ORM_Error::push(PIECE_ORM_ERROR_NOT_READABLE,
                                  "The cache directory [ {$GLOBALS['PIECE_ORM_Mapper_CacheDirectory']} ] is not readable or writable."
                                  );
            return;
        }

        $configFile = "{$GLOBALS['PIECE_ORM_Mapper_ConfigDirectory']}/$mapperName.yaml";
        if (!file_exists($configFile)) {
            Piece_ORM_Error::push(PIECE_ORM_ERROR_NOT_FOUND,
                                  "The configuration file [ $configFile ] not found."
                                  );
            return;
        }

        if (!is_readable($configFile)) {
            Piece_ORM_Error::push(PIECE_ORM_ERROR_NOT_READABLE,
                                  "The configuration file [ $configFile ] is not readable."
                                  );
            return;
        }

        $mapperSource = Piece_ORM_Mapper_Factory::_getMapperSource($mapperID, $mapperName, $configFile);
        if (Piece_ORM_Error::hasErrors()) {
            return;
        }

        eval($mapperSource);

        if (!Piece_ORM_Mapper_Factory::_loaded($mapperID)) {
            Piece_ORM_Error::push(PIECE_ORM_ERROR_NOT_FOUND,
                                  "The mapper [ $mapperName ] not found."
                                  );
        }
    }

    // }}}
    // {{{ _getMapperClass()

    /**
     * Gets the class name for a given mapper ID.
     *
     * @param string $mapperID
     * @return string
     */
    function _getMapperClass($mapperID)
    {
        return "Piece_ORM_Mapper_$mapperID";
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
