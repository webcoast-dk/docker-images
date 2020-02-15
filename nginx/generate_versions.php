<?php

$nginxVersions = explode(PHP_EOL, shell_exec("docker run --rm -i webcoast_docker_images_builder sh -c 'get-nginx-versions.sh|filter-versions.php \">= 1.15.0\"'"));

$localDistributions = ['', 'alpine'];

foreach ($nginxVersions as $nginxVersion) {
    foreach ($localDistributions as $distribution) {
        if (preg_match('/^(\d+\.\d+)/', $nginxVersion, $nginxMajorVersion)) {
            $tag = $nginxMajorVersion[1] . (!empty($distribution) ? '-' . $distribution : '');
            $versionsToBuild[$tag] = [
                'tag' => $tag,
                'nginxVersion' => $nginxMajorVersion[1],
                'distribution' => $distribution ? '-' . $distribution : ''
            ];
        }
    }
}
