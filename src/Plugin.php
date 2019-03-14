<?php
/**
 * @link      https://craftcms.com/
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 * @license   MIT
 */

namespace saboteur777\deleteentries;

use Craft;
use craft\elements\User;
use craft\models\Section;

/**
 * Class Plugin
 *
 * @property Settings $settings
 * @method Settings getSettings()
 */
class Plugin extends \craft\base\Plugin
{
    // Properties
    // =========================================================================

    /**
     * @inheritdoc
     */
    public $schemaVersion = '0.1.0';

    /**
     * @inheritdoc
     */
    public $hasCpSettings = false;

}
