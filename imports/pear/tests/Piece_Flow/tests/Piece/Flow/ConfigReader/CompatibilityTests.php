<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP versions 4 and 5
 *
 * Copyright (c) 2006-2008 KUBO Atsuhiro <iteman@users.sourceforge.net>,
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
 * @package    Piece_Flow
 * @copyright  2006-2008 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    SVN: $Id: CompatibilityTests.php 546 2008-07-27 08:40:01Z iteman $
 * @since      File available since Release 0.1.0
 */

require_once realpath(dirname(__FILE__) . '/../../../prepare.php');
require_once 'PHPUnit.php';
require_once 'Piece/Flow/Error.php';
require_once 'Piece/Flow/Config.php';
require_once 'Cache/Lite/File.php';

// {{{ Piece_Flow_ConfigReader_CompatibilityTests

/**
 * The base class for compatibility test of Piece_Flow_Config drivers.
 *
 * @package    Piece_Flow
 * @copyright  2006-2008 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: 1.16.0
 * @since      Class available since Release 0.1.0
 */
class Piece_Flow_ConfigReader_CompatibilityTests extends PHPUnit_TestCase
{

    // {{{ properties

    /**#@+
     * @access public
     */

    /**#@-*/

    /**#@+
     * @access private
     */

    var $_cacheDirectory;

    /**#@-*/

    /**#@+
     * @access public
     */

    function setUp()
    {
        $this->_doSetUp();
    }

    function tearDown()
    {
        $cache = &new Cache_Lite_File(array('cacheDir' => "{$this->_cacheDirectory}/",
                                            'masterFile' => '',
                                            'automaticSerialization' => true,
                                            'errorHandlingAPIBreak' => true)
                                      );
        $cache->clean();
        Piece_Flow_Error::clearErrors();
    }

    function testConfiguration()
    {
        $firstState = 'DisplayForm';
        $lastState = array('name' => 'Finish', 'view' => 'Finish',
                           'entry' =>
                           array('class' => 'Piece_FlowTestCaseAction',
                                 'method' => 'setupForm'),
                           'exit' =>
                           array('class' => 'Piece_FlowTestCaseAction',
                                 'method' => 'teardownForm'),
                           'activity' =>
                           array('class' => 'Piece_FlowTestCaseAction',
                                 'method' => 'countDisplay')
                           );
        $initial = array('class' => 'Piece_FlowTestCaseAction',
                         'method' => 'initialize'
                         );
        $final = array('class' => 'Piece_FlowTestCaseAction',
                       'method' => 'finalize'
                       );

        $viewState5 = array('name' => 'DisplayForm', 'view' => 'Form',
                            'entry' =>
                            array('class' => 'Piece_FlowTestCaseAction',
                                  'method' => 'setupForm'),
                            'exit' =>
                            array('class' => 'Piece_FlowTestCaseAction',
                                  'method' => 'teardownForm'),
                            'activity' =>
                            array('class' => 'Piece_FlowTestCaseAction',
                                  'method' => 'countDisplay')
                            );
        $transition51 = array('event' => 'submit',
                              'nextState' => 'processSubmitDisplayForm',
                              'action' =>
                              array('class' => 'Piece_FlowTestCaseAction',
                                    'method' => 'validateInput'),
                              'guard' =>
                              array('class' => 'Piece_FlowTestCaseAction',
                                    'method' => 'isPermitted')
                              );

        $viewState6 = array('name' => 'ConfirmForm', 'view' => 'Confirmation');
        $transition61 = array('event' => 'submit',
                              'nextState' => 'processSubmitConfirmForm',
                              'action' =>
                              array('class' => 'Piece_FlowTestCaseAction',
                                    'method' => 'validateConfirmation')
                              );

        $actionState1 = 'processSubmitDisplayForm';
        $transition11 = array('event' => 'raiseError',
                              'nextState' => 'DisplayForm'
                              );
        $transition12 = array('event' => 'succeed',
                              'nextState' => 'ConfirmForm'
                              );

        $actionState7 = 'processSubmitConfirmForm';
        $transition71 = array('event' => 'raiseError',
                              'nextState' => 'DisplayForm'
                              );
        $transition72 = array('event' => 'succeed',
                              'nextState' => 'Register',
                              'action' =>
                              array('class' => 'Piece_FlowTestCaseAction',
                                    'method' => 'register')
                              );

        $actionState2 = 'Register';
        $transition21 = array('event' => 'raiseError',
                              'nextState' => 'DisplayForm'
                              );
        $transition22 = array('event' => 'succeed',
                              'nextState' => 'Finish'
                              );

        $expectedConfig = new Piece_Flow_Config();
        $expectedConfig->setFirstState($firstState);
        $expectedConfig->setLastState($lastState['name'], $lastState['view']);
        $expectedConfig->setEntryAction($lastState['name'], $lastState['entry']);
        $expectedConfig->setExitAction($lastState['name'], $lastState['exit']);
        $expectedConfig->setActivity($lastState['name'], $lastState['activity']);
        $expectedConfig->setInitialAction($initial);
        $expectedConfig->setFinalAction($final);
        $expectedConfig->addViewState($viewState5['name'], $viewState5['view']);
        $expectedConfig->setEntryAction($viewState5['name'], $viewState5['entry']);
        $expectedConfig->setExitAction($viewState5['name'], $viewState5['exit']);
        $expectedConfig->setActivity($viewState5['name'], $viewState5['activity']);
        $expectedConfig->addViewState($viewState6['name'], $viewState6['view']);
        $expectedConfig->addTransition($viewState5['name'],
                                       $transition51['event'],
                                       $transition51['nextState'],
                                       $transition51['action'],
                                       $transition51['guard']
                                       );
        $expectedConfig->addTransition($viewState6['name'],
                                       $transition61['event'],
                                       $transition61['nextState'],
                                       $transition61['action']
                                       );
        $expectedConfig->addActionState($actionState1);
        $expectedConfig->addTransition($actionState1,
                                       $transition11['event'],
                                       $transition11['nextState']
                                       );
        $expectedConfig->addTransition($actionState1,
                                       $transition12['event'],
                                       $transition12['nextState']
                                       );
        $expectedConfig->addActionState($actionState7);
        $expectedConfig->addTransition($actionState7,
                                       $transition71['event'],
                                       $transition71['nextState']
                                       );
        $expectedConfig->addTransition($actionState7,
                                       $transition72['event'],
                                       $transition72['nextState'],
                                       $transition72['action']
                                       );
        $expectedConfig->addActionState($actionState2);
        $expectedConfig->addTransition($actionState2,
                                       $transition21['event'],
                                       $transition21['nextState']
                                       );
        $expectedConfig->addTransition($actionState2,
                                       $transition22['event'],
                                       $transition22['nextState']
                                       );

        $reader = &$this->_createConfigReader("{$this->_cacheDirectory}/Registration" . $this->_getExtension());
        $actualConfig = &$reader->read();

        $this->assertEquals(strtolower('Piece_Flow_Config'), strtolower(get_class($actualConfig)));
        $this->assertEquals($expectedConfig->getFirstState(), $actualConfig->getFirstState());
        $this->assertEquals($expectedConfig->getLastState(), $actualConfig->getLastState());
        $this->assertEquals($expectedConfig->getViewStates(), $actualConfig->getViewStates());
        $this->assertEquals($expectedConfig->getActionStates(), $actualConfig->getActionStates());
        $this->assertEquals($expectedConfig->getInitialAction(), $actualConfig->getInitialAction());
        $this->assertEquals($expectedConfig->getFinalAction(), $actualConfig->getFinalAction());
    }

    /**
     * @since Method available since Release 1.10.0
     */
    function testExceptionShouldBeRaisedIfInvalidFormatIsDetected()
    {
        $this->_assertExceptionShouldBeRaisedIfInvalidFormatIsDetected('FirstStateNotFound');
        $this->_assertExceptionShouldBeRaisedIfInvalidFormatIsDetected('FirstStateIsInvalid');
        $this->_assertExceptionShouldBeRaisedIfInvalidFormatIsDetected('NameInLastStateNotFound');
        $this->_assertExceptionShouldBeRaisedIfInvalidFormatIsDetected('NameInLastStateIsInvalid');
        $this->_assertExceptionShouldBeRaisedIfInvalidFormatIsDetected('ViewInLastStateNotFound');
        $this->_assertExceptionShouldBeRaisedIfInvalidFormatIsDetected('ViewInLastStateIsInvalid');
        $this->_assertExceptionShouldBeRaisedIfInvalidFormatIsDetected('MethodInFinalActionNotFound');
        $this->_assertExceptionShouldBeRaisedIfInvalidFormatIsDetected('MethodInFinalActionIsInvalid');
        $this->_assertExceptionShouldBeRaisedIfInvalidFormatIsDetected('ClassInFinalActionIsInvalid');
        $this->_assertExceptionShouldBeRaisedIfInvalidFormatIsDetected('MethodInInitialActionNotFound');
        $this->_assertExceptionShouldBeRaisedIfInvalidFormatIsDetected('MethodInInitialActionIsInvalid');
        $this->_assertExceptionShouldBeRaisedIfInvalidFormatIsDetected('ClassInInitialActionIsInvalid');
        $this->_assertExceptionShouldBeRaisedIfInvalidFormatIsDetected('ViewStateHasNoElements');
        $this->_assertExceptionShouldBeRaisedIfInvalidFormatIsDetected('NameInViewStateNotFound');
        $this->_assertExceptionShouldBeRaisedIfInvalidFormatIsDetected('NameInViewStateIsInvalid');
        $this->_assertExceptionShouldBeRaisedIfInvalidFormatIsDetected('ViewInViewStateNotFound');
        $this->_assertExceptionShouldBeRaisedIfInvalidFormatIsDetected('ViewInViewStateIsInvalid');
        $this->_assertExceptionShouldBeRaisedIfInvalidFormatIsDetected('NameInActionStateNotFound');
        $this->_assertExceptionShouldBeRaisedIfInvalidFormatIsDetected('NameInActionStateIsInvalid');
        $this->_assertExceptionShouldBeRaisedIfInvalidFormatIsDetected('EventInTransitionNotFound');
        $this->_assertExceptionShouldBeRaisedIfInvalidFormatIsDetected('EventInTransitionIsInvalid');
        $this->_assertExceptionShouldBeRaisedIfInvalidFormatIsDetected('NextStateInTransitionNotFound');
        $this->_assertExceptionShouldBeRaisedIfInvalidFormatIsDetected('NextStateInTransitionIsInvalid');
        $this->_assertExceptionShouldBeRaisedIfInvalidFormatIsDetected('MethodInActionNotFound');
        $this->_assertExceptionShouldBeRaisedIfInvalidFormatIsDetected('MethodInActionIsInvalid');
        $this->_assertExceptionShouldBeRaisedIfInvalidFormatIsDetected('ClassInActionIsInvalid');
        $this->_assertExceptionShouldBeRaisedIfInvalidFormatIsDetected('MethodInGuardNotFound');
        $this->_assertExceptionShouldBeRaisedIfInvalidFormatIsDetected('MethodInGuardIsInvalid');
        $this->_assertExceptionShouldBeRaisedIfInvalidFormatIsDetected('ClassInGuardIsInvalid');
        $this->_assertExceptionShouldBeRaisedIfInvalidFormatIsDetected('MethodInEntryNotFound');
        $this->_assertExceptionShouldBeRaisedIfInvalidFormatIsDetected('MethodInEntryIsInvalid');
        $this->_assertExceptionShouldBeRaisedIfInvalidFormatIsDetected('ClassInEntryIsInvalid');
        $this->_assertExceptionShouldBeRaisedIfInvalidFormatIsDetected('MethodInExitNotFound');
        $this->_assertExceptionShouldBeRaisedIfInvalidFormatIsDetected('MethodInExitIsInvalid');
        $this->_assertExceptionShouldBeRaisedIfInvalidFormatIsDetected('ClassInExitIsInvalid');
        $this->_assertExceptionShouldBeRaisedIfInvalidFormatIsDetected('MethodInActivityNotFound');
        $this->_assertExceptionShouldBeRaisedIfInvalidFormatIsDetected('MethodInActivityIsInvalid');
        $this->_assertExceptionShouldBeRaisedIfInvalidFormatIsDetected('ClassInActivityIsInvalid');
    }

    /**
     * @since Method available since Release 1.14.0
     */
    function testCacheIDsShouldUniqueInOneCacheDirectory()
    {
        $oldDirectory = getcwd();
        chdir("{$this->_cacheDirectory}/CacheIDsShouldBeUniqueInOneCacheDirectory1");
        $reader = &$this->_createConfigReader('New' . $this->_getExtension());
        $reader->read();

        $this->assertEquals(1, $this->_getCacheFileCount($this->_cacheDirectory));

        chdir("{$this->_cacheDirectory}/CacheIDsShouldBeUniqueInOneCacheDirectory2");
        $reader = &$this->_createConfigReader('New' . $this->_getExtension());
        $reader->read();

        $this->assertEquals(2, $this->_getCacheFileCount($this->_cacheDirectory));

        chdir($oldDirectory);
    }

    /**#@-*/

    /**#@+
     * @access private
     */

    function &_createConfigReader($source) {}
    function _doSetUp() {}
    function _getSource($name) {}

    function _assertExceptionShouldBeRaisedIfInvalidFormatIsDetected($name)
    {
        $reader = &$this->_createConfigReader("{$this->_cacheDirectory}/$name" . $this->_getExtension());
        Piece_Flow_Error::disableCallback();
        @$config = &$reader->read();
        Piece_Flow_Error::enableCallback();

        $this->assertNull($config, $name, $name);
        $this->assertTrue(Piece_Flow_Error::hasErrors(), $name, $name);

        $error = Piece_Flow_Error::pop();

        $this->assertEquals(PIECE_FLOW_ERROR_INVALID_FORMAT, $error['code'], $name);
    }

    /**
     * @since Method available since Release 1.14.0
     */
    function _getCacheFileCount($directory)
    {
        $cacheFileCount = 0;
        if ($dh = opendir($directory)) {
            while (true) {
                $file = readdir($dh);
                if ($file === false) {
                    break;
                }

                if (filetype("$directory/$file") == 'file') {
                    if (preg_match('/^cache_.+/', $file)) {
                        ++$cacheFileCount;
                    }
                }
            }

            closedir($dh);
        }

        return $cacheFileCount;
    }

    /**
     * @since Method available since Release 1.14.0
     */
    function _getExtension() {}

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
