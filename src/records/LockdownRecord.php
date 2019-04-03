<?php
/**
 * Lockdown plugin for Craft CMS 3.x
 *
 * Whitelist your control panel by IP address
 *
 * @link      https://trendyminds.com
 * @copyright Copyright (c) 2019 TrendyMinds
 */

namespace trendyminds\lockdown\records;

use trendyminds\lockdown\Lockdown;

use Craft;
use craft\db\ActiveRecord;

/**
 * @author    TrendyMinds
 * @package   Lockdown
 * @since     1.0.0
 */
class LockdownRecord extends ActiveRecord
{
    // Public Static Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%lockdown_restrictions}}';
    }
}
