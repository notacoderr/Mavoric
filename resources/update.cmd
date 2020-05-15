@echo off
TITLE Dependency updates [INCOMPLETE]
cd /d %~dp0

set iterations=0

if exist bin\php\php.exe (
	set PHPRC=""
	set PHP_BINARY=bin\php\php.exe
) else (
	set PHP_BINARY=php
)

if exist vendor (
	rmdir vendor /s /q
)

:checkComposer
if exist bin\composer.phar (
	echo Composer exists, attempting to update.
) else (
	powershell -command "Invoke-WebRequest https://getcomposer.org/composer.phar -OutFile \bin\composer.phar"
	goto checkComposer
)
cls
%PHP_BINARY% ./bin/composer.phar update
TITLE Dependency updates [COMPLETED]
cls
echo Successfully updated dependencies
echo Press any key to close.
pause > nul
exit