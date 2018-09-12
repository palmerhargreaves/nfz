#!/bin/bash

cat generate_id.js namespace.js extend.js coordinates.js delegate.js defer.js string.js observable.js effects/frame.js effects/image_preloader.js effects/simple_image_preloader.js effects/mask.js | yuicompressor --charset utf-8 --type js -o build/utils.js