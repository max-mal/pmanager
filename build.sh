#!/bin/bash

cd backend
php artisan october:install

cd ../frontend
npm run build

cd ../chrome-extension
./build.sh

cd ../android-app
./build.sh
