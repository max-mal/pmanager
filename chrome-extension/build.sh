#!/bin/bash

rm -rf ./build
mkdir build;
cp -r extension ./build/
cp manifest.json ./build/

cd ../frontend
npm run build
cp -r ./dist/* ../chrome-extension/build/

cd ../chrome-extension
