<?php
namespace VVC\Controller;

class Uploader
{
    public static function getFiles(
        string $directory,
        array $extensions
    ) {
        $files = [];

        if ($handle = opendir(ltrim($directory, '/'))) {

            while (false !== ($file = readdir($handle))) {
                $fileInfo = pathinfo($file);
                if (in_array($fileInfo['extension'], $extensions)) {
                    $files[] = $directory . $file;
                }
            }

            closedir($handle);

        } else {
            Logger::log('upload', 'error', "Failed to open directory $directory");
        }

        return $files;
    }

    public static function readYml(
        $controller, string $file = null, string $redirect
    ) {
        if (empty($file)) {
            $controller->flash('fail', 'No file selected');
            Router::redirect($redirect);
        }

        try {
            $data = \Symfony\Component\Yaml\Yaml::parse(
                file_get_contents(ltrim(YML_DIRECTORY, '/') . $file)
            );
            return $data;

        } catch (\Exception $e) {
            Logger::log('upload', 'error', 'Failed to parse YML', $e);
            $controller->flash('fail', 'Could not parse data, check the file');
            Router::redirect($redirect);
        }
    }

    public static function uploadFile(string $location, string $name, $file)
    {
        return $file->move(ltrim($location, '/'), $name);
    }

    public static function uploadDrugPic(
        $controller, array $files, string $redirect
    ) {
        $tmp = $files['drug_pic'];

        $ext = $tmp->getClientOriginalExtension();
        $mime = $tmp->getMimeType();

        if (!(in_array($mime, ['image/png', 'image/jpg'])) ||
            !(in_array($ext, ['png', 'jpg'])))
        {
            $controller->flash('fail',
                ".png or .jpg file expected, .$ext file given"
            );
            Router::redirect($redirect);
        }

        $filename = $tmp->getClientOriginalName();
        $path = DRUG_DIRECTORY . $filename;

        if (file_exists($path)) {
            // DUP
            // Router::addCookie('file_to_overwrite', $upload);
            //
            // $controller->addTwigVar('filename', $filename);
            // $controller->showAccountListPage();
        }

        $pic = self::uploadFile(DRUG_DIRECTORY, $filename, $tmp);

        if (!$pic) {
            $controller->flash('fail', 'Upload failed');
            Router::redirect($redirect);
        }

        return $filename;
    }

    public static function isEmbedded(string $path)
    {
        if (strpos($path, 'http://') !== false ||
            strpos($path, 'https://') !== false)
        {
            return true;
        } else {
            return false;
        }
    }
}
