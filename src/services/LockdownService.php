<?php
/**
 * Lockdown plugin for Craft CMS 3.x
 *
 * Whitelist your control panel by IP address
 *
 * @link      https://trendyminds.com
 * @copyright Copyright (c) 2019 TrendyMinds
 */

namespace trendyminds\lockdown\services;

use trendyminds\lockdown\Lockdown;
use trendyminds\lockdown\records\LockdownRecord;

use Craft;
use craft\web\View;
use craft\base\Component;
use yii\web\ForbiddenHttpException;

/**
 * @author    TrendyMinds
 * @package   Lockdown
 * @since     1.0.0
 */
class LockdownService extends Component
{
    public function saveRecord($id, string $ipAddressStart, string $ipAddressEnd = "", string $notes = "")
    {
        if (!$id) {
            $record = new LockdownRecord;
        } else {
            $record = LockdownRecord::findOne(["id" => $id]);
        }

        if (
            !$this->_ipIsValid($ipAddressStart) ||
            ($ipAddressEnd !== "" && !$this->_ipIsValid($ipAddressEnd))
        ) {
            return false;
        }

        $record->setAttribute('ipAddressStart', $ipAddressStart);
        $record->setAttribute('ipAddressEnd', $ipAddressEnd);
        $record->setAttribute('notes', $notes);
        $record->setAttribute('siteId', Craft::$app->sites->getPrimarySite()->id);
        $record->save();

        return true;
    }

    public function verifyAccess()
    {
        $userIp = Craft::$app->request->getUserIP();
        $template = Lockdown::$plugin->getSettings()->template;

        $canAccess = $this->_canAccessCP($userIp);

        // If this user can access the control panel, allow them
        if ($canAccess) {
            return true;
        }

        // If we need to display a custom template, render it
        if ($template) {
            $oldMode = Craft::$app->view->getTemplateMode();
            Craft::$app->view->setTemplateMode(View::TEMPLATE_MODE_SITE);

            $html = Craft::$app->view->renderTemplate($template);
            Craft::$app->view->setTemplateMode($oldMode);

            echo $html;
            die();
        }

        // Otherwise display the generic "Access Denied" error
        throw new ForbiddenHttpException("Access Denied");
    }

    private function _ipIsValid(string $ipAddress)
    {
        return filter_var($ipAddress, FILTER_VALIDATE_IP);
    }

    private function _canAccessCP(string $ipAddress)
    {
        // Allow super admins to have access
        if (Craft::$app->user->getIsAdmin()) {
            return true;
        }

        // Allow local access
        if ($ipAddress === "127.0.0.1") {
            return true;
        }

        $records = LockdownRecord::find()->all();

        foreach ($records as $record) {
            $ipStart = ip2long($record->ipAddressStart);
            $ipEnd = ip2long($record->ipAddressEnd);

            // If there's not an ending IP but there's an exact match to an IP in the list allow the user access
            if (!$ipEnd && ip2long($ipAddress) === $ipStart) {
                return true;
            }

            // If the user's IP is within the given range allow them access
            if ($ipEnd && (ip2long($ipAddress) >= $ipStart && ip2long($ipAddress) <= $ipEnd)) {
                return true;
            }
        }
    }
}
