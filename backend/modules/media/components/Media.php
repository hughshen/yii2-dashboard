<?php

namespace backend\modules\media\components;

use Yii;
use yii\db\Exception;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;

class Media
{
    const MAX_FILE_SIZE = 10 * 1024 * 1024;
    const ALLOW_EXTENSIONS = ['jpg', 'png', 'gif', 'jpeg'];
    const ALLOW_MIME_TYPES = ['image/jpeg', 'image/png', 'image/gif', 'image/jpeg'];
    const UPLOAD_FOLDER = 'uploads';
    const IGNORE_FOLDERS = ['resize'];

    public static function rootPath()
    {
        return Yii::getAlias('@frontend/web');
    }

    public static function uploadFolderName($withSlash = false)
    {
        if ($withSlash) {
            return '/' . self::UPLOAD_FOLDER . '/';
        }

        return self::UPLOAD_FOLDER;
    }

    public static function uploadFolderExists()
    {
        return is_dir(self::rootPath() . self::uploadFolderName(true));
    }

    public static function folderPath($folder = '')
    {
        $folder = $folder ? (trim($folder, '/') . '/') : '';

        return self::uploadFolderName(true) . $folder;
    }

    public static function fullFolderPath($folder = '')
    {
        return self::rootPath() . self::folderPath($folder);
    }

    public static function genFilePath($fileName, $folder = '')
    {
        return self::folderPath($folder) . $fileName;
    }

    public static function genFileName($extension = '')
    {
        if (!empty($extension)) {
            $extension = '.' . trim($extension, '.');
        }

        return time() . mt_rand(1100, 9900) . $extension;
    }

    public static function createUploadFolder($mode = 0755, $recursive = true)
    {
        if (self::uploadFolderExists()) {
            return true;
        }

        try {
            if (!FileHelper::createDirectory(self::fullFolderPath(), $mode, $recursive)) {
                return false;
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public static function getFileList($searchFolder = '', $search = '')
    {
        $fullPath = self::fullFolderPath($searchFolder);
        if (in_array($searchFolder, self::IGNORE_FOLDERS) || !file_exists($fullPath)) return [[], 0];

        // Get folders
        $folders = glob("{$fullPath}*{$search}*", GLOB_ONLYDIR);
        $folders = $folders ? $folders : [];
        $newFolders = [];
        foreach ($folders as $val) {
            if (!in_array(basename($val), self::IGNORE_FOLDERS)) {
                $newFolders[] = $val;
            }
        }

        // If has search folder
        if ($searchFolder) {
            array_unshift($newFolders, $searchFolder);
        }

        // Get files
        $extList = implode(',', self::ALLOW_EXTENSIONS);
        $extListUpper = implode(',', array_map('strtoupper', self::ALLOW_EXTENSIONS));
        $extList = "{$extList},{$extListUpper}";
        $files = glob("{$fullPath}*{$search}*{{$extList}}", GLOB_BRACE);

        $list = array_merge($newFolders, $files);
        $listCount = count($list);

        return [$list, $listCount];
    }

    public static function getFileData($list, $offset = 0, $limit = 12)
    {
        $list = array_splice($list, $offset, $limit);

        $data = [];
        $prevFolder = [];
        foreach ($list as $key => $val) {
            $name = basename($val);
            if (is_dir($val)) {
                $arr = explode(self::UPLOAD_FOLDER, $val);
                $data[] = [
                    'thumb' => '',
                    'name' => $name,
                    'type' => 'folder',
                    'path' => (string)substr($arr['1'], 1),
                ];
            } elseif (is_file($val)) {
                $arr = explode(self::rootPath(), $val);
                $data[] = [
                    'thumb' => $arr[1],
                    'name' => $name,
                    'type' => 'image',
                    'path' => $arr['1'],
                ];
            } else {
                $prevPath = self::fullFolderPath($val);
                $prevPath = dirname($prevPath);
                if (is_dir($prevPath)) {
                    $arr = explode(self::UPLOAD_FOLDER, $prevPath);
                    $prevFolder = [
                        'thumb' => '',
                        'name' => '..',
                        'type' => 'folder',
                        'path' => (string)substr($arr['1'], 1),
                    ];
                }
            }
        }

        if ($prevFolder) {
            array_unshift($data, $prevFolder);
        }

        return $data;
    }

    public static function saveFile($file, $filePath)
    {
        if ($file instanceof UploadedFile) {
            $fullPath = self::rootPath() . $filePath;
            if ($file->saveAs($fullPath)) {
                // compress($fullPath, $fullPath);
                return true;
            }

            @unlink($file->tempName);
        }

        return false;
    }

    public static function saveFiles($files, $folder = '')
    {
        self::createUploadFolder();

        $files = is_array($files) ? $files : [$files];
        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $fileName = $file->name;
                $fileName = preg_replace('/\s+/', '-', $fileName);
                $filePath = self::genFilePath($fileName, $folder);

                if (
                    file_exists(self::rootPath() . $filePath) ||
                    preg_match('/[\x7f-\xff]/', $fileName) ||
                    strpos($fileName, '.') === 0
                ) {
                    $filePath = self::genFilePath(self::genFileName($file->extension), $folder);
                }

                self::saveFile($file, $filePath);
            }
        }
    }

    public static function deleteFile($filePath)
    {
        return @unlink(self::rootPath() . $filePath);
    }

    public static function createFolder($folder, $mode = 0755, $recursive = true)
    {
        self::createUploadFolder();

        $path = self::fullFolderPath() . ltrim($folder, '/');

        return FileHelper::createDirectory($path, $mode, $recursive);
    }

    public static function deletePath($path, $type = null)
    {
        if (!$path || in_array($path, self::IGNORE_FOLDERS)) return false;

        if ($type === 'folder') {
            $fullPath = self::fullFolderPath($path);
        } else {
            $fullPath = self::rootPath() . $path;
        }

        if (is_file($fullPath)) {
            return @unlink($fullPath);
        } elseif (is_dir($fullPath)) {
            $files = [];
            $paths = [$fullPath];
            while (count($paths) != 0) {
                $nextPath = array_shift($paths);
                foreach (glob($nextPath) as $file) {
                    if (is_dir($file)) {
                        $paths[] = rtrim($file, '/') . '/*';
                    }
                    $files[] = realpath($file);
                }
            }

            rsort($files);
            foreach ($files as $file) {
                if (is_file($file)) {
                    @unlink($file);
                } elseif (is_dir($file)) {
                    rmdir($file);
                }
            }
        }

        return true;
    }

    public static function saveByData($encodedData, $fileName = null)
    {
        @list($type, $data) = explode(';', $encodedData);
        @list(, $data) = explode(',', $data);

        $data = base64_decode($data);

        if ($type && $data) {
            @list(, $ext) = explode('/', $type);

            if ($ext == 'jpeg') $ext = 'jpg';

            try {
                if (!$ext || !in_array($ext, self::ALLOW_EXTENSIONS)) {
                    throw new \yii\base\Exception(Yii::t('app', 'Only files with these extensions are allowed: ') . implode(', ', self::ALLOW_EXTENSIONS));
                }

                if ($fileName) {
                    $fileName = self::genFileName($ext);
                } else {
                    $fileName = "{$fileName}.{$ext}";
                }
                $filePath = self::genFilePath($fileName);

                if (
                    file_exists(self::rootPath() . $filePath) ||
                    preg_match('/[\x7f-\xff]/', $fileName)
                ) {
                    $filePath = self::genFilePath(self::genFileName($ext));
                }

                $fullPath = self::rootPath() . $filePath;
                $writeRes = file_put_contents($fullPath, $data);

                if ($writeRes) {
                    $fileSize = filesize($fullPath);
                    if ($fileSize > self::MAX_FILE_SIZE) {
                        @unlink($fullPath);

                        throw new \yii\base\Exception(Yii::t('app', 'The file is too big. Its size cannot exceed {number} B', [
                            'number' => self::MAX_FILE_SIZE,
                        ]));
                    } else {
                        return $filePath;
                    }
                } else {
                    throw new \yii\base\Exception(Yii::t('app', 'Upload failed'));
                }
            } catch (\Exception $e) {
                throw $e;
            }
        }

        return false;
    }
}
