<?php

namespace Quran\Interfaces;

interface QuranInterface
{
    /**
     * Default API url
     */
    const DEFAULT_URL = "http://staging.quran.com:3000/api/v3/?language=en";

    /**
     * Default langauge
     */
    const DEFAULT_LANGUAGE = 'en';

    /**
     * Default Options, for listing them
     */
    const DEFAULT_OPTIONS = ['recitations', 'translations', 'languages', 'tafsirs'];
}
