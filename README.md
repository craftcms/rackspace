Rackspace Cloud Files for Craft CMS
===================================

This plugin provides an [Rackspace Cloud Files](https://www.rackspace.com/cloud/files) integration for [Craft CMS](https://craftcms.com/).


## Requirements

This plugin requires Craft CMS 3.0.0-beta.1 or later.


## Installation

### For Composer-based Craft installs

If you installed Craft via [Composer](https://getcomposer.org/), follow these instructions:

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to install the plugin:

        php composer.phar require craftcms/rackspace

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for Rackspace Cloud Files.


### For manual Craft installs

If you installed Craft manually, you will need to install this plugin manually as well.

1. [Download the zip](https://github.com/craftcms/rackspace/archive/master.zip), and extract it to your craft/plugins/ folder, renamed to “rackspace” (no “-master”).
2. Open your terminal and go to your craft/plugins/rackspace/ folder:

        cd /path/to/project/craft/plugins/rackspace 

3. Install Composer into the folder by running the commands listed at [getcomposer.org/download](https://getcomposer.org/download/).
    - **Note:** If you get an error running the first line, you may need to change `https` to `http`.

4. Once Composer is installed, tell it to install the plugin’s dependencies:

        php composer.phar install

5. In the Control Panel, go to Settings → Plugins and click the “Install” button for Rackspace Cloud Files.

## Setup

To create a new asset volume for your Rackspace Cloud Files bucket, go to Settings → Assets, create a new volume, and set the Volume Type setting to “Rackspace Cloud Files”.
