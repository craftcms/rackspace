<?php
/**
 * @link https://craftcms.com/
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 * @license https://craftcms.github.io/license/
 */

namespace craft\rackspace\migrations;

use Craft;
use craft\db\Migration;
use craft\db\Query;
use craft\errors\VolumeException;
use craft\helpers\Json;
use craft\rackspace\Volume;
use craft\services\Volumes;

/**
 * Installation Migration
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since 1.0
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
        $projectConfig = Craft::$app->getProjectConfig();
        $projectConfig->muteEvents = true;

        $volumes = $projectConfig->get(Volumes::CONFIG_VOLUME_KEY) ?? [];

        foreach ($volumes as $uid => &$volume) {
            if ($volume['type'] === Volume::class && isset($volume['settings']) && is_array($volume['settings'])) {
                $settings = $volume['settings'];

                $hasUrls = !empty($volume['hasUrls']);
                $url = ($hasUrls && !empty($settings['urlPrefix'])) ? $settings['urlPrefix'] : null;
                unset($settings['urlPrefix'], $settings['location']);

                $volume['url'] = $url;
                $volume['settings'] = $settings;

                $this->update('{{%volumes}}', [
                    'settings' => Json::encode($settings),
                    'url' => $url,
                ], ['uid' => $uid]);

                $projectConfig->set(Volumes::CONFIG_VOLUME_KEY . '.' . $uid, $volume);
            }
        }

        $projectConfig->muteEvents = false;
    }

    /**
     * Drops the obsolete rackspaceaccess table if it exists
     *
     * @return void
     */
    private function _dropRackspaceAccessTable()
    {
        $this->dropTableIfExists('{{%rackspaceaccess}}');
    }
}
