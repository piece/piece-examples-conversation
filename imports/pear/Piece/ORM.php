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
 * @version    SVN: $Id: ORM.php 416 2008-07-12 06:41:48Z iteman $
 * @since      File available since Release 0.1.0
 */

require_once 'Piece/ORM/Config/Factory.php';
require_once 'Piece/ORM/Mapper/Factory.php';
require_once 'Piece/ORM/Metadata/Factory.php';
require_once 'Piece/ORM/Context.php';

// {{{ GLOBALS

$GLOBALS['PIECE_ORM_Configured'] = false;

// }}}
// {{{ Piece_ORM

/**
 * A single entry point for Piece_ORM mappers.
 *
 * @package    Piece_ORM
 * @copyright  2007-2008 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: 1.2.0
 * @since      Class available since Release 0.1.0
 */
class Piece_ORM
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
    // {{{ configure()

    /**
     * Configures the Piece_ORM environment.
     *
     * First this method tries to load a configuration from a configuration
     * file in the given configration directory using
     * Piece_ORM_Config_Factory::factory(). The method creates a new object
     * if the load failed.
     * Second this method sets the configuration to the current context.
     * And also this method sets the configuration directory for the mapper
     * configuration, and the cache directory for mappers, and the cache
     * directory for Piece_ORM_Metadata class.
     *
     * @param string $configDirectory
     * @param string $cacheDirectory
     * @param string $mapperConfigDirectory
     */
    function configure($configDirectory,
                       $cacheDirectory,
                       $mapperConfigDirectory
                       )
    {
        $config = &Piece_ORM_Config_Factory::factory($configDirectory,
                                                     $cacheDirectory
                                                     );
        $context = &Piece_ORM_Context::singleton();
        $context->setConfiguration($config);
        $context->setMapperConfigDirectory($mapperConfigDirectory);
        $defaultDatabase = $config->getDefaultDatabase();
        if (!is_null($defaultDatabase)) {
            $context->setDatabase($defaultDatabase);
        }

        Piece_ORM_Mapper_Factory::setCacheDirectory($cacheDirectory);
        Piece_ORM_Metadata_Factory::setCacheDirectory($cacheDirectory);

        $GLOBALS['PIECE_ORM_Configured'] = true;
    }

    // }}}
    // {{{ getMapper()

    /**
     * Gets a mapper object for a given mapper name.
     *
     * @param string $mapperName
     * @return Piece_ORM_Mapper_Common
     * @throws PIECE_ORM_ERROR_INVALID_OPERATION
     */
    function &getMapper($mapperName)
    {
        if (!$GLOBALS['PIECE_ORM_Configured']) {
            Piece_ORM_Error::push(PIECE_ORM_ERROR_INVALID_OPERATION,
                                  __FUNCTION__ . ' method must be called after calling configure().'
                                  );
            $return = null;
            return $return;
        }

        return Piece_ORM_Mapper_Factory::factory($mapperName);
    }

    // }}}
    // {{{ getConfiguration()

    /**
     * Gets the Piece_ORM_Config object after calling configure().
     *
     * @return Piece_ORM_Config
     * @throws PIECE_ORM_ERROR_INVALID_OPERATION
     */
    function &getConfiguration()
    {
        if (!$GLOBALS['PIECE_ORM_Configured']) {
            Piece_ORM_Error::push(PIECE_ORM_ERROR_INVALID_OPERATION,
                                  __FUNCTION__ . ' method must be called after calling configure().'
                                  );
            $return = null;
            return $return;
        }

        $context = &Piece_ORM_Context::singleton();
        return $context->getConfiguration();
    }

    // }}}
    // {{{ setDatabase()

    /**
     * Sets a database as the current database.
     *
     * @param string $database
     * @throws PIECE_ORM_ERROR_INVALID_OPERATION
     */
    function setDatabase($database)
    {
        if (!$GLOBALS['PIECE_ORM_Configured']) {
            Piece_ORM_Error::push(PIECE_ORM_ERROR_INVALID_OPERATION,
                                  __FUNCTION__ . ' method must be called after calling configure().'
                                  );
            return;
        }

        $context = &Piece_ORM_Context::singleton();
        $context->setDatabase($database);
    }

    // }}}
    // {{{ createObject()

    /**
     * Creates an object from the metadata.
     *
     * @param string $mapperName
     * @return stdClass
     * @throws PIECE_ORM_ERROR_INVALID_OPERATION
     */
    function &createObject($mapperName)
    {
        if (!$GLOBALS['PIECE_ORM_Configured']) {
            Piece_ORM_Error::push(PIECE_ORM_ERROR_INVALID_OPERATION,
                                  __FUNCTION__ . ' method must be called after calling configure().'
                                  );
            $return = null;
            return $return;
        }

        $mapper = &Piece_ORM_Mapper_Factory::factory($mapperName);
        if (Piece_ORM_Error::hasErrors()) {
            $return = null;
            return $return;
        }

        return $mapper->createObject();
    }

    // }}}
    // {{{ dressObject()

    /**
     * Converts an object into a specified object.
     *
     * @param stdClass &$oldObject
     * @param mixed    $newObject
     * @return mixed
     */
    function &dressObject(&$oldObject, $newObject)
    {
        foreach (array_keys(get_object_vars($oldObject)) as $property) {
            if (!is_object($oldObject->$property)) {
                $newObject->$property = $oldObject->$property;
            } else {
                $newObject->$property = &$oldObject->$property;
            }
        }

        return $newObject;
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
