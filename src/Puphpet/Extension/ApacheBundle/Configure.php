<?php

namespace Puphpet\Extension\ApacheBundle;

use Puphpet\MainBundle\Extension;

use Symfony\Component\Yaml\Yaml;

class Configure extends Extension\ExtensionAbstract implements Extension\ExtensionInterface
{
    private $data = [];
    private $customData = [];

    protected $name = 'Apache';
    protected $slug = 'apache';

    protected $targetFile = 'puppet/manifests/default.pp';
    protected $sources = [
        'apache' => ":git => 'git://github.com/puppetlabs/puppetlabs-apache.git'",
    ];

    public function getFrontController()
    {
        return $this->container->get('puphpet.extension.apache.front_controller');
    }

    public function getManifestController()
    {
        return $this->container->get('puphpet.extension.apache.manifest_controller');
    }

    /**
     * Return all data needed for our templates
     *
     * @return array
     */
    public function getData()
    {
        $dataToMerge = empty($this->customData)
            ? Yaml::parse(__DIR__ . '/Resources/config/defaults.yml')
            : $this->customData;

        if ($this->returnAvailableData) {
            $dataToMerge = array_merge(
                $dataToMerge,
                $this->getAvailableData()
            );
        }

        $this->data = array_replace_recursive(
            $this->getDefaultData(),
            $dataToMerge
        );

        return $this->data;
    }

    /**
     * Add user-supplied values
     *
     * @param array $data
     * @return self
     */
    public function setCustomData(array $data = [])
    {
        $this->customData = $data;

        return $this;
    }

    /**
     * Our base data
     *
     * @return array
     */
    private function getDefaultData()
    {
        if (empty($this->data)) {
            $this->data = Yaml::parse(__DIR__ . '/Resources/config/data.yml');
        }

        return $this->data;
    }

    /**
     * Grab data to fill out available options
     *
     * @return array
     */
    private function getAvailableData()
    {
        $available = Yaml::parse(__DIR__ . '/Resources/config/available.yml');

        return is_array($available)
            ? $available
            : [];
    }
}
