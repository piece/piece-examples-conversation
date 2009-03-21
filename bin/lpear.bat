@ECHO OFF
REM *************************************************************
REM ** The project local PEAR for Windows based systems (based on symfony.bat)
REM *************************************************************

REM This script will do the following:
REM - check for PHP_COMMAND env, if found, use it.
REM   - if not found detect php, if found use it, otherwise err and terminate

IF "%OS%"=="Windows_NT" @SETLOCAL

REM %~dp0 is expanded pathname of the current script under NT
SET SCRIPT_DIR=%~dp0

pushd "%SCRIPT_DIR%.."
FOR /f "usebackq" %%p IN (`chdir`) DO SET TARGET_PATH=%%p
popd

SET PHP_PEAR_INSTALL_DIR=%TARGET_PATH%\imports\pear
SET PHP_PEAR_BIN_DIR=%TARGET_PATH%\bin
SET LPEAR_PHP_INSTALL_DIR=C:\win32app\php-5.2.6-Win32

IF NOT EXIST %SCRIPT_DIR%pear.ini (
   @call %PHP_PEAR_BIN_DIR%\pear.bat -c %SCRIPT_DIR%pear.ini config-set bin_dir %TARGET_PATH%\bin user
   @call %PHP_PEAR_BIN_DIR%\pear.bat -c %SCRIPT_DIR%pear.ini config-set doc_dir %PHP_PEAR_INSTALL_DIR%\docs user
   @call %PHP_PEAR_BIN_DIR%\pear.bat -c %SCRIPT_DIR%pear.ini config-set ext_dir %LPEAR_PHP_INSTALL_DIR%\ext user
   @call %PHP_PEAR_BIN_DIR%\pear.bat -c %SCRIPT_DIR%pear.ini config-set php_dir %PHP_PEAR_INSTALL_DIR% user
   @call %PHP_PEAR_BIN_DIR%\pear.bat -c %SCRIPT_DIR%pear.ini config-set cache_dir %TARGET_PATH%\tmp\pear\cache user
   @call %PHP_PEAR_BIN_DIR%\pear.bat -c %SCRIPT_DIR%pear.ini config-set cfg_dir %PHP_PEAR_INSTALL_DIR%\cfg user
   @call %PHP_PEAR_BIN_DIR%\pear.bat -c %SCRIPT_DIR%pear.ini config-set data_dir %PHP_PEAR_INSTALL_DIR%\data user
   @call %PHP_PEAR_BIN_DIR%\pear.bat -c %SCRIPT_DIR%pear.ini config-set download_dir %TARGET_PATH%\tmp\pear\download user
   @call %PHP_PEAR_BIN_DIR%\pear.bat -c %SCRIPT_DIR%pear.ini config-set php_bin %LPEAR_PHP_INSTALL_DIR%\php.exe user
   @call %PHP_PEAR_BIN_DIR%\pear.bat -c %SCRIPT_DIR%pear.ini config-set temp_dir %TARGET_PATH%\tmp\pear\temp user
   @call %PHP_PEAR_BIN_DIR%\pear.bat -c %SCRIPT_DIR%pear.ini config-set test_dir %PHP_PEAR_INSTALL_DIR%\tests user
   @call %PHP_PEAR_BIN_DIR%\pear.bat -c %SCRIPT_DIR%pear.ini config-set www_dir %TARGET_PATH%\www user
)

@call %PHP_PEAR_BIN_DIR%\pear.bat -c %SCRIPT_DIR%pear.ini %1 %2 %3 %4 %5 %6 %7 %8 %9

@ECHO OFF

IF "%OS%"=="Windows_NT" @ENDLOCAL
REM PAUSE

REM Local Variables:
REM mode: bat-generic
REM coding: iso-8859-1
REM indent-tabs-mode: nil
REM End:
