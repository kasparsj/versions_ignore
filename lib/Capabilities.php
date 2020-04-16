<?php

declare(strict_types=1);

namespace OCA\Versions_Ignore;

use OCP\Capabilities\ICapability;

class Capabilities implements ICapability {

    /**
     * Function an app uses to return the capabilities
     *
     * @return array Array containing the apps capabilities
     * @since 8.2.0
     */
    public function getCapabilities() {
        $capabilities = [
            'files' =>
            [
                'versioning' => true,
            ]
        ];

        return $capabilities;
    }
}