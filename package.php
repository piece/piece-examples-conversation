<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP version 5
 *
 * Copyright (c) 2009 Piece Project, All rights reserved.
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
 * @package    Piece_Examples_Conversation
 * @copyright  2009 Piece Project
 * @license    http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @version    Release: @package_version@
 * @since      File available since Release 1.0.0
 */

require_once 'PEAR/PackageFileManager2.php';
require_once 'PEAR.php';

PEAR::staticPushErrorHandling(PEAR_ERROR_CALLBACK, create_function('$error', 'var_dump($error); exit();'));

$releaseVersion = '1.0.0';
$releaseStability = 'stable';
$apiVersion = '1.0.0';
$apiStability = 'stable';
$notes = 'The first release of Piece_Examples_Conversation.';

$package = new PEAR_PackageFileManager2();
$package->setOptions(array('filelistgenerator' => 'file',
                           'changelogoldtonew' => false,
                           'simpleoutput'      => true,
                           'baseinstalldir'    => '/',
                           'packagefile'       => 'package.xml',
                           'packagedirectory'  => '.',
                           'dir_roles'         => array('data' => 'data',
                                                        'doc' => 'doc',
                                                        'www' => 'data'
                                                        ),
                           'ignore'            => array('package.php',
                                                        '*/www/webapp/cache/*/cache_*',
                                                        '*/www/webapp/compiled-templates/*.php',
                                                        '*/www/webapp/compiled-templates/*.serial',
                                                        '*/www/webapp/sessions/sess_*'))
                     );

$package->setPackage('Piece_Examples_Conversation');
$package->setPackageType('php');
$package->setSummary('A potato burger order application example using Piece_Unity');
$package->setDescription('Piece_Examples_Conversation provides an example application using Piece_Unity. This application focuses stateful conversation in ordering a potato burger with a potato side dish. And also this application is a reference implementation for Piece_Unity 1.x with PHP 5.');
$package->setChannel('pear.piece-framework.com');
$package->setLicense('New BSD License', 'http://www.opensource.org/licenses/bsd-license.php');
$package->setAPIVersion($apiVersion);
$package->setAPIStability($apiStability);
$package->setReleaseVersion($releaseVersion);
$package->setReleaseStability($releaseStability);
$package->setNotes($notes);
$package->setPhpDep('5.0.0');
$package->setPearinstallerDep('1.4.3');
$package->addPackageDepWithChannel('required', 'Piece_Unity', 'pear.piece-framework.com', '1.7.1');
$package->addPackageDepWithChannel('required', 'Piece_Unity_Component_NullByteAttackPreventation', 'pear.piece-framework.com', '1.0.0');
$package->addPackageDepWithChannel('required', 'Piece_Unity_Component_Flexy', 'pear.piece-framework.com', '1.2.0');
$package->addPackageDepWithChannel('required', 'Piece_Unity_Component_PieceORM', 'pear.piece-framework.com', '1.1.0');
$package->addPackageDepWithChannel('required', 'Piece_Unity_Component_ExceptionHandler', 'pear.piece-framework.com', '0.1.0');
$package->addPackageDepWithChannel('required', 'Stagehand_Autoload', 'pear.piece-framework.com', '0.1.0');
$package->addMaintainer('lead', 'iteman', 'KUBO Atsuhiro', 'kubo@iteman.jp');
$package->addMaintainer('developer', 'matsufuji', 'MATSUFUJI Hideharu', 'matsufuji@iteman.jp');
$package->addGlobalReplacement('package-info', '@package_version@', 'version');
$package->generateContents();

if (array_key_exists(1, $_SERVER['argv']) && $_SERVER['argv'][1] == 'make') {
    $package->writePackageFile();
} else {
    $package->debugPackageFile();
}

exit();

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
