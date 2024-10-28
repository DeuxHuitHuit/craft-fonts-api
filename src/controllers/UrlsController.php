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
            return $this->asJson(['error' => "The fonts directory `$dir` does not exist"]);
        }

        $result = $this->recursiveScanDir($dir);
        if (empty($result['dirs'])) {
            // Return the urls only, like the non-recursive version did
            return $this->asJson($result['files']);
        } else if (empty($result['files'])) {
            // Return the dirs only
           return $this->asJson($result['dirs']);
        }

        // Return the result as a JSON response
        return $this->asJson($result);
    }

    private function recursiveScanDir(string $dir): array
    {
        $files = [];
        $dirs = [];
        $inodes = scandir($dir);
        foreach ($inodes as $inode) {
            if ($inode === '.' || $inode === '..') {
                continue;
            }
            if (is_dir($dir . '/' . $inode)) {
                $dirs[$inode] = $this->recursiveScanDir($dir . '/' . $inode);
            } else {
                $files[] = $inode;
            }
        }
        if (empty($files)) {
            return ['dirs' => $dirs];
        } else if (empty($dirs)) {
            return ['files' => $files];
        }
        return ['files' => $files, 'dirs' => $dirs];
    }
}
