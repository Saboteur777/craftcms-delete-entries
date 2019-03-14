<?php
/**
 * @link      https://craftcms.com/
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 * @license   MIT
 */

namespace saboteur777\deleteentries\events;

use craft\elements\Entry;
use craft\events\CancelableEvent;

/**
 * SaveEvent class
 */
class DeleteEvent extends CancelableEvent
{
    // Properties
    // =========================================================================

    /**
     * @var Entry The guest entry submission
     */
    public $entry;
}
