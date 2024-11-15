<?php

/**
 * @author    3Liz
 * @copyright 2024 3liz.com
 *
 * @see      3liz.com
 *
 * @license   MIT
 */

namespace Lizmap\Project;

class UserFilesFinder
{
    public function listFileURLS(Project $project, $ignoreRepoAllowUserDefined = false)
    {
        $jsUrls = array();
        $mjsUrls = array();
        $cssUrls = array();
        if ($ignoreRepoAllowUserDefined && $project->getRepository()->allowUserDefinedThemes()) {

            $jsDirArray = array('default', $project->getKey());
            $repositoryPath = $project->getRepository()->getPath();
            foreach ($jsDirArray as $dir) {

                $items = array(
                    // current repository
                    'media/js/',
                    // or root (js shared over repositories)
                    '../media/js/',
                );
                foreach ($items as $item) {
                    $jsPathRoot = realpath($repositoryPath.$item.$dir);
                    if (is_dir($jsPathRoot)) {
                        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($jsPathRoot)) as $filename) {
                            $fileExtension = pathinfo($filename, PATHINFO_EXTENSION);
                            if ($fileExtension == 'js' || $fileExtension == 'mjs' || $fileExtension == 'css') {
                                $jsPath = realpath($filename);
                                $jsRelPath = $item.$dir.str_replace($jsPathRoot, '', $jsPath);
                                $url = 'view~media:getMedia';
                                if ($fileExtension == 'css') {
                                    $url = 'view~media:getCssFile';
                                }
                                $jsUrl = \jUrl::get(
                                    $url,
                                    array(
                                        'repository' => $project->getRepositoryKey(),
                                        'project' => $project,
                                        'mtime' => filemtime($filename),
                                        'path' => $jsRelPath,
                                    )
                                );
                                if ($fileExtension == 'js') {
                                    $jsUrls[] = $jsUrl;

                                } elseif ($fileExtension == 'mjs') {
                                    $mjsUrls[] = $jsUrl;

                                } else {
                                    $cssUrls[] = $jsUrl;
                                }
                            }
                        }
                    }
                }
            }
        }

        return array('css' => $cssUrls, 'js' => $jsUrls, 'mjs' => $mjsUrls);
    }
}
