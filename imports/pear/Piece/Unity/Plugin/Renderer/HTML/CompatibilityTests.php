<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP versions 4 and 5
 *
 * Copyright (c) 2006-2009 KUBO Atsuhiro <kubo@iteman.jp>,
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
 * @package    Piece_Unity
 * @copyright  2006-2009 KUBO Atsuhiro <kubo@iteman.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    GIT: $Id$
 * @since      File available since Release 0.2.0
 */

require_once 'PHPUnit.php';
require_once 'Piece/Unity/Context.php';
require_once 'Piece/Unity/Plugin/Factory.php';
require_once 'Piece/Unity/Error.php';
require_once 'PHP/Compat.php';
require_once 'PEAR/ErrorStack.php';

PHP_Compat::loadFunction('scandir');

// {{{ GLOBALS

$GLOBALS['PIECE_UNITY_Plugin_Renderer_HTML_CompatibilityTests_hasWarnings'] = false;

// }}}
// {{{ Piece_Unity_Plugin_Renderer_HTML_CompatibilityTests

/**
 * Renderer_HTML compatibility tests.
 *
 * @package    Piece_Unity
 * @copyright  2006-2009 KUBO Atsuhiro <kubo@iteman.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: 1.7.0
 * @since      Class available since Release 0.2.0
 */
class Piece_Unity_Plugin_Renderer_HTML_CompatibilityTests extends PHPUnit_TestCase
{

    // {{{ properties

    /**#@+
     * @access public
     */

    /**#@-*/

    /**#@+
     * @access private
     */

    var $_target;
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
        Piece_Unity_Plugin_Renderer_HTML_CompatibilityTests::removeDirectoryRecursively("{$this->_cacheDirectory}/compiled-templates");
        Piece_Unity_Plugin_Factory::clearInstances();
        Piece_Unity_Context::clear();
    }

    function testRendering()
    {
        $context = &Piece_Unity_Context::singleton();
        $context->setView("{$this->_target}Example");
        $viewElement = &$context->getViewElement();
        $viewElement->setElement('content', 'This is a dynamic content.');
        $config = &$this->_getConfig();
        $context->setConfiguration($config);

        $this->assertEquals("This is a test for rendering dynamic pages.\nThis is a dynamic content.", $this->_render());
    }

    function testRelativePathVulnerability()
    {
        $context = &Piece_Unity_Context::singleton();
        $context->setView('../RelativePathVulnerability');
        $viewElement = &$context->getViewElement();
        $viewElement->setElement('content', 'This is a dynamic content.');
        $config = &$this->_getConfig();
        $context->setConfiguration($config);
        set_error_handler(create_function('$code, $message, $file, $line', "
if (\$code == E_USER_WARNING) {
    \$GLOBALS['PIECE_UNITY_Plugin_Renderer_HTML_CompatibilityTests_hasWarnings'] = true;
}
"));
        $output = $this->_render();
        restore_error_handler();

        $this->assertEquals('', $output);
        $this->assertTrue($GLOBALS['PIECE_UNITY_Plugin_Renderer_HTML_CompatibilityTests_hasWarnings']);

        $GLOBALS['PIECE_UNITY_Plugin_Renderer_HTML_CompatibilityTests_hasWarnings'] = false;
    }

    function testKeepingReference()
    {
        $context = &Piece_Unity_Context::singleton();
        $context->setView("{$this->_target}KeepingReference");
        $foo = &new stdClass();
        $viewElement = &$context->getViewElement();
        $viewElement->setElementByRef('foo', $foo);
        $config = &$this->_getConfig();
        $context->setConfiguration($config);
        $this->_render();

        $this->assertTrue(array_key_exists('bar', $foo));
        $this->assertEquals('baz', $foo->bar);
    }

    function testNonExistingTemplate()
    {
        $context = &Piece_Unity_Context::singleton();
        $context->setView("{$this->_target}NonExistingView");
        $config = &$this->_getConfig();
        $context->setConfiguration($config);
        set_error_handler(create_function('$code, $message, $file, $line', "
if (\$code == E_USER_WARNING) {
    \$GLOBALS['PIECE_UNITY_Plugin_Renderer_HTML_CompatibilityTests_hasWarnings'] = true;
}
"));
        $this->_render();
        restore_error_handler();

        $this->assertTrue($GLOBALS['PIECE_UNITY_Plugin_Renderer_HTML_CompatibilityTests_hasWarnings']);

        $GLOBALS['PIECE_UNITY_Plugin_Renderer_HTML_CompatibilityTests_hasWarnings'] = false;
    }

    function testLayout()
    {
        $context = &Piece_Unity_Context::singleton();
        $context->setView("{$this->_target}LayoutContent");
        $viewElement = &$context->getViewElement();
        $viewElement->setElement('foo', 'This is an element for the content.');
        $viewElement->setElement('bar', 'This is an element for the layout.');
        $config = &$this->_getConfig();
        $config->setConfiguration("Renderer_{$this->_target}", 'useLayout', true);
        $config->setConfiguration("Renderer_{$this->_target}", 'layoutView', "{$this->_target}Layout");
        $config->setConfiguration("Renderer_{$this->_target}", 'layoutDirectory', "{$this->_cacheDirectory}/templates/Layout");
        $config->setConfiguration("Renderer_{$this->_target}", 'layoutCompileDirectory', "{$this->_cacheDirectory}/compiled-templates/Layout");
        $context->setConfiguration($config);

        $this->assertEquals('<html>
  <body>
    <h1>This is an element for the layout.</h1>
    This is an element for the content.
  </body>
</html>', trim($this->_render()));
    }

    function testTurnOffLayoutByHTTPAcceptSuccess()
    {
        $this->_assertTurnOffLayoutByHTTPAccept(true, 'This is an element for the content.');
    }

    function testTurnOffLayoutByHTTPAcceptFailure()
    {
        $this->_assertTurnOffLayoutByHTTPAccept(false, '<html>
  <body>
    <h1>This is an element for the layout.</h1>
    This is an element for the content.
  </body>
</html>');
    }

    function testFallback()
    {
        $context = &Piece_Unity_Context::singleton();
        $context->setView('NonExistingView');
        $viewElement = &$context->getViewElement();
        $viewElement->setElement('content', 'This is a dynamic content.');
        $config = &$this->_getConfig();
        $config->setConfiguration("Renderer_{$this->_target}", 'useFallback', true);
        $config->setConfiguration("Renderer_{$this->_target}", 'fallbackView', 'Fallback');
        $config->setConfiguration("Renderer_{$this->_target}", 'fallbackDirectory', "{$this->_cacheDirectory}/templates/Fallback");
        $config->setConfiguration("Renderer_{$this->_target}", 'fallbackCompileDirectory', "{$this->_cacheDirectory}/compiled-templates/Fallback");
        $context->setConfiguration($config);

        set_error_handler(create_function('$code, $message, $file, $line', "
if (\$code == E_USER_WARNING) {
    \$GLOBALS['PIECE_UNITY_Plugin_Renderer_HTML_CompatibilityTests_hasWarnings'] = true;
}
"));
        $output = $this->_render();
        restore_error_handler();

        $this->assertEquals('<html>
  <body>
    <p>This is a test for fallback.</p>
  </body>
</html>', rtrim($output));
        $this->assertFalse(Piece_Unity_Error::hasErrors());
        $this->assertTrue($GLOBALS['PIECE_UNITY_Plugin_Renderer_HTML_CompatibilityTests_hasWarnings']);

        $GLOBALS['PIECE_UNITY_Plugin_Renderer_HTML_CompatibilityTests_hasWarnings'] = false;
    }

    /**
     * @since Method available since Release 1.3.0
     */
    function testUnderScoresInViewStringShouldBeUsedAsDirectorySeparators()
    {
        $context = &Piece_Unity_Context::singleton();
        $context->setView('Foo_Bar_Baz');
        $viewElement = &$context->getViewElement();
        $viewElement->setElement('content', 'This is a dynamic content.');
        $config = &$this->_getConfigForLayeredStructure();
        $context->setConfiguration($config);

        $this->assertEquals('Hello, World!', rtrim($this->_render()));
    }

    /**
     * @since Method available since Release 1.3.0
     */
    function removeDirectoryRecursively($directory, $rootDirectory = null)
    {
        if (is_null($rootDirectory)) {
            $rootDirectory = $directory;
        }

        if (!is_dir($directory)) {
            return;
        }

        foreach (scandir($directory) as $file) {
            if ($file == '.'
                || $file == '..'
                || $file == 'README'
                || $file == '.svn'
                ) {
                continue;
            }

            $file = "$directory/$file";

            if (is_dir($file)) {
                Piece_Unity_Plugin_Renderer_HTML_CompatibilityTests::removeDirectoryRecursively($file, $rootDirectory);
            } elseif (is_file($file) && substr(basename($file), 0, 1) != '.') {
                @unlink($file);
            }
        }

        if ($directory != $rootDirectory) {
            @rmdir($directory);
        }
    }

    /**#@-*/

    /**#@+
     * @access private
     */

    function &_getConfig() {}

    function _assertTurnOffLayoutByHTTPAccept($turnOffLayoutByHTTPAccept, $result)
    {
        $context = &Piece_Unity_Context::singleton();
        $context->setView("{$this->_target}LayoutContent");
        $viewElement = &$context->getViewElement();
        $viewElement->setElement('foo', 'This is an element for the content.');
        $viewElement->setElement('bar', 'This is an element for the layout.');
        $config = &$this->_getConfig();
        $config->setConfiguration("Renderer_{$this->_target}", 'turnOffLayoutByHTTPAccept', $turnOffLayoutByHTTPAccept);
        $config->setConfiguration("Renderer_{$this->_target}", 'useLayout', true);
        $config->setConfiguration("Renderer_{$this->_target}", 'layoutView', "{$this->_target}Layout");
        $config->setConfiguration("Renderer_{$this->_target}", 'layoutDirectory', "{$this->_cacheDirectory}/templates/Layout");
        $config->setConfiguration("Renderer_{$this->_target}", 'layoutCompileDirectory', "{$this->_cacheDirectory}/compiled-templates/Layout");
        $context->setConfiguration($config);
        $_SERVER['HTTP_ACCEPT'] = 'application/x-piece-html-fragment';

        $this->assertEquals($result, rtrim($this->_render()));

        unset($_SERVER['HTTP_ACCEPT']);
    }

    /**
     * @since Method available since Release 1.0.0
     */
    function _render()
    {
        $renderer = &Piece_Unity_Plugin_Factory::factory("Renderer_{$this->_target}");
        ob_start();
        $renderer->invoke();
        $buffer = ob_get_contents();
        ob_end_clean();

        return $buffer;
    }

    /**
     * @since Method available since Release 1.0.0
     */
    function _doSetUp() {}

    /**
     * @abstract
     * @since Method available since Release 1.3.0
     */
    function &_getConfigForLayeredStructure() {}

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
