# UWO Gravity Forms/Jira Data Request Integration

This plugin extends [Gravity Forms](https://www.gravityforms.com/) functionality using the [Gravity Forms Add-on Framework](https://docs.gravityforms.com/add-on-framework/). The purpose of this plugin is to create Jira issues when a Gravity Forms is submitted. This functionality allows us to collect new Data Requests submitted by the UWO campus so we may accept, process, or reject them in Jira.

## Global Settings

Upon activating this plugin you may reach the global settings page by going to Gravity Forms > Settings > Data Requests Add-On. Here you will find 5 settings: Jira Site, Jira Username, Jira API Key, Project Key, and Project Name. The first 3 are purposed for authenticating to the Jira installation, and the last two are purposed for selecting the proper project inside the Jira installation. These are all required to be set up before issues will be sent to your Jira installation.

## Form-Specific Settings

To activate the Jira integration on a specific form in your site go to Forms > Hover over the form of your choosing > Settings > Data Requests Add-On. Here you can activate the data requests form and design the format for the Jira issue summary and description using Gravity Forms merge tags from form design.
