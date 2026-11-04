<?php

use Xhgui\Profiler\Profiler;
use Xhgui\Profiler\ProfilingFlags;

return array(
    'save.handler' => Profiler::SAVER_UPLOAD,
    'save.handler.stack' => array(
        'savers' => array(
            Profiler::SAVER_UPLOAD,
        ),
        'saveAll' => false,
    ),
    'save.handler.upload' => array(
        'url' => 'http://localhost:8142/run/import',
        'timeout' => 3,
        'token' => '',
    ),
    'profiler.enable' => function () {
        return !empty($_COOKIE['_profile']);
    },
    'profiler.flags' => array(
        ProfilingFlags::CPU,
        ProfilingFlags::MEMORY,
        ProfilingFlags::NO_BUILTINS,
        ProfilingFlags::NO_SPANS,
    ),
    'profiler.options' => array(),
    'profiler.exclude-env' => array(),
    'profiler.simple_url' => function ($url) {
        return preg_replace('/=\d+/', '', $url);
    },
    'profiler.replace_url' => null,
);
