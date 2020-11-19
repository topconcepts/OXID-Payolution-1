<?php
/**
 * Copyright 2018 Payolution GmbH
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0 [^]
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace TopConcepts\Payolution\Utils;

use OxidEsales\Eshop\Core\Registry;
use TopConcepts\Payolution\AccessPoint;
use TopConcepts\Payolution\Module\Core\Exception\PayolutionException;
use TopConcepts\Payolution\PayolutionModule;

/**
 * Class JavascriptLibraryUtils
 * @package TopConcepts\Payolution\Utils
 */
class JavascriptLibraryUtils
{
    /**
     * Payolution installment JavaScript library backup file name internal cache.
     * Used for library update process
     *
     * @var string
     */
    private $backupFilename;

    /**
     * Payolution installment JavaScript library download file name internal cache.
     * Used for library update process
     *
     * @var string
     */
    private $downloadFilename;

    /**
     * Download lock file pointer used to lock file for writing
     * @var file pointer
     */
    private $lockFilePointer = null;

    /**
     * Return true if payolution js library is expired and needs update
     *
     * @return bool
     */
    public function isExpired()
    {
        try {
            $lastUpdateTimestamp = $this->getLastUpdateTime();
        } catch (PayolutionException $e) {
            // library file not found. Update it
            return true;
        }

        $lastUpdateTime = new \DateTime(date('Y-m-d H:i:s', $lastUpdateTimestamp));
        $currentTime = new \DateTime();
        $expireTime = $lastUpdateTime->modify('+1 day');
        return $expireTime < $currentTime;
    }

    /**
     * Initiate update process without concurrent updaters
     *
     * @throws Payolution_Exception
     */
    public function updateWithLock()
    {
        if ($this->getLock()) {
            $this->update();
            $this->releaseLock();
        }
    }

    /**
     * Mark transaction as locked
     * Return true if lock succeeded
     * Return false if already locked
     */
    private function getLock()
    {
        if ($this->isFreeLock() || $this->isExpiredLock()) {
            return $this->writeLock();
        }

        return false;
    }

    /**
     * Return true if lock is unused
     *
     * @return bool
     */
    private function isFreeLock()
    {
        return !file_exists($this->getLockFileName());
    }

    /**
     * Return true if transaction has been locked by more than one hour
     * @return bool
     */
    private function isExpiredLock()
    {
        try {
            $filePointer = fopen($this->getLockFileName(), 'r');
            if (false === $filePointer) {
                return true;
            }
            if (!flock($filePointer, LOCK_SH | LOCK_NB)) {
                // lock file is still locked. Some process still uses it. Don't try to update library
                return false;
            }

            $lockFileTime = fread($filePointer, 100);

            fclose($filePointer);

            if (false === $lockFileTime || "" === $lockFileTime) {
                return true;
            }

            $lockTime = new \DateTime($lockFileTime);
            $currentTime = new \DateTime();
            $expireTime = $lockTime->modify('+1 hour');
            return $expireTime < $currentTime;
        } catch (\Exception $e) {
            return true;
        }
    }

    /**
     * Mark library download as started
     * Return true on success, false on failure
     *
     * @return bool
     */
    private function writeLock()
    {

        // *) if we are holding a previous lock then release it before next lock.
        if ($this->lockFilePointer) {
            $this->releaseLock();
        }

        $this->lockFilePointer = fopen($this->getLockFileName(), 'c');

        if (!$this->lockFilePointer) {
            return false;
        }

        if (!flock($this->lockFilePointer, LOCK_EX | LOCK_NB)) {
            // could not lock file, someone already is updating the library
            return false;
        }

        fwrite($this->lockFilePointer, date('Y-m-d H:i:s'));
        fflush($this->lockFilePointer);

        return true;
    }

    /**
     * Mark library download as ended
     */
    private function releaseLock()
    {
        if (null !== $this->lockFilePointer) {
            fclose($this->lockFilePointer);
            $this->lockFilePointer = null;
        }

        if (file_exists($this->getLockFileName())) {
            unlink($this->getLockFileName());
        }
    }

    /**
     * @return string
     */
    private function getLockFileName()
    {
        $path = Registry::getConfig()->getConfigParam('sCompileDir');

        return $path.DIRECTORY_SEPARATOR.'payolutionJsLibraryDownloader.lock';
    }

    /**
     * Initiate update process
     *
     * @throws PayolutionException
     */
    public function update()
    {
        try {
            $this->backupFile();
            $this->downloadFile();
            $this->useDownloadedFile();
        } catch (\ErrorException $e) {
            /** @var PayolutionException $err */
            $err = oxNew(PayolutionException::class, $e->getMessage());

            throw $err;
        }
    }

    /**
     * Get Payolution module url with added path
     *
     * @param string $sPath
     * @return string
     */
    public function getPayolutionModuleUrl($sPath = '')
    {
        return Registry::getConfig()->getConfigParam('sShopDir') . 'modules/tc/payolution/' . $sPath;
    }

    /**
     * Get path where Payolution installment JavaScript library files will be stored
     *
     * @return string
     */
    public function getPath()
    {
        return $this->getPayolutionModuleUrl('out/src/js/libs/payolution/');
    }

    /**
     * Get Payolution installment JavaScript library file name
     *
     * @return string
     */
    public function getFilename()
    {
        return 'payo-installment.js';
    }

    /**
     * Get Payolution installment JavaScript library backup file name
     *
     * @return string
     */
    private function getBackupFilename()
    {
        return isset($this->backupFilename)
            ? $this->backupFilename
            : $this->backupFilename = strtr('payo-installment-{date}-{time}.backup.js', $this->getBackupFilenameParams());
    }

    /**
     * Get Payolution installment JavaScript library backup file name template replacement params.
     * Used for strtr() function.
     *
     * @return array
     */
    private function getBackupFilenameParams()
    {
        return [
            '{date}' => date('Y-m-d'),
            '{time}' => date('H-i-s'),
        ];
    }

    /**
     * Get Payolution installment JavaScript library download URL.
     *
     * @return string
     */
    private function getFileDownloadUrl()
    {
        /** @var PayolutionModule $module */
        $module = oxNew(AccessPoint::class)->getModule();

        return $module->getConfig()->getInstallmentJsLibraryUrl();
    }

    /**
     * Get Payolution installment JavaScript library file name
     * while it is being downloaded from server
     *
     * @return string
     */
    private function getDownloadedFilename()
    {
        return isset($this->downloadFilename)
            ? $this->downloadFilename
            : $this->downloadFilename = strtr(
                'payo-installment-{date}-{time}.downloaded.js',
                $this->getDownloadedFilenameParams()
            );
    }

    /**
     * Get Payolution installment JavaScript library file name template params
     * while it is being downloaded from server.
     * Used for strtr() function
     *
     * @return array
     */
    private function getDownloadedFilenameParams()
    {
        return [
            '{date}' => date('Y-m-d'),
            '{time}' => date('H-i-s'),
        ];
    }

    /**
     * Check if Payolution installment JavaScript library file is already downloaded and exists
     *
     * @return bool
     */
    private function fileExists()
    {
        return file_exists($this->getPath().$this->getFilename());
    }

    /**
     * Get Payolution installment JavaScript library last modification (last update) time as UNIX timestamp
     * @return int
     */
    public function getLastUpdateTime()
    {
        if (!file_exists($this->getPath().$this->getFilename())) {
            throw oxNew(PayolutionException::class);
        }

        $time = filemtime($this->getPath().$this->getFilename());
        if (false === $time) {
            throw oxNew(PayolutionException::class);
        }

        return $time;
    }

    /**
     * Backup current version of Payolution installment JavaScript library file
     *
     */
    private function backupFile()
    {
        if ($this->fileExists()) {
            // Create backup
            if (!copy($this->getPath().$this->getFilename(), $this->getPath().$this->getBackupFilename())) {
                throw new \ErrorException(
                    sprintf(
                        Registry::getLang()->translateString('PAYOLUTION_ERROR_JSLIB_FILE_CREATE_FAILED'),
                        $this->getPath()
                    )
                );
            }
        }
    }

    /**
     * Check if Payolution installment JavaScript library file is being downloaded
     *
     * @return bool
     */
    private function downloadedFileExists()
    {
        return file_exists($this->getPath().$this->getDownloadedFilename());
    }

    /**
     * Get Payolution installment JavaScript library server response headers
     *
     * @return mixed
     */
    private function getHeaders()
    {
        $rRequest = curl_init($this->getFileDownloadUrl());
        curl_setopt($rRequest, CURLOPT_NOBODY, true);
        curl_setopt($rRequest, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($rRequest, CURLOPT_HEADER, false);
        curl_setopt($rRequest, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($rRequest, CURLOPT_CONNECTTIMEOUT, 10);
        if (curl_exec($rRequest) === false) {
            throw new \RuntimeException("Curl error: " . curl_error($rRequest));
        }
        $aHeaders = curl_getinfo($rRequest);
        curl_close($rRequest);

        return $aHeaders;
    }

    /**
     * Confirm that server response headers are valid.
     * This means:
     *  - content type            = application/javascript
     *  - http_code               = 200
     *  - download_content_length < 1024 * 1024
     *
     * @throws \Exception
     */
    private function confirmHeaders()
    {
        $aHeaders = $this->getHeaders();
        if ($aHeaders['content_type'] !== 'application/javascript'
            || $aHeaders['http_code'] !== 200
            || $aHeaders['download_content_length'] >= 1024 * 1024
        )
        {
            throw new \Exception('Wrong response headers: '.PHP_EOL.print_r($aHeaders, true));
        }
    }

    /**
     * Download Payolution installment JavaScript library file from server
     */
    private function downloadFile()
    {
        try {
            // Confirm headers
            $this->confirmHeaders();

            // Create file pointer
            if (!($rFile = fopen($this->getPath().$this->getDownloadedFilename(), 'w+'))) {
                throw new \ErrorException(
                    sprintf(
                        Registry::getLang()->translateString('PAYOLUTION_ERROR_JSLIB_FILE_CREATE_FAILED'),
                        $this->getPath()
                    )
                );
            }

            // Init CURL request and set parameters and link request with download file pointer
            $rRequest = curl_init($this->getFileDownloadUrl());
            curl_setopt($rRequest, CURLOPT_RETURNTRANSFER, false);
            curl_setopt($rRequest, CURLOPT_BINARYTRANSFER, true);
            curl_setopt($rRequest, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($rRequest, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($rRequest, CURLOPT_FILE, $rFile);

            // Execute request and download file
            curl_exec($rRequest);

            // Close connection and file pointer
            curl_close($rRequest);
            fclose($rFile);

            if (!$this->downloadedFileValid()) {
                throw new \Exception('Downloaded file does not exist or has size of 0 bytes');
            }
        } catch (\Exception $e) {
            // Save detailed error log
            /** @var PayolutionException $oEx */
            $oEx = oxNew(PayolutionException::class, $e->getMessage());
            $oEx->debugOut();

            // Remove unnecessary files
            $this->removeDownloadedFile();
            $this->removeBackupFile();

            // Rethrow user friendly error message
            throw new \ErrorException(Registry::getLang()->translateString("PAYOLUTION_ERROR_JSLIB_UPDATE_FAILED"));
        }
    }

    /**
     * Confirm that downloaded file is present and has file size higher than 0 bytes
     *
     * @return bool
     */
    private function downloadedFileValid()
    {
        return $this->downloadedFileExists() && filesize($this->getPath().$this->getDownloadedFilename()) > 0;
    }

    /**
     * Removes downloaded file when update process is complete or has failed at some point
     *
     * @return bool
     */
    private function removeDownloadedFile()
    {
        return @unlink($this->getPath().$this->getDownloadedFilename());
    }

    /**
     * Removes unnecessary backup file when update process has failed at some point
     *
     * @return bool
     */
    private function removeBackupFile()
    {
        return unlink($this->getPath().$this->getBackupFilename());
    }

    /**
     * Replace Payolution installment JavaScript library file with new downloaded file
     *
     * @throws \ErrorException
     */
    private function useDownloadedFile()
    {
        // Replace downloaded file with the used one
        $bCopied = copy($this->getPath().$this->getDownloadedFilename(), $this->getPath().$this->getFilename());
        if (!$bCopied) {
            throw new \ErrorException(Registry::getLang()->translateString("PAYOLUTION_ERROR_JSLIB_UPDATE_FAILED"));
        }

        // Remove downloaded file
        $this->removeDownloadedFile();
    }
}
