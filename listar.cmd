@echo off
setlocal enabledelayedexpansion

rem Guardar el directorio actual
set "directorio_actual=%cd%"
echo Listando el contenido de: %directorio_actual%
echo.

rem Función para listar directorios y archivos
call :listar "%directorio_actual%" 0
echo.
echo Listado completado. Presiona cualquier tecla para continuar...
pause
exit /b

:listar
set "ruta=%~1"
set "nivel=%~2"

rem Listar archivos en el directorio actual
for %%f in ("%ruta%\*") do (
    set "nombre=%%~nxf"
    
    rem Excluir carpetas "assets" y "src"
    if /I not "%%~nxf"=="assets" (
        if /I not "%%~nxf"=="src" (
            set "espacios="
            for /L %%i in (1,1,!nivel!) do set "espacios=!espacios!    "
            echo !espacios!!nombre!
        )
    )
)

rem Listar subdirectorios y llamarse recursivamente
for /D %%d in ("%ruta%\*") do (
    call :listar "%%d" !nivel! + 1
)

exit /b
