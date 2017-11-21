<?php
/**
 * @link      https://craftcms.com/
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 * @license   https://craftcms.github.io/license/
 */

namespace craft\rackspace\migrations;

use Craft;
use craft\base\Volume as BaseVolume;
use craft\db\Migration;
use craft\db\Query;
use craft\errors\VolumeException;
use craft\helpers\Json;
use craft\rackspace\Volume;
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
        // Drop the rackspaceaccess table if it exists
        $this->_dropRackspaceAccessTable();

        // Convert any built-in Rackspace volumes to ours
        $this->_convertVolumes();

        return true;
    }

    // Private Methods
    // =========================================================================


    /**
     * Converts any old school Rackspace volumes to this one
     *
     * @return void
     * @throws VolumeException if the new volume couldn't be saved
     */
    private function _convertVolumes()
    {
        $volumes = (new Query())
            ->select([
                'id',
                'fieldLayoutId',
                'settings',
            ])
            ->where(['type' => 'craft\volumes\Rackspace'])
            ->from(['{{%volumes}}'])
            ->all();

        $dbConnection = Craft::$app->getDb();

        foreach ($volumes as $volume) {

            $settings = Json::decode($volume['settings']);

            if ($settings !== null) {
                $hasUrls = !empty($settings['publicURLs']);
                $url = ($hasUrls && !empty($settings['urlPrefix'])) ? $settings['urlPrefix'] : null;
                unset($settings['publicURLs'], $settings['urlPrefix']);

                $values = [
                    'type' => Volume::class,
                    'hasUrls' => $hasUrls,
                    'url' => $url,
                    'settings' => Json::encode($settings)
                ];

                $dbConnection->createCommand()
                    ->update('{{%volumes}}', $values, ['id' => $volume['id']])
                    ->execute();
            }
        }
    }

    /**
     * Drops the obsolete rackspaceaccess table if it exists
     *
     * @return void
     */
    private function _dropRackspaceAccessTable()
    {
        $table = '{{%rackspaceaccess}}';

        if ($this->db->tableExists($table)) {
            $this->dropTable('{{%rackspaceaccess}}');
        }
    }

}
