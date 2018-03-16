<?php
/**
 * Copyright 2015 Payolution GmbH
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
namespace Payolution\Client\Response;

/**
 * Class ErrorResponse
 * @package Payolution\Client\Response
 */
class ErrorResponse
{
    /**
     * Bank rejection status key
     */
    const STATUS_REJECTED_BANK = 'REJECTED_BANK';
    /**
     * Connection error key
     */
    const STATUS_CONNECTION_ERROR = 'CONNECTION_ERROR';

    /**
     * @var string
     */
    public $status;         //  <Status code="60">REJECTED_BANK</Status>
    /**
     * @var int
     */
    public $statusCode;

    /**
     * @var string
     */
    public $reason;         //   <Reason code="95">Authorization Error</Reason>
    /**
     * @var int
     */
    public $reasonCode;

    /**
     * @var string
     */
    public $message;        //  <Return code="800.100.170">transaction declined (transaction not permitted)</Return>
    /**
     * @var string
     */
    public $messageCode;

    /**
     * Method creates an instance of Payolution_Client_ResponseError with a
     * given XML, which is gotten from payolution response, xpath:
     * /Transaction/Processing
     *
     * @param SimpleXMLElement $xml
     *
     * @return Payolution_Client_ResponseError
     */
    public static function createFromXml(SimpleXMLElement $xml)
    {
        /* @var $error Payolution_Client_ResponseError */
        $error = oxNew('Payolution_Client_ResponseError');

        $error->status     = (string) $xml->Status;
        $error->statusCode = (string) $xml->Status['code'];

        $error->reason     = (string) $xml->Reason;
        $error->reasonCode = (string) $xml->Reason['code'];

        $error->message     = (string) $xml->Return;
        $error->messageCode = (string) $xml->Return['code'];

        return $error;
    }


    /**
     * @return string JSON encoded string
     */
    public function serialize()
    {
        $data    = array(
          $this->statusCode,
          $this->reasonCode,
          $this->messageCode,
          $this->status,
          $this->reason,
          $this->message
        );
        $data [] = self::checksum($data);

        return base64_encode(gzdeflate(json_encode($data)));
    }

    /**
     * Method deserializes data and returns ResponseError object.
     * Method returns null if was error in decoding.
     *
     * @param $serializedData
     *
     * @return Payolution_Client_ResponseError|null
     */
    public static function deserialize($serializedData)
    {
        $error = null;

        $base64decoded = @base64_decode($serializedData);

        $decompressed = $base64decoded ? @gzinflate($base64decoded) : null;

        $decoded = $decompressed ? @json_decode($decompressed) : null;

        if ($decoded && is_array($decoded) && count($decoded) == 7) {

            $crc  = $decoded[6];
            $data = array_splice($decoded, 0, 6);

            if (self::checksum($data) === $crc) {

                /* @var $error Payolution_Client_ResponseError */
                $error = oxNew('Payolution_Client_ResponseError');

                $error->statusCode  = $data[0];
                $error->reasonCode  = $data[1];
                $error->messageCode = $data[2];
                $error->status      = $data[3];
                $error->reason      = $data[4];
                $error->message     = $data[5];
            }
        }

        return $error;
    }

    /**
     * You should not trust this pseudo security checksum. It just protects
     * from simple hacks. Anyone could see in the code to manage how to
     * generate correct checksums so input validation is a must.
     *
     * @param array $data
     *
     * @return int
     */
    private static function checksum($data)
    {
        $str = '';
        foreach ($data as $item) {
            $str .= ';' . $item;
        }

        return substr(base64_encode(crc32($str)), 3, 4);
    }
}
