#!/bin/bash

npm run build
rm -rf ../backend/app

cp -r ./dist ../backend/app
mv ../backend/app/app/nuxt ../backend/app/
