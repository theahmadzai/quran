<?php

namespace Quran\Http\Interfaces;

interface UrlInterface
{
    const DEFAULT_SCHEME = 'http';

    const DEFAULT_HOST = 'staging.quran.com';

    const DEFAULT_PORT = 3000;

    const DEFAULT_BASE_PATH = 'api/v3';

    const DEFAULT_QUERY = 'language=en';

    public function getScheme();

    public function getHost();

    public function getPort();

    public function getPath();

    public function getQuery();
}
