<?php

namespace craft\rackspace\controllers;

use Craft;
use craft\rackspace\Volume;
use craft\web\Controller as BaseController;
use yii\base\UserException;
use yii\web\Response;

/**
 * This controller provides functionality to load data from Rackspace.
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since 3.0
 */
class DefaultController extends BaseController
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->defaultAction = 'load-container-data';
    }

    /**
     * Load container data for specified credentials and region.
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
