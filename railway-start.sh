#!/bin/bash

# Este script será llamado por Railway y redirigirá al entrypoint real
echo "=== Wrapper Script - Redirigiendo a entrypoint real ==="

# Llamar al entrypoint real
exec /entrypoint.sh
