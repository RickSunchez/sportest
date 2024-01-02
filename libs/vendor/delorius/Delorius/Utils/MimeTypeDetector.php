<?php

namespace Delorius\Utils;

/**
 * Mime type detector.
 */
final class MimeTypeDetector
{

    /**
     * Static class - cannot be instantiated.
     */
    private function __construct()
    {
    }


    protected static $mime_types = array(
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpe' => 'image/jpeg',
        'gif' => 'image/gif',
        'png' => 'image/png',
        'bmp' => 'image/bmp',
        'flv' => 'video/x-flv',
        'js' => 'application/x-javascript',
        'json' => 'application/json',
        'tiff' => 'image/tiff',
        'css' => 'text/css',
        'xml' => 'application/xml',
        'doc' => 'application/msword',
        'docx' => 'application/msword',
        'xls' => 'application/vnd.ms-excel',
        'xlt' => 'application/vnd.ms-excel',
        'xlm' => 'application/vnd.ms-excel',
        'xld' => 'application/vnd.ms-excel',
        'xla' => 'application/vnd.ms-excel',
        'xlc' => 'application/vnd.ms-excel',
        'xlw' => 'application/vnd.ms-excel',
        'xll' => 'application/vnd.ms-excel',
        'ppt' => 'application/vnd.ms-powerpoint',
        'pps' => 'application/vnd.ms-powerpoint',
        'rtf' => 'application/rtf',
        'pdf' => 'application/pdf',
        'html' => 'text/html',
        'htm' => 'text/html',
        'php' => 'text/html',
        'txt' => 'text/plain',
        'mpeg' => 'video/mpeg',
        'mpg' => 'video/mpeg',
        'mpe' => 'video/mpeg',
        'mp3' => 'audio/mpeg3',
        'wav' => 'audio/wav',
        'aiff' => 'audio/aiff',
        'aif' => 'audio/aiff',
        'avi' => 'video/msvideo',
        'wmv' => 'video/x-ms-wmv',
        'mov' => 'video/quicktime',
        'zip' => 'application/zip',
        'tar' => 'application/x-tar',
        'swf' => 'application/x-shockwave-flash',
        'odt' => 'application/vnd.oasis.opendocument.text',
        'ott' => 'application/vnd.oasis.opendocument.text-template',
        'oth' => 'application/vnd.oasis.opendocument.text-web',
        'odm' => 'application/vnd.oasis.opendocument.text-master',
        'odg' => 'application/vnd.oasis.opendocument.graphics',
        'otg' => 'application/vnd.oasis.opendocument.graphics-template',
        'odp' => 'application/vnd.oasis.opendocument.presentation',
        'otp' => 'application/vnd.oasis.opendocument.presentation-template',
        'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        'ots' => 'application/vnd.oasis.opendocument.spreadsheet-template',
        'odc' => 'application/vnd.oasis.opendocument.chart',
        'odf' => 'application/vnd.oasis.opendocument.formula',
        'odb' => 'application/vnd.oasis.opendocument.database',
        'odi' => 'application/vnd.oasis.opendocument.image',
        'oxt' => 'application/vnd.openofficeorg.extension',
        'docm' => 'application/vnd.ms-word.document.macroEnabled.12',
        'dotx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
        'dotm' => 'application/vnd.ms-word.template.macroEnabled.12',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'xlsm' => 'application/vnd.ms-excel.sheet.macroEnabled.12',
        'xltx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
        'xltm' => 'application/vnd.ms-excel.template.macroEnabled.12',
        'xlsb' => 'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
        'xlam' => 'application/vnd.ms-excel.addin.macroEnabled.12',
        'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'pptm' => 'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
        'ppsx' => 'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
        'ppsm' => 'application/vnd.ms-powerpoint.slideshow.macroEnabled.12',
        'potx' => 'application/vnd.openxmlformats-officedocument.presentationml.template',
        'potm' => 'application/vnd.ms-powerpoint.template.macroEnabled.12',
        'ppam' => 'application/vnd.ms-powerpoint.addin.macroEnabled.12',
        'sldx' => 'application/vnd.openxmlformats-officedocument.presentationml.slide',
        'sldm' => 'application/vnd.ms-powerpoint.slide.macroEnabled.12',
        'thmx' => 'application/vnd.ms-officetheme',
        'onetoc' => 'application/onenote',
        'onetoc2' => 'application/onenote',
        'onetmp' => 'application/onenote',
        'onepkg' => 'application/onenote',
    );

    /**
     * @param $ext
     * @return mixed
     */
    public static function getContentType($ext)
    {
        $content = self::$mime_types[$ext];
        if (!$content) {
            return 'application/octet-stream';
        }
        return $content;
    }


    /**
     * Returns the MIME content type of file.
     * @param  string
     * @return string
     */
    public static function fromFile($file)
    {
        if (!is_file($file)) {
            die("File '$file' not found.");
        }

        $info = @getimagesize($file); // @ - files smaller than 12 bytes causes read error
        if (isset($info['mime'])) {
            return $info['mime'];

        } elseif (extension_loaded('fileinfo')) {
            $type = preg_replace('#[\s;].*$#', '', finfo_file(finfo_open(FILEINFO_MIME), $file));

        } elseif (function_exists('mime_content_type')) {
            $type = mime_content_type($file);
        }

        return isset($type) && preg_match('#^\S+/\S+$#', $type) ? $type : 'application/octet-stream';
    }


    /**
     * Returns the MIME content type of file.
     * @param  string
     * @return string
     */
    public static function fromString($data)
    {
        if (extension_loaded('fileinfo') && preg_match('#^(\S+/[^\s;]+)#', finfo_buffer(finfo_open(FILEINFO_MIME), $data), $m)) {
            return $m[1];

        } elseif (strncmp($data, "\xff\xd8", 2) === 0) {
            return 'image/jpeg';

        } elseif (strncmp($data, "\x89PNG", 4) === 0) {
            return 'image/png';

        } elseif (strncmp($data, "GIF", 3) === 0) {
            return 'image/gif';

        } else {
            return 'application/octet-stream';
        }
    }

}
