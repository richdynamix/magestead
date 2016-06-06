<?php

namespace Magestead\Installers;

/**
 * Class Project.
 */
class Project
{
    /**
     * @param array $options
     * @param array $config
     * @param $projectPath
     * @param $output
     *
     * @return Magento2Project|MagentoProject
     */
    public static function create(array $options, array $config, $projectPath, $output)
    {
        switch ($options['app']) {
            case 'magento':
                return new MagentoProject($options, $config, $projectPath, $output);
            break;
            case 'magento2':
                return new Magento2Project($options, $config, $projectPath, $output);
            break;
        }
    }
}
