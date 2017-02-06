Rackspace Cloud Files for Craft CMS
===================================

This plugin provides an [Rackspace Cloud Files](https://www.rackspace.com/cloud/files) integration for [Craft CMS](https://craftcms.com/).

**Note: Rackspace is no longer maintaining their PHP OpenCloud SDK, so you should probably find a different asset storage provider and not use this plugin.**


## Requirements

This plugin requires Craft CMS 3.0.0-beta.1 or later.


## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        php composer.phar require craftcms/rackspace

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for Rackspace Cloud Files.

## Setup

To create a new asset volume for your Rackspace Cloud Files bucket, go to Settings → Assets, create a new volume, and set the Volume Type setting to “Rackspace Cloud Files”.
