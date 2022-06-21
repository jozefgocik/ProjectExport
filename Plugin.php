<?php

namespace Kanboard\Plugin\ProjectExport;

use Kanboard\Core\Plugin\Base;
use Kanboard\Core\Translator;

class Plugin extends Base
{
    public function initialize()
    {
        $this->template->setTemplateOverride('export/tasks', 'ProjectExport:export/tasks');
        $this->template->setTemplateOverride('header/title', 'ProjectExport:header/title');
        $this->hook->on("template:layout:css", array("template" => "plugins/ProjectExport/Assets/ProjectOverview.css"));
    }

    public function onStartup()
    {
        Translator::load($this->languageModel->getCurrentLanguage(), __DIR__ . '/Locale');
    }

    public function getClasses()
    {
        return array(
            'Plugin\ProjectExport\Export' => array(
                'TaskExport',
            ),
        );
    }

    public function getPluginName()
    {
        return 'ProjectExport';
    }

    public function getPluginDescription()
    {
        return t('Add option to export chosen data from project in form of HTML table.');
    }

    public function getPluginAuthor()
    {
        return 'Matej Kovaľ';
    }

    public function getPluginVersion()
    {
        return '1.0.0';
    }

    public function getPluginHomepage()
    {
        return 'https://github.com/kovalmatej/ProjectExport';
    }
}
