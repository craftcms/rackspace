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
        // Create the rackspaceaccess table
        $this->_createRackspaceAccessTable();

        // Convert any built-in Rackspace volumes to ours
        $this->_convertVolumes();

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        // Drop the rackspaceaccess table, but keep the volumes
        $this->dropTable('{{%rackspaceaccess}}');

        return true;
    }

    // Private Methods
    // =========================================================================

    /**
     * Creates the rackspaceaccess table if it doesn't already exist
     *
     * @return void
     */
    private function _createRackspaceAccessTable()
    {
        $table = '{{%rackspaceaccess}}';

        if ($this->db->tableExists($table)) {
            return;
        }

        $this->createTable($table, [
            'id' => $this->primaryKey(),
            'connectionKey' => $this->string()->notNull(),
            'token' => $this->string()->notNull(),
            'storageUrl' => $this->string()->notNull(),
            'cdnUrl' => $this->string()->notNull(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
        ]);

        $this->createIndex(
            $this->db->getIndexName($table, 'connectionKey', true),
            $table,
            'connectionKey',
            true);
    }

    /**
     * Converts any old school Rackspace volumes to this one
     *
     * @return void
     * @throws VolumeException if the new volume couldn't be saved
     */
    private function _convertVolumes()
    {
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
    }
}
