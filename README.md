# Rackspace Cloud Files for Craft CMS

This plugin provides an [Rackspace Cloud Files](https://www.rackspace.com/cloud/files) integration for [Craft CMS](https://craftcms.com/).

**:warning: Rackspace is no longer maintaining their PHP OpenCloud SDK, so you should probably find a different asset storage provider and not use this plugin.**

## Requirements

This plugin requires Craft CMS 3.1 or later.

## Installation

You can install this plugin from the Plugin Store or with Composer.

#### From the Plugin Store

Go to the Plugin Store in your project’s Control Panel and search for “Rackspace”. Then click on the “Install” button in its modal window.

#### With Composer

Open your terminal and run the following commands:

```bash
# go to the project directory
cd /path/to/my-project.test

# tell Composer to load the plugin
composer require craftcms/rackspace

# tell Craft to install the plugin
./craft install/plugin rackspace
```

## Setup

To create a new asset volume for your Rackspace Cloud Files bucket, go to Settings → Assets, create a new volume, and set the Volume Type setting to “Rackspace Cloud Files”.
