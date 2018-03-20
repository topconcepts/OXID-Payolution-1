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
namespace TopConcepts\Payolution\Client\Type;

use TopConcepts\Payolution\Client\SerializableInterface;

/**
 * Class IdentificationType
 * @package TopConcepts\Payolution\Client\Type
 */
class IdentificationType implements SerializableInterface
{
    /**
     * (conditional) Needed if a transaction references a different transaction
     * (e.g. a capture transaction has to reference the respective pre
     * authorization transaction).
     *
     * @var string
     */
    public $referenceId;

    /**
     * (optional) A merchant assigned ID, which will also be included in the
     * response to make it easier for the merchant to process transactions.
     * This ID will be used to match payments of customers.
     *
     * @var string
     */
    public $transactionId;

    /**
     * (optional, PreAuth only) Shopper customer number on merchant side. Also
     * included in the response.
     *
     * @var string
     */
    public $shopperId;

    /**
     * (optional) Invoice Id of current transaction.
     *
     * @var string
     */
    public $invoiceId;

    /**
     * @param \SimpleXMLElement $output
     */
    public function toXml(\SimpleXMLElement &$output)
    {
        if ($this->isNotEmpty()) {
            $element =& $output->Identification;

            if ($this->referenceId) {
                $element->ReferenceID = $this->referenceId;
            }

            if ($this->transactionId) {
                $element->TransactionID = $this->transactionId;
            }

            if ($this->shopperId) {
                $element->ShopperID = $this->shopperId;
            }

            if ($this->invoiceId) {
                $element->InvoiceID = $this->invoiceId;
            }
        }
    }

    /**
     * @return bool
     */
    public function isNotEmpty()
    {
        return $this->referenceId || $this->transactionId || $this->shopperId || $this->invoiceId;
    }
}
