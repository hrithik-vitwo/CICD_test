<?php
require_once(__DIR__ . '/../../vendor/autoload.php');

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

class s3Config
{
    private $s3;
    private $fileExpiry = "+1 hour";
    private $bucket = 'vitwo-ai-analytics';
    private $region = 'ap-southeast-1';
    private $accessKey = 'AKIAUJ5K6KLP2RAYAUHW';
    private $secretKey = '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';
    private $subFolder = 'upload';

    public function __construct()
    {
        $this->s3 = new S3Client([
            'region' => $this->region,
            'version' => 'latest',
            'credentials' => [
                'key' => $this->accessKey,
                'secret' => $this->secretKey,
            ],
            'suppress_php_deprecation_warning' => true,
        ]);
    }

    // Upload file to S3
    public function uploadFileInS3($file, $folder)
    {
        $fileName = basename($file['name']);
        $key = $this->subFolder . $folder . $fileName;

        try {
            $result = $this->s3->putObject([
                'Bucket' => $this->bucket,
                'Key' => $key,
                'SourceFile' => $file['tmp_name'],
            ]);

            return [
                'status' => true,
                'key' => $key,
                'url' => $result['ObjectURL'],
                'message' => 'File uploaded successfully.',
            ];
        } catch (S3Exception $e) {
            return [
                'status' => false,
                'message' => 'Upload failed: ' . $e->getMessage(),
            ];
        }
    }

    // Generate a signed URL
    public function getUploadedFile($url)
    {
        // Get the file extension
        $ext = strtolower(pathinfo($url, PATHINFO_EXTENSION));

        $contentTypes = [
            // Images
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png'  => 'image/png',
            'gif'  => 'image/gif',
            'bmp'  => 'image/bmp',
            'webp' => 'image/webp',
            'svg'  => 'image/svg+xml',
            'tiff' => 'image/tiff',
            'ico'  => 'image/x-icon',

            // Documents
            'pdf'  => 'application/pdf',
            'doc'  => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls'  => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ppt'  => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'txt'  => 'text/plain',
            'rtf'  => 'application/rtf',
            'csv'  => 'text/csv',
            'html' => 'text/html',
            'htm'  => 'text/html',
            'xml'  => 'application/xml',

            // Audio
            'mp3'  => 'audio/mpeg',
            'wav'  => 'audio/wav',
            'ogg'  => 'audio/ogg',
            'm4a'  => 'audio/mp4',
            'flac' => 'audio/flac',

            // Video
            'mp4'  => 'video/mp4',
            'avi'  => 'video/x-msvideo',
            'mov'  => 'video/quicktime',
            'wmv'  => 'video/x-ms-wmv',
            'mkv'  => 'video/x-matroska',
            'webm' => 'video/webm',

            // Archives
            'zip'  => 'application/zip',
            'rar'  => 'application/vnd.rar',
            'tar'  => 'application/x-tar',
            'gz'   => 'application/gzip',
            '7z'   => 'application/x-7z-compressed',

            // Code & Misc
            'js'   => 'application/javascript',
            'json' => 'application/json',
            'css'  => 'text/css',
            'php'  => 'application/x-httpd-php',
            'py'   => 'text/x-python',
            'java' => 'text/x-java-source',
            'c'    => 'text/x-c',
            'cpp'  => 'text/x-c++',
            'sql'  => 'application/sql',
        ];


        $contentType = $contentTypes[$ext] ?? null;
//--------------If contentType is null then handel this --------------------------
        if (!$contentType) {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $localTmp = tempnam(sys_get_temp_dir(), 's3preview');

            try {
                $this->s3->getObject([
                    'Bucket' => $this->bucket,
                    'Key' => $url,
                    'SaveAs' => $localTmp,
                ]);
                $contentType = $finfo->file($localTmp);
            } catch (Exception $e) {
                $contentType = 'application/octet-stream';
            } finally {
                if (file_exists($localTmp)) {
                    unlink($localTmp);
                }
            }
        }

        try {
            $cmd = $this->s3->getCommand('GetObject', [
                'Bucket' => $this->bucket,
                'Key' => $url,
                'ResponseContentDisposition' => 'inline',
                'ResponseContentType' => $contentType,
            ]);

            $request = $this->s3->createPresignedRequest($cmd, $this->fileExpiry);
            return (string) $request->getUri();
        } catch (S3Exception $e) {
            return false;
        }
    }
}
