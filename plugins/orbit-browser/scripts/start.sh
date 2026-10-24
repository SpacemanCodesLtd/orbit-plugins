#!/bin/sh
set -eu

if [ -n "${ORBIT_APP_PATH:-}" ]; then
    orbit_app="$ORBIT_APP_PATH"
elif [ -d /Applications/Orbit.app ]; then
    orbit_app=/Applications/Orbit.app
else
    orbit_app="$HOME/Applications/Orbit.app"
fi

orbit_build="$orbit_app/Contents/Resources/build"
if [ ! -x "$orbit_build/php/php" ] || [ ! -f "$orbit_build/app/vendor/autoload.php" ]; then
    echo 'Orbit Browser needs Orbit installed in /Applications or ~/Applications. Set ORBIT_APP_PATH for another location.' >&2
    exit 1
fi

plugin_root=$(CDPATH= cd -- "$(dirname -- "$0")/.." && pwd)
exec "$orbit_build/php/php" "$plugin_root/scripts/server.php" "$orbit_build/app"
