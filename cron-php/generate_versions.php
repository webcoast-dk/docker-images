<?php

foreach ($phpVersions as $phpVersion) {
    foreach ($distributions as $distribution => $distConfig) {
        if (version_compare($phpVersion, $distConfig['minPhpVersion'], '>=') && version_compare($phpVersion, $distConfig['maxPhpVersion'], '<=')) {
            $versionsToBuild["php${phpVersion}-${distribution}"] = [
                'tag' => "php${phpVersion}-${distribution}",
                'phpVersion' => $phpVersion,
                'distribution' => $distribution
            ];
        }
    }
}
