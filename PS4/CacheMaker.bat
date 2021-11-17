@echo off

echo CACHE MANIFEST > test0.txt
echo # v4 >> test0.txt
echo # %date%-%time% >> test0.txt
echo. >> test0.txt
echo. >> test0.txt
echo CACHE: >> test0.txt
echo. >> test0.txt

set LOC=%~dp0

dir /B /S /A:-D >> test0.txt

echo. >> test0.txt
echo FALLBACK: >> test0.txt
echo index.html >> test0.txt
echo. >> test0.txt
echo NETWORK: >> test0.txt
echo * >> test0.txt

findstr /v "media .bat .exe .mp4 .git .py .bin test0.txt" test0.txt > test.txt
del test0.txt

@echo off
setlocal enableextensions disabledelayedexpansion
set "search=%LOC%"
set "replace="
set "textFile=test.txt"
for /f "delims=" %%i in ('type "%textFile%" ^& break ^> "%textFile%" ') do (
set "line=%%i"
setlocal enabledelayedexpansion
>>"%textFile%" echo(!line:%search%=%replace%!
endlocal)

setlocal DisableDelayedExpansion
set "firstLineReady="
(for /F "eol=$ delims=" %%a in (test.txt) DO (
if defined firstLineReady (echo()
set "firstLineReady=1"
<nul set /p "=%%a")
) > offline.manifest
del test.txt

echo offline.manifest created!!

sleep 2