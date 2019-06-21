importScripts('/app/nuxt/workbox.4c4f5ca6.js')

workbox.precaching.precacheAndRoute([
  {
    "url": "/app/nuxt/1d02bda4e3cb56454561.js",
    "revision": "543f1605ab51c7203f40c91c81114f03"
  },
  {
    "url": "/app/nuxt/2ad108b7711538820245.js",
    "revision": "ba702f7fc9f2f6eacb92caabc2612324"
  },
  {
    "url": "/app/nuxt/2dd59e3567289a4c2de7.js",
    "revision": "b0b0160b2e1a893fc7618e7de2623ac5"
  },
  {
    "url": "/app/nuxt/3b96acafd5d60c3195d7.js",
    "revision": "f9ca520a6d6613ff6a56d71b94f6a33a"
  },
  {
    "url": "/app/nuxt/416a23cceae3f0a396b8.js",
    "revision": "27fccbd3e4f00da6faa5348de7abb447"
  },
  {
    "url": "/app/nuxt/4d3f46bea350dfbbc54f.js",
    "revision": "4f06cd801be1719894fbf34efe11e8a1"
  },
  {
    "url": "/app/nuxt/5f68ce4cb84da9c002b0.js",
    "revision": "9c8e204ad4d6f239527bd7334821b158"
  },
  {
    "url": "/app/nuxt/650b9d4b2745519cf78e.js",
    "revision": "5dc3b0dfb52e8099479b9ae438356cbb"
  },
  {
    "url": "/app/nuxt/cb0645f28e23876e5e04.js",
    "revision": "a92379b8981e25df23ad573528cfc597"
  },
  {
    "url": "/app/nuxt/d60a955710da0622de92.js",
    "revision": "cebc0722ec7a610fdf740b41baa0b77c"
  },
  {
    "url": "/app/nuxt/e36fa281c49405c54f51.js",
    "revision": "783cad0a285ce36752e20d020e053306"
  },
  {
    "url": "/app/nuxt/e718c46cd81c938dde5e.js",
    "revision": "814c0c02f4b8b0d832a159c47a5acc1e"
  },
  {
    "url": "/app/nuxt/f504127fdc6fc4e977b1.js",
    "revision": "6a0ce8d80e5aca20c75470840e0f2078"
  }
], {
  "cacheId": "pm-nuxt",
  "directoryIndex": "/",
  "cleanUrls": false
})

workbox.clientsClaim()
workbox.skipWaiting()

workbox.routing.registerRoute(new RegExp('/app/nuxt/.*'), workbox.strategies.cacheFirst({}), 'GET')

workbox.routing.registerRoute(new RegExp('/.*'), workbox.strategies.networkFirst({}), 'GET')
