<?php
/**
 * Lockdown plugin for Craft CMS 3.x
 *
 * Whitelist your control panel by IP address
 *
 * @link      https://trendyminds.com
 * @copyright Copyright (c) 2019 TrendyMinds
 */

namespace trendyminds\lockdown\controllers;

use trendyminds\lockdown\Lockdown;

use Craft;
use craft\web\Controller;
use craft\helpers\UrlHelper;
use trendyminds\lockdown\records\LockdownRecord;

/**
 * @author    TrendyMinds
 * @package   Lockdown
 * @since     1.0.0
 */
class DefaultController extends Controller
{
    public function actionIndex()
    {
        $restrictions = LockdownRecord::find()->all();

        return $this->renderTemplate('lockdown/index', [
            "restrictions" => $restrictions
        ]);
    }

    public function actionEdit($id)
    {
        $entry = LockdownRecord::findOne([
            "id" => $id
        ]);

        return $this->renderTemplate('lockdown/entry', [
            "entry" => $entry
        ]);
    }

    public function actionSave()
    {
        $this->requirePostRequest();
        $session = Craft::$app->getSession();

        $id = Craft::$app->getRequest()->getBodyParam("id");
        $ipAddressStart = Craft::$app->getRequest()->getBodyParam("ipAddressStart");
        $ipAddressEnd = Craft::$app->getRequest()->getBodyParam("ipAddressEnd");
        $notes = Craft::$app->getRequest()->getBodyParam("notes");

        $success = Lockdown::$plugin->lockdownService->saveRecord(
            $id,
            $ipAddressStart,
            $ipAddressEnd,
            $notes
        );

        if (!$success) {
            $session->setError("An error occurred saving your restriction. Make sure your IP address is properly formatted");
            return $this->redirect(UrlHelper::cpUrl(Craft::$app->request->getPathInfo()));
        }

        $session->setNotice("Your restriction was created successfully");

        return $this->redirect(UrlHelper::cpUrl("lockdown"));
    }

    public function actionNew()
    {
        return $this->renderTemplate('lockdown/entry');
    }

    public function actionDelete()
    {
        $this->requirePostRequest();

        $session = Craft::$app->getSession();

        $id = Craft::$app->getRequest()->getBodyParam("id");

        $record = LockdownRecord::findOne([
            "id" => $id
        ]);

        if ($record) {
            $record->delete();

            return $this->asJson(['success' => true]);
        }

        return $this->asJson(['success' => false]);
    }
}
