#!/bin/bash
# This script starts the Laravel queue worker
# Run this in a separate terminal: ./start-queue.sh

cd "$(dirname "$0")"
php artisan queue:work --verbose --tries=3 --timeout=90
