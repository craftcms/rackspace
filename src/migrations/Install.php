<?php
/**
 * @link      https://craftcms.com/
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 * @license   https://craftcms.com/license
 */

namespace craft\rackspace\migrations;

use Craft;
use craft\base\Volume;
use craft\db\Migration;
use craft\errors\VolumeException;
use craft\volumes\MissingVolume;

/**
 * Installation Migration
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since  3.0
 */
class Install extends Migration
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        // Convert any built-in Rackspace volumes to ours
        $volumesService = Craft::$app->getVolumes();
        /** @var Volume[] $allVolumes */
        $allVolumes = $volumesService->getAllVolumes();

        foreach ($allVolumes as $volume) {
            if ($volume instanceof MissingVolume && $volume->expectedType === 'craft\volumes\Rackspace') {
                /** @var Volume $convertedVolume */
                $convertedVolume = $volumesService->createVolume([
                    'id' => $volume->id,
                    'type' => Volume::class,
                    'name' => $volume->name,
                    'handle' => $volume->handle,
                    'hasUrls' => $volume->hasUrls,
                    'url' => $volume->url,
                    'settings' => $volume->settings
                ]);
                $convertedVolume->setFieldLayout($volume->getFieldLayout());

                if (!$volumesService->saveVolume($convertedVolume)) {
                    throw new VolumeException('Unable to convert the legacy “{volume}” Rackspace volume.', ['volume' => $volume->name]);
                }
            }
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        return false;
    }
}
