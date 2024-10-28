<?php

// A Craft CMS 5 controller that returns the fonts filenames as urls
namespace deuxhuithuit\fontsapi\controllers;

use yii\web\Response;
use Craft;

class UrlsController extends \craft\web\Controller
{
    protected array|bool|int $allowAnonymous = true;

    public function actionIndex(): Response
    {
        // Read the /web/fonts folder to get the font filenames
        $dir = Craft::getAlias('@webroot/fonts');
        if (!is_dir($dir)) {
            return $this->asJson(['error' => "The fonts directory `$dir` does not exist"])->setStatusCode(404);
        }

        // Get the urls of the files and directories
        $result = $this->recursiveScanDir($dir);

        // Return the result as a JSON response
        return $this->asJson($result);
    }

    private function recursiveScanDir(string $dir, array $parents = []): array
    {
        $files = [];
        $dirs = [];
        $inodes = scandir($dir);
        foreach ($inodes as $inode) {
            if ($inode === '.' || $inode === '..') {
                continue;
            }
            if (is_dir($dir . '/' . $inode)) {
                $newParents = array_merge($parents, [$inode]);
                $dirs[$inode] = $this->recursiveScanDir($dir . '/' . $inode, $newParents);
            } else {
                $currentParents = empty($parents) ? '' : implode('/', $parents) . '/';
                $files[] = Craft::getAlias("@web/fonts/{$currentParents}{$inode}");
            }
        }
        if (empty($files)) {
            return $dirs;
        } else if (empty($dirs)) {
            return $files;
        }
        return array_merge($files, $dirs);
    }
}
