<?php namespace Me\Manager;

use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function registerComponents()
    {
    	return [
            \Me\Manager\Components\Api::class       => 'api',
        ];
    }

    public function registerSettings()
    {
    }
}
