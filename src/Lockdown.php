<?php
/**
 * Lockdown plugin for Craft CMS 3.x
 *
 * Whitelist your control panel by IP address
 *
 * @link      https://trendyminds.com
 * @copyright Copyright (c) 2019 TrendyMinds
 */

namespace trendyminds\lockdown;

use trendyminds\lockdown\services\LockdownService as LockdownServiceService;
use trendyminds\lockdown\models\Settings;

use Craft;
use craft\base\Plugin;
use craft\web\UrlManager;
use craft\events\RegisterUrlRulesEvent;

use yii\base\Event;

/**
 * Class Lockdown
 *
 * @author    TrendyMinds
 * @package   Lockdown
 * @since     1.0.0
 *
 * @property  LockdownServiceService $lockdownService
 */
class Lockdown extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var Lockdown
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $schemaVersion = '1.0.0';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                if ($this->getSettings()->enabled) {
                    Lockdown::$plugin->lockdownService->verifyAccess();
                }

                $event->rules = array_merge($event->rules, [
                    "lockdown" => "lockdown/default/index",
                    "lockdown/new" => "lockdown/default/new",
                    "lockdown/edit/<id:\d+>" => "lockdown/default/edit"
                ]);
            }
        );

        Craft::info(
            Craft::t(
                'lockdown',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    public function getSidebarLabel()
    {
        return Craft::t('lockdown', $this->getSettings()->sidebarLabel);
    }

    // Protected Methods
    // =========================================================================
    protected function createSettingsModel()
    {
        return new Settings();
    }

    public function getCpNavItem()
    {
        $navItem = parent::getCpNavItem();
        $navItem['label'] = $this->getSidebarLabel();
        return $navItem;
    }

    /**
     * @inheritdoc
     */
    protected function settingsHtml(): string
    {
        return Craft::$app->view->renderTemplate('lockdown/settings', [
            'settings' => $this->getSettings()
        ]);
    }
}
