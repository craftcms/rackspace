<?php

namespace craft\plugins\rackspace\controllers;

use Craft;
use craft\app\web\Controller as BaseController;
use craft\plugins\rackspace\Volume;
use yii\base\UserException;
use yii\web\Response;

/**
 * Rackspace controller provides functionality to load container data for Rackspace.
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since  3.0
 */

class RackspaceController extends BaseController
{
    /**
     * Load container data
     *
     * This is used to, for example, load Amazon S3 bucket list or Rackspace Cloud Storage Containers.
     *
     * @return Response
     */
    public function actionLoadContainerData()
    {
        $this->requirePostRequest();
        $this->requireAcceptsJson();

        $request = Craft::$app->getRequest();
        $username = $request->getRequiredBodyParam('username');
        $apiKey = $request->getRequiredBodyParam('apiKey');
        $region = $request->getRequiredBodyParam('region');

        try {
            return $this->asJson(Volume::loadContainerList($username, $apiKey, $region));
        } catch (UserException $e) {
            return $this->asErrorJson($e->getMessage());
        }
    }
}