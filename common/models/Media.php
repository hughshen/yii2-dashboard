<?php

namespace common\models;

use Yii;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;

class Media
{
    const MAX_FILE_SIZE = 4 * 1024 * 1024;
    const ALLOW_EXTENSIONS = ['jpg', 'png', 'gif', 'jpeg'];
    const ALLOW_MIME_TYPES = ['image/jpeg', 'image/png', 'image/gif', 'image/jpeg'];
    const UPLOAD_FOLDER = 'uploads';
    const IGNORE_FOLDER = ['resize'];

    public static function checkUploadFolder()
    {
        return file_exists(self::uploadPath() . self::uploadFolderName(true));
    }

    public static function uploadPath()
    {
        return Yii::getAlias('@frontend/web');
    }

    public static function uploadFolderName($haveSlash = false)
    {
        if ($haveSlash) {
            return '/' . self::UPLOAD_FOLDER . '/';
        } else {
            return self::UPLOAD_FOLDER;
        }
    }

    public static function folderPath($folder = '')
    {
        $folder = $folder ? (trim($folder, '/') . '/') : '';
        return self::uploadFolderName(true) . $folder;
    }

    public static function fullPath($folder = '')
    {
        return self::uploadPath() . self::folderPath($folder);
    }

    public static function generateFilePath($fileName, $folder = '')
    {
        return self::folderPath($folder) . $fileName;
    }

    public static function generateFileName($extension = '')
    {
        if (!empty($extension)) {
            $extension = '.' . trim($extension, '.');
        }
        return time() . mt_rand(1100, 9900) . $extension;
    }

    public static function getList($searchFolder = '', $search = '')
    {
        $fullPath = self::fullPath($searchFolder);
        if (in_array($searchFolder, self::IGNORE_FOLDER) || !file_exists($fullPath)) return [];
        
        // Get folders
        $folders = glob("{$fullPath}*{$search}*", GLOB_ONLYDIR);
        $folders = $folders ? $folders : [];
        $newFolders = [];
        foreach ($folders as $val) {
            if (!in_array(basename($val), self::IGNORE_FOLDER)) {
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

    public static function getListData($list, $offset = 0, $limit = 12)
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
                    'name'  => $name,
                    'type'  => 'folder',
                    'path'  => (string)substr($arr['1'], 1),
                ];
            } elseif (is_file($val)) {
                $arr = explode(self::uploadPath(), $val);
                $data[] = [
                    'thumb' => self::resize($arr['1'], 100, 100),
                    'name'  => $name,
                    'type'  => 'image',
                    'path'  => $arr['1'],
                ];
            } else {
                $prevPath = self::fullPath($val);
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

    public static function deletePath($path)
    {
        if (!$path || in_array($path, self::IGNORE_FOLDER)) return;

        $fullPath = self::uploadPath() . $path;

        if (is_file($fullPath)) {
            return @unlink($fullPath);
        } elseif (is_dir($fullPath)) {
            return self::deleteFolder($fullPath);
        }

        return true;
    }

    public static function saveFile($file, $filePath)
    {
        if ($file instanceof UploadedFile) {
            $fullPath = self::uploadPath() . $filePath;
            if ($file->saveAs($fullPath)) {
                // compress($fullPath, $fullPath);
                return true;
            } else {
                return Yii::t('app', 'Save file fail.');
            }
            @unlink($file->tempName);
        } else {
            return Yii::t('app', 'Invalid file.');
        }
    }

    public static function saveFiles($files, $folder = '')
    {
        if (!self::checkUploadFolder()) {
            return Yii::t('app', 'Upload directory does not exist.');
        }

        if (!is_array($files)) $files = [$files];

        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $fileName = $file->name;
                $fileName = preg_replace('/\s+/', '-', $fileName);
                $filePath = self::generateFilePath($fileName, $folder);

                if (file_exists(self::uploadPath() . $filePath) || preg_match('/[\x7f-\xff]/', $fileName) || strpos($fileName, '.') === 0) {
                    $filePath = self::generateFilePath(self::generateFileName($file->extension), $folder);
                }

                self::saveFile($file, $filePath);
            }
        }
    }

    public static function deleteFile($filePath)
    {
        return @unlink(self::uploadPath() . $filePath);
    }

    public static function jsonUpload($encodedData, $fileName = null)
    {
        @list($type, $data) = explode(';', $encodedData);
        @list(, $data) = explode(',', $data);

        $data = base64_decode($data);

        if ($type && $data) {
            @list(, $ext) = explode('/', $type);

            if ($ext == 'jpeg') $ext = 'jpg';

            if ($ext && in_array($ext, self::ALLOW_EXTENSIONS)) {
                if ($fileName) {
                    $fileName = self::generateFileName($ext);
                } else {                    
                    $fileName = "{$fileName}.{$ext}";
                }
                $filePath = self::generateFilePath($fileName);

                if (file_exists(self::uploadPath() . $filePath) || preg_match('/[\x7f-\xff]/', $fileName)) {
                    $filePath = self::generateFilePath(self::generateFileName($ext));
                }

                $fullPath = self::uploadPath() . $filePath;
                
                $putRes = file_put_contents($fullPath, $data);

                if ($putRes) {
                    $fileSize = filesize($fullPath);
                    if ($fileSize > self::MAX_FILE_SIZE) {
                        @unlink($fullPath);
                        return Yii::t('app', 'The file is too big. Its size cannot exceed {number} B', [
                            'number' => self::MAX_FILE_SIZE,
                        ]);
                    } else {
                        return [
                            'msg' => Yii::t('app', 'Upload success.'),
                            'path' => $filePath,
                        ];
                    }
                } else {
                    return Yii::t('app', 'Save file fail.');
                }

            } else {
                return Yii::t('app', 'Only files with these extensions are allowed: ') . implode(', ', self::ALLOW_EXTENSIONS);
            }
        }

        return false;
    }

    public static function createFolder($folder, $mode = 0755, $recursive = true)
    {
        $path = self::fullPath() . ltrim($folder, '/');
        
        return FileHelper::createDirectory($path, $mode, $recursive);
    }

    public static function subFolders($path)
    {
        $path = self::fullPath() . $path . '/';
        $dirs = glob($path . '*', GLOB_ONLYDIR);

        return self::cleanFolders($dirs, $path);
    }

    public static function loopFoolders($path, $flag)
    {
        $folders = glob($path, $flag);

        $tempFolders = $folders;
        foreach ($tempFolders as $dir) {
            $folders = array_merge($folders, self::loopFoolders($dir . '/*', $flag));
        }

        return $folders;
    }

    public static function listFolders()
    {
        $path = self::fullPath() . '*';
        $folders = self::loopFoolders($path, GLOB_ONLYDIR);

        return self::cleanFolders($folders);
    }

    public static function cleanFolders($folders, $root = null)
    {
        $root = $root ? $root : self::fullPath();     
        $newFolders = [];
        foreach ($folders as $folder) {
            $newFolders[] = str_replace($root, '', $folder);
        }

        sort($newFolders);

        return $newFolders;
    }

    public static function deleteFolder($path)
    {
        $files = [];
        $paths = [$path];

        while (count($paths) != 0) {
            $nextPath = array_shift($paths);
            foreach (glob($nextPath) as $file) {
                if (is_dir($file)) {
                    $paths[] = $file . '/*';
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

        return true;
    }

    public static function compress($source, $dest)
    {
        $info = getimagesize($source);
        if (empty($info)) return;

        switch($info['mime']) {
            case 'image/jpg':
            case 'image/jpeg':
                $image = imagecreatefromjpeg($source);
                imagejpeg($image, $dest, 70);
                imagedestroy($image);
                break;
            case 'image/png':
                $image = imagecreatefrompng($source);
                // imagealphablending($image, true);
                // imagesavealpha($image, true);
                // imagepng($image, $dest, 9);
                imagejpeg($image, $dest, 70);
                imagedestroy($image);
            break;
        }
    }

    public static function resize($file, $width, $height)
    {
        $file = trim($file);

        if (stripos($file, 'http') === 0) {
            return $file;
        }

        $filePath = self::uploadPath() . $file;

        if (!is_file($filePath)) {
            return $filePath;
        }

        $pathInfo = pathinfo($filePath);
        $extension = $pathInfo['extension'];
        $filename = $pathInfo['filename'];

        $folder = 'resize';
        $folderPath = self::fullPath($folder);
        if (!is_dir($folderPath) && !self::createFolder($folder)) {
            return $file;
        }

        $newName = $filename . '-' . $width . 'x' . $height . '.' . $extension;

        $newFile = self::folderPath($folder) . $newName;
        $newFilePath = self::fullPath($folder) . $newName;

        if (!is_file($newFilePath) || (filemtime($filePath) > filemtime($newFilePath))) {

            list($oldWidth, $oldHeight) = getimagesize($filePath);

            if ($oldWidth != $width || $oldHeight != $height) {
                if (self::cropImage($width, $height, $filePath, $newFilePath) === false) {
                    $newFile = $file;
                }
            } else {
                if (!@copy($filePath, $newFilePath)) {
                    $newFile = $file;
                }
            }
        }

        return $newFile;
    }

    public static function cropImage($maxWidth, $maxHeight, $file, $newFile, $quality = 90)
    {
        $imgsize = getimagesize($file);
        $width = $imgsize[0];
        $height = $imgsize[1];
        $mime = $imgsize['mime'];

        switch ($mime) {
            case 'image/gif':
                $image_create = 'imagecreatefromgif';
                $image = 'imagegif';
                break;
     
            case 'image/png':
                $image_create = 'imagecreatefrompng';
                $image = 'imagepng';
                $quality = (int)($quality / 10);
                break;
        
            case 'image/jpg':
            case 'image/jpeg':
                $image_create = 'imagecreatefromjpeg';
                $image = 'imagejpeg';
                break;
     
            default:
                return false;
                break;
        }
         
        $dstImage = imagecreatetruecolor($maxWidth, $maxHeight);
        $srcImage = $image_create($file);
         
        $newWidth = $height * $maxWidth / $maxHeight;
        $newHeight = $width * $maxHeight / $maxWidth;

        if ($newWidth > $width) {
            // Crop from middle
            $heightPoint = (($height - $newHeight) / 2);
            imagecopyresampled($dstImage, $srcImage, 0, 0, 0, $heightPoint, $maxWidth, $maxHeight, $width, $newHeight);

            // Crop from top
            // imagecopyresampled($dstImage, $srcImage, 0, 0, 0, 0, $maxWidth, $maxHeight, $width, $newHeight);
        } else {
            $widthPoint = (($width - $newWidth) / 2);
            imagecopyresampled($dstImage, $srcImage, 0, 0, $widthPoint, 0, $maxWidth, $maxHeight, $newWidth, $height);
        }
         
        $image($dstImage, $newFile, $quality);
     
        if ($dstImage) imagedestroy($dstImage);
        if ($srcImage) imagedestroy($srcImage);

        return true;
    }
}
