<?php
/**
 * @link https://craftcms.com/
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 * @license MIT
 */

namespace craft\rackspace;

use Craft;
use craft\base\FlysystemVolume;
use League\Flysystem\Rackspace\RackspaceAdapter;
use OpenCloud\Common\Service\Catalog;
use OpenCloud\Common\Service\CatalogItem;
use OpenCloud\Identity\Resource\Token;
use OpenCloud\OpenStack;
use OpenCloud\Rackspace;
use yii\base\UserException;

/**
 * Class Volume
 *
 * @property null|string $settingsHtml
 * @property string $rootUrl
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since 3.0
 */
class Volume extends FlysystemVolume
{
    /**
     * Cache key to use for caching purposes
     */
    const CACHE_KEY_PREFIX = 'rackspace.';

    // Static
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return 'Rackspace Cloud Files';
    }

    // Properties
    // =========================================================================

    /**
     * @var bool Whether this is a local source or not. Defaults to false.
     */
    protected $isSourceLocal = false;

    /**
     * @var string Path to the root of this sources local folder.
     */
    public $subfolder = '';

    /**
     * @var string Rackspace username
     */
    public $username = '';

    /**
     * @var string Rackspace API key
     */
    public $apiKey = '';

    /**
     * @var string Container to use
     */
    public $container = '';

    /**
     * @var string Region to use
     */
    public $region = '';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->foldersHaveTrailingSlashes = false;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules[] = [['username', 'apiKey', 'region', 'container'], 'required'];

        return $rules;
    }

    /**
     * @inheritdoc
     * @return string|null
     */
    public function getSettingsHtml()
    {
        return Craft::$app->getView()->renderTemplate('rackspace/volumeSettings', [
            'volume' => $this
        ]);
    }

    /**
     * Get the container list list using the specified credentials for the region.
     *
     * @param $username
     * @param $apiKey
     * @param $region
     * @throws UserException
     * @return array
     */
    public static function loadContainerList($username, $apiKey, $region)
    {
        if (empty($username) || empty($apiKey) || empty($region)) {
            throw new UserException(Craft::t('rackspace', 'You must specify a username, the API key and a region to get the container list.'));
        }

        $client = static::client($username, $apiKey);

        $service = $client->objectStoreService('cloudFiles', $region);

        $containerList = $service->getCdnService()->listContainers();

        $returnData = [];

        while ($container = $containerList->next()) {
            $returnData[] = (object)[
                'container' => $container->name,
                'urlPrefix' => rtrim($container->getCdnUri(), '/').'/'
            ];
        }

        return $returnData;
    }

    /**
     * @inheritdoc
     */
    public function getRootUrl()
    {
        if (($rootUrl = parent::getRootUrl()) !== false && $this->subfolder) {
            $rootUrl .= rtrim($this->subfolder, '/').'/';
        }
        return $rootUrl;
    }


    // Overrides to ensure whitespaces and non-ASCII characters work.
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function getFileMetadata(string $uri): array
    {
        return parent::getFileMetadata(urlencode($uri));
    }

    /**
     * @inheritdoc
     */
    public function createFileByStream(string $path, $stream, array $config)
    {
        parent::createFileByStream(urlencode($path), $stream, $config);
    }

    /**
     * @inheritdoc
     */
    public function updateFileByStream(string $path, $stream, array $config)
    {
        parent::updateFileByStream(urlencode($path), $stream, $config);
    }

    /**
     * @inheritdoc
     */
    public function createDir(string $path)
    {
        parent::createDir(urlencode($path));
    }

    /**
     * @inheritdoc
     */
    public function fileExists(string $path): bool
    {
        return parent::fileExists(urlencode($path));
    }

    /**
     * @inheritdoc
     */
    public function folderExists(string $path): bool
    {
        return parent::folderExists(urlencode($path));
    }

    /**
     * @inheritdoc
     */
    public function renameFile(string $path, string $newPath)
    {
        parent::renameFile(urlencode($path), urlencode($newPath));
    }

    /**
     * @inheritdoc
     */
    public function deleteFile(string $path)
    {
        parent::deleteFile(urlencode($path));
    }

    /**
     * @inheritdoc
     */
    public function copyFile(string $path, string $newPath)
    {
        parent::copyFile(urlencode($path), urlencode($newPath));
    }

    /**
     * @inheritdoc
     */
    public function getFileStream(string $uriPath)
    {
        return parent::getFileStream(urlencode($uriPath));
    }

    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     * @return RackspaceAdapter
     */
    protected function createAdapter()
    {
        $client = static::client($this->username, $this->apiKey);

        $store = $client->objectStoreService('cloudFiles', $this->region);
        $container = $store->getContainer($this->container);

        return new RackspaceAdapter($container, $this->subfolder);
    }

    /**
     * Get the AWS S3 client.
     *
     * @param $username
     * @param $apiKey
     * @return OpenStack
     */
    protected static function client($username, $apiKey)
    {
        $config = ['username' => $username, 'apiKey' => $apiKey];

        $client = new Rackspace(Rackspace::US_IDENTITY_ENDPOINT, $config);

        // Check if we have a cached token
        $tokenKey = static::CACHE_KEY_PREFIX.md5($username.$apiKey);
        if (Craft::$app->cache->exists($tokenKey)) {
            $credentials = unserialize(Craft::$app->cache->get($tokenKey), [
                'allowed_classes' => [Catalog::class, CatalogItem::class, \StdClass::class]
            ]);
            $client->importCredentials($credentials);
        }

        /** @var Token $token */
        $token = $client->getTokenObject();

        // If it's not a valid token, re-authenticate and store the token
        if (!$token || ($token && $token->hasExpired())) {
            $client->authenticate();
            $tokenData = $client->exportCredentials();
            Craft::$app->cache->set($tokenKey, serialize($tokenData));
        }

        return $client;
    }
}
