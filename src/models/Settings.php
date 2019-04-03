<?php
/**
 * Lockdown plugin for Craft CMS 3.x
 *
 * Force users to only access a subset of your entries
 *
 * @link      https://trendyminds.com
 * @copyright Copyright (c) 2019 TrendyMinds
 */

namespace trendyminds\lockdown\models;

use trendyminds\lockdown\Lockdown;

use Craft;
use craft\base\Model;

/**
 * @author    TrendyMinds
 * @package   Lockdown
 * @since     1.0.0
 */
class Settings extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $sidebarLabel = 'Lockdown';
    public $enabled = true;
    public $template = '';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['sidebarLabel', 'string'],
            ['sidebarLabel', 'default', 'value' => 'Lockdown'],

            ['template', 'string'],
            ['template', 'default', 'value' => ''],

            ['enabled', 'boolean'],
            ['enabled', 'default', 'value' => false],
        ];
    }
}
