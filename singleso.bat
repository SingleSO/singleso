@echo off

@setlocal

set HERE_PATH=%~dp0

if "%PHP_COMMAND%" == "" set PHP_COMMAND=php.exe

"%PHP_COMMAND%" "%HERE_PATH%singleso" %*

@endlocal
