#!/bin/bash

rm -rf ./www
mkdir www

cd ../frontend
mv nuxt.config.js nuxt.config.js.orig
cp ../android-app/nuxt.config.js ./
npm run build
mv nuxt.config.js.orig nuxt.config.js

cd ../android-app
cp -r ../frontend/dist/* ./www/
mv ./www/android_asset/www/nuxt ./www/
rm -rf ./www/android_asset

cordova build android
