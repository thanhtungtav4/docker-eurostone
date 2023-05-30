<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 11/12/2018
 * Time: 17:34
 *
 * @since 1.8.0
 */

namespace WPCCrawler\Objects\File;


use Illuminate\Filesystem\Filesystem;
use WPCCrawler\Objects\Enums\FileTemplateShortCodeName;
use WPCCrawler\Objects\Enums\ShortCodeName;
use WPCCrawler\Objects\Informing\Informer;

class MediaFile {

    /** @var string Raw source URL after "find-replace in image URLs" options applied to it. */
    private $sourceUrl;

    /** @var string|null Raw source URL retrieved from the target site */
    private $originalSourceUrl;

    /** @var string|null */
    private $localUrl;

    /** @var string|null */
    private $localPath;

    /** @var bool True if this file is a gallery image. */
    private $isGalleryImage = false;

    /** @var string|null */
    private $mediaTitle;

    /** @var string|null */
    private $mediaDescription;

    /** @var string|null */
    private $mediaCaption;

    /** @var string|null */
    private $mediaAlt;

    /** @var string */
    private $originalFileName;

    /** @var int|null Media ID of this file, retrieved by inserting the media into the database. */
    private $mediaId;

    /** @var string[] Stores the paths of the copies of the file */
    private $copyFilePaths = [];

    /**
     * @param string      $sourceUrl See {@link $sourceUrl}
     * @param string|null $localPath See {@link $localPath}
     */
    public function __construct(string $sourceUrl, ?string $localPath) {
        $this->sourceUrl = $sourceUrl;
        $this->setLocalPath($localPath);
        $this->originalFileName = $this->getFileSystem()->name($this->localPath ?: $sourceUrl);
    }

    /**
     * @return string
     */
    public function getSourceUrl(): string {
        return $this->sourceUrl;
    }

    /**
     * @param string $sourceUrl
     * @return MediaFile
     */
    public function setSourceUrl(string $sourceUrl): self {
        $this->sourceUrl = $sourceUrl;
        return $this;
    }

    /**
     * @return string
     */
    public function getOriginalSourceUrl(): string {
        return $this->originalSourceUrl ?: $this->getSourceUrl();
    }

    /**
     * @param string|null $originalSourceUrl
     * @return MediaFile
     */
    public function setOriginalSourceUrl(?string $originalSourceUrl): self {
        $this->originalSourceUrl = $originalSourceUrl;
        return $this;
    }

    /**
     * @return string
     */
    public function getLocalUrl(): string {
        // If there is no local URL, create it.
        if ($this->localUrl === null) {
            $url = FileService::getInstance()->getUrlForPathUnderUploadsDir($this->getLocalPath());
            if ($url !== null) {
                $this->localUrl = $url;
            }
        }

        return $this->localUrl ?: '';
    }

    /**
     * @param string|null $localUrl
     * @return MediaFile
     */
    public function setLocalUrl(?string $localUrl): self {
        $this->localUrl = $localUrl;
        return $this;
    }

    /**
     * @return string
     */
    public function getOriginalFileName(): string {
        return $this->originalFileName;
    }

    /**
     * @return string|null
     */
    public function getLocalPath(): ?string {
        return $this->localPath;
    }

    /**
     * @param string|null $localPath
     * @return MediaFile
     */
    public function setLocalPath(?string $localPath): self {
        if ($localPath !== null) {
            $this->localPath = realpath($localPath) ?: null;
        } else {
            $this->localPath = null;
        }

        // If this is a test, store the file path as test file path so that it will be deleted later.
        MediaService::getInstance()->addTestFilePath($this->localPath);

        // Make the local URL null, since the path of the file has just changed.
        $this->localUrl = null;
        return $this;
    }

    /**
     * @return bool
     */
    public function isGalleryImage(): bool {
        return $this->isGalleryImage;
    }

    /**
     * @param bool $isGalleryImage
     * @return MediaFile
     */
    public function setIsGalleryImage(bool $isGalleryImage): self {
        $this->isGalleryImage = $isGalleryImage;
        return $this;
    }

    /**
     * @return string
     */
    public function getMediaTitle(): string {
        if ($this->mediaTitle !== null) {
            return $this->mediaTitle;
        }

        $baseName = $this->getBaseName();
        if ($baseName === null) {
            return '';
        }

        $result = preg_replace('/\.[^.]+$/', '', $baseName);
        return is_string($result) ? $result : '';
    }

    /**
     * @param null|string $mediaTitle
     * @return MediaFile
     */
    public function setMediaTitle(?string $mediaTitle): self {
        $this->mediaTitle = $mediaTitle;
        return $this;
    }

    /**
     * @return string
     */
    public function getMediaDescription(): string {
        return $this->mediaDescription !== null ? $this->mediaDescription : '';
    }

    /**
     * @param null|string $mediaDescription
     * @return MediaFile
     */
    public function setMediaDescription(?string $mediaDescription): self {
        $this->mediaDescription = $mediaDescription;
        return $this;
    }

    /**
     * @return string
     */
    public function getMediaCaption(): string {
        return $this->mediaCaption !== null ? $this->mediaCaption : '';
    }

    /**
     * @param null|string $mediaCaption
     * @return MediaFile
     */
    public function setMediaCaption(?string $mediaCaption): self {
        $this->mediaCaption = $mediaCaption;
        return $this;
    }

    /**
     * @return string
     */
    public function getMediaAlt(): string {
        return $this->mediaAlt !== null ? $this->mediaAlt : '';
    }

    /**
     * @param null|string $mediaAlt
     * @return MediaFile
     */
    public function setMediaAlt(?string $mediaAlt): self {
        $this->mediaAlt = $mediaAlt;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getMediaId(): ?int {
        return $this->mediaId;
    }

    /**
     * @param int|null $mediaId
     */
    public function setMediaId(?int $mediaId): void {
        $this->mediaId = $mediaId;
    }

    /*
     * OPERATIONS
     */

    /**
     * @return bool True if the media file exists.
     * @since 1.8.0
     */
    public function exists(): bool {
        $path = $this->getLocalPath();
        return $path === null
            ? false
            : $this->getFileSystem()->exists($path);
    }

    /**
     * @return bool True if the media file path is a file.
     * @since 1.8.0
     */
    public function isFile(): bool {
        $path = $this->getLocalPath();
        return $path === null
            ? false
            : strlen($this->getFileSystem()->extension($path)) > 0;
    }

    /**
     * @return string|null Directory of the media file
     * @since 1.8.0
     */
    public function getDirectory(): ?string {
        $path = $this->getLocalPath();
        return $path === null
            ? null
            : $this->getFileSystem()->dirname($path);
    }

    /**
     * @return string|null Name of the media file
     * @since 1.8.0
     */
    public function getName(): ?string {
        $path = $this->getLocalPath();
        return $path === null
            ? null
            : $this->getFileSystem()->name($path);
    }

    /**
     * @return string|null Base name of the file, i.e. file name with extension.
     * @since 1.8.0
     */
    public function getBaseName(): ?string {
        $path = $this->getLocalPath();
        return $path === null
            ? null
            : $this->getFileSystem()->basename($path);
    }

    /**
     * @return string|null Extension of the media file
     * @since 1.8.0
     */
    public function getExtension(): ?string {
        $path = $this->getLocalPath();
        return $path === null
            ? null
            : $this->getFileSystem()->extension($path);
    }

    /**
     * @return string Mime type or empty string if mime type does not exist.
     * @since 1.8.0
     */
    public function getMimeType(): string {
        $path = $this->getLocalPath();
        if ($path === null) return '';

        $mimeType = $this->getFileSystem()->mimeType($path);
        return $mimeType ?: '';
    }

    /**
     * @return string|null MD5 hash of the file, if the file exists.
     * @since 1.8.0
     */
    public function getMD5Hash(): ?string {
        $path = $this->getLocalPath();
        return $path === null
            ? null
            : $this->getFileSystem()->hash($path);
    }

    /**
     * @return string|null A unique SHA1 string created by using {@link uniqid()} and the base name of the file, if
     *                     there is a file.
     * @since 1.8.0
     */
    public function getRandomUniqueHash(): ?string {
        $baseName = $this->getBaseName();
        return $baseName === null
            ? null
            : sha1($baseName . uniqid('wpcc'));
    }

    /**
     * @return int File size in kilobytes.
     * @since 1.8.0
     */
    public function getFileSizeByte(): int {
        return $this->getFileSize();
    }

    /**
     * @return int File size in kilobytes.
     * @since 1.8.0
     */
    public function getFileSizeKb(): int {
        return (int) ($this->getFileSize() / 1000);
    }

    /**
     * @return int File size in megabytes.
     * @since 1.8.0
     */
    public function getFileSizeMb(): int {
        return (int) ($this->getFileSize() / 1000000);
    }

    /**
     * Rename the file.
     *
     * @param string|null $newName New name of the file. Without extension.
     * @return bool True if the renaming was successful.
     * @since 1.8.0
     */
    public function rename(?string $newName): bool {
        $newName = FileService::getInstance()->validateFileName($newName);

        // If there is no name after validation, assign a default name.
        if (!$newName) $newName = 'no-name';

        // If the new name is the same as the old name, stop by indicating success.
        if ($newName === $this->getName()) return true;

        $directory = $this->getDirectory();
        if (!$directory) return false;

        // Rename the file
        $newPath = $this->getUniqueFilePath($newName, $directory);
        if ($newPath === null) return false;

        $success = $this->move($newPath);
        if (!$success) return false;

        // If the file name does not exist, we cannot rename the copies. The copies probably do not exist in that case,
        // too. At this point in execution, we already moved the file. So, the file name probably exists. However, to
        // be on the safe side, we check its existence. If the file name does not exist, return true, because we already
        // moved the file. Also note that, we get the file name here again, because the names has been changed above.
        // We need the changed name. So, we retrieve the name by calling getName() method again.
        $fileName = $this->getName();
        if ($fileName === null) return true;

        // If there are copies, rename them as well.
        $copyFilePaths = $this->copyFilePaths;

        // First, clear the copy file paths since we are gonna rename them.
        $this->clearCopyFilePaths();

        // Rename the copy files
        foreach($copyFilePaths as $copyFilePath) {
            // Get the directory of the copy file
            $directoryPath = $this->getFileSystem()->dirname($copyFilePath);

            // Get a unique name for the copy file in the same directory
            $newCopyFilePath = $this->getUniqueFilePath($fileName, $directoryPath);

            // Try to rename the file
            if ($newCopyFilePath && @$this->getFileSystem()->move($copyFilePath, $newCopyFilePath)) {
                // If renamed, store it.
                $this->addCopyFilePath($newCopyFilePath);

                // If testing, remove the old copy file path from test file paths.
                MediaService::getInstance()->removeTestFilePath($copyFilePath);

            } else {
                // Otherwise, inform the user.
                Informer::addError(sprintf(_wpcc('File %1$s could not be moved to %2$s'), $copyFilePath, $newCopyFilePath ?: '"null"'))
                    ->addAsLog();
            }
        }

        return true;
    }

    /**
     * @param string $newPath New path of the file
     * @return bool True if the operation has been successful.
     * @since 1.8.0
     */
    public function move($newPath): bool {
        $newPath = FileService::getInstance()->forceDirectorySeparator($newPath);
        if (!$newPath) return false;

        $localPath = $this->getLocalPath();
        if (!$localPath) return false;

        $result = @$this->getFileSystem()->move($localPath, $newPath);

        // If the file was moved, set the new path.
        if ($result) {
            // If this is a test, remove the previous local path.
            MediaService::getInstance()->removeTestFilePath($localPath);

            $this->setLocalPath($newPath);

        } else {
            // Otherwise, inform the user.
            Informer::addError(sprintf(_wpcc('File %1$s could not be moved to %2$s'), $localPath, $newPath))
                ->addAsLog();
        }

        return $result;
    }

    /**
     * @param string $newDirectoryPath New directory path
     * @return bool True if the file has been successfully moved. Otherwise, false.
     * @since 1.8.0
     */
    public function moveToDirectory($newDirectoryPath): bool {
        $newDirectoryPath = FileService::getInstance()->forceDirectorySeparator($newDirectoryPath);
        $baseName = $this->getBaseName();
        if (!$newDirectoryPath || $baseName === null) return false;

        // Make sure the directories exist. If not, create them. Stop if they do not exist.
        if (!$this->makeDirectory($newDirectoryPath)) return false;

        // We now have the target directory created. Let's move the file to that directory.
        return $this->move($newDirectoryPath . DIRECTORY_SEPARATOR . $baseName);
    }

    /**
     * @param string $directoryPath Target directory path
     * @return false|string False if the operation was not successful. Otherwise, copied file's path.
     * @since 1.8.0
     */
    public function copyToDirectory($directoryPath) {
        $directoryPath = FileService::getInstance()->forceDirectorySeparator($directoryPath);
        if (!$directoryPath) return false;

        // Make sure the directories exist. If not, create them. Stop if they do not exist.
        if (!$this->makeDirectory($directoryPath)) return false;

        $fileName = $this->getName();
        if ($fileName === null) return false;

        // Get the new name
        $copyPath = $this->getUniqueFilePath($fileName, $directoryPath);
        if ($copyPath === null) return false;

        // Copy the file
        $localPath = $this->getLocalPath();
        if ($localPath === null) return false;

        $success = $this->getFileSystem()->copy($localPath, $copyPath);

        // If the file is copied, store the copy file's path.
        if ($success) $this->addCopyFilePath($copyPath);

        return $success === false ? false : $copyPath;
    }

    /**
     * Delete the local file.
     *
     * @return bool True if the file has been successfully deleted. Otherwise, false.
     * @since 1.8.0
     */
    public function delete(): bool {
        $localPath = $this->getLocalPath();
        if (!$localPath) return true;

        $result = $this->getFileSystem()->delete($localPath);
        if (!$result) {
            // Inform the user if the file could not be deleted
            Informer::addError(sprintf(_wpcc('File %1$s could not be deleted.'), $localPath))
                ->addAsLog();
        }

        return $result;
    }

    /**
     * Delete copies of the local file.
     *
     * @return bool True if all of the copy file have been deleted. Otherwise, false.
     * @since 1.8.0
     */
    public function deleteCopyFiles(): bool {
        if (!$this->copyFilePaths) return true;
        $success = true;

        // Delete a copy file
        foreach($this->copyFilePaths as $copyPath) {
            if (!$this->getFileSystem()->delete($copyPath)) {
                $success = false;

                // Inform the user if the file could not be deleted
                Informer::addError(sprintf(_wpcc('File %1$s could not be deleted.'), $copyPath))
                    ->addAsLog();
            } else {
                // If file is deleted and this is a test, remove the path from test file paths.
                MediaService::getInstance()->removeTestFilePath($copyPath);
            }
        }

        return $success;
    }

    /**
     * Get URLs of the files that are copies of the file.
     *
     * @return string[] An array of copy file URLs
     * @since 1.8.0
     */
    public function getCopyFileUrls(): array {
        if (!$this->copyFilePaths) return [];

        $urls = [];

        foreach($this->copyFilePaths as $filePath) {
            $url = FileService::getInstance()->getUrlForPathUnderUploadsDir($filePath);
            if (!$url) continue;

            $urls[] = $url;
        }

        return $urls;
    }

    /*
     *
     */

    /**
     * @return Filesystem
     * @since 1.8.0
     */
    public function getFileSystem(): Filesystem {
        return FileService::getInstance()->getFileSystem();
    }

    /**
     * @return array A map storing short code names of media files templates and corresponding values
     * @since 1.8.0
     */
    public function getShortCodeMap(): array {
        return [
            ShortCodeName::WCC_ITEM                         => function() { return $this->getSourceUrl(); },
            FileTemplateShortCodeName::ORIGINAL_FILE_NAME   => function() { return $this->getOriginalFileName(); },
            FileTemplateShortCodeName::ORIGINAL_TITLE       => function() { return $this->getMediaTitle(); },
            FileTemplateShortCodeName::ORIGINAL_ALT         => function() { return $this->getMediaAlt(); },
            FileTemplateShortCodeName::PREPARED_FILE_NAME   => function() { return $this->getName()             ?: ''; },
            FileTemplateShortCodeName::FILE_EXT             => function() { return $this->getExtension()        ?: ''; },
            FileTemplateShortCodeName::MIME_TYPE            => function() { return $this->getMimeType(); },
            FileTemplateShortCodeName::FILE_SIZE_BYTE       => function() { return $this->getFileSizeByte(); },
            FileTemplateShortCodeName::FILE_SIZE_KB         => function() { return $this->getFileSizeKb(); },
            FileTemplateShortCodeName::FILE_SIZE_MB         => function() { return $this->getFileSizeMb(); },
            FileTemplateShortCodeName::BASE_NAME            => function() { return $this->getBaseName()         ?: ''; },
            FileTemplateShortCodeName::MD5_HASH             => function() { return $this->getMD5Hash()          ?: ''; },
            FileTemplateShortCodeName::RANDOM_HASH          => function() { return $this->getRandomUniqueHash() ?: ''; },
            FileTemplateShortCodeName::LOCAL_URL            => function() { return $this->getLocalUrl(); },
        ];
    }

    /*
     *
     */

    /**
     * Clear copy file paths.
     *
     * @since 1.8.0
     */
    private function clearCopyFilePaths(): void {
        // Clear the copy file paths
        $this->copyFilePaths = [];
    }

    /**
     * Add a copy file path.
     *
     * @param string $copyFilePath
     * @since 1.8.0
     */
    private function addCopyFilePath(string $copyFilePath): void {
        $this->copyFilePaths[] = $copyFilePath;

        // If this is a test, store the file path as a test file path.
        MediaService::getInstance()->addTestFilePath($copyFilePath);
    }

    /**
     * @return int Size of the file in bytes
     * @since 1.8.0
     */
    private function getFileSize(): int {
        $localPath = $this->getLocalPath();
        $size = $localPath
            ? $this->getFileSystem()->size($localPath)
            : 0;
        return $size ?: 0;
    }

    /**
     * Get unique file path under a directory.
     *
     * @param string $fileName  File name without extension
     * @param string $directory Directory path. The file name will be unique to this directory.
     * @return string|null Absolute file path that is unique to the given directory, if there is a file. Otherwise, null.
     * @since 1.8.0
     */
    private function getUniqueFilePath(string $fileName, string $directory): ?string {
        $ext = $this->getExtension();
        if ($ext === null) {
            return null;
        }

        return FileService::getInstance()->getUniqueFilePath(
            $fileName . '.' . $ext,
            $directory,
            $this->getLocalPath()
        );
    }

    /**
     * @param string $directoryPath Directory or file path.
     * @return bool True if the directories of the file or the given path exist.
     * @since 1.8.0
     */
    private function makeDirectory(string $directoryPath): bool {
        // Get the directory path of the given path.
        $directoryPath = rtrim(FileService::getInstance()->forceDirectorySeparator($directoryPath), DIRECTORY_SEPARATOR);
        $directoryPath = strlen($this->getFileSystem()->extension($directoryPath)) ? $this->getFileSystem()->dirname($directoryPath) : $directoryPath;

        // If the directories do not exist, create them.
        if (!$this->getFileSystem()->isDirectory($directoryPath)) {
            $result = $this->getFileSystem()->makeDirectory($directoryPath, 0755, true);

            // Stop if the directories could not be created.
            if (!$result) {
                Informer::addError(
                    sprintf(_wpcc('%1$s directory could not be created'), $directoryPath)
                )->addAsLog();

                return false;
            }
        }

        return true;
    }

}