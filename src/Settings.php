<?php

namespace craft\applenews;

use craft\base\Model;

/**
 * Settings
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since 2.0
 */
class Settings extends Model
{
    /**
     * @var array
     */
    public array $channels = [];

    /**
     * @var bool
     */
    public bool $autoPublishOnSave = true;

    /**
     * @var int
     */
    public int $httpClientTimeout = 30;
}
