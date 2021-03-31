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
namespace TopConcepts\Payolution\Client\Request;

use TopConcepts\Payolution\Client\Type\AccountType;
use TopConcepts\Payolution\Client\Type\AnalysisType;
use TopConcepts\Payolution\Client\Type\CustomerType;
use TopConcepts\Payolution\Client\Type\PaymentType;
use TopConcepts\Payolution\Client\Type\TransactionType;
use TopConcepts\Payolution\Form\DirectDebitForm;
use TopConcepts\Payolution\Form\InstallmentDeForm;
use TopConcepts\Payolution\Form\InstallmentForm;
use TopConcepts\Payolution\Form\InstallmentGbForm;
use TopConcepts\Payolution\Payment\PaymentMethod;

/**
 * Class PreAuthRequest
 * @package TopConcepts\Payolution\Client\Request
 */
class PreAuthRequest extends AbstractRequest
{
    /**
     * @param string                                 $precheckResponseId
     * @param PaymentMethod              $paymentMethod
     * @param CustomerType        $customer
     * @param PaymentType         $payment
     * @param AnalysisType[] $basketItems
     */
    public function __construct(
      $precheckResponseId,
      PaymentMethod $paymentMethod,
      CustomerType $customer,
      PaymentType $payment,
      $basketItems
    )
    {
        $this->setPaymentMethod($paymentMethod);
        $transaction = $this->transaction();
        $transaction->customer = $customer;

        if ($customer->company->name) {
            $transaction->analysis->company->type = $customer->company->type;
            $transaction->analysis->company->uid = $customer->company->uid;
            $transaction->analysis->company->name = $customer->company->name;
            $transaction->analysis->company->ownerBirthdate = $customer->company->ownerBirthdate;
            $transaction->analysis->company->ownerGiven = $customer->company->ownerGiven;
            $transaction->analysis->company->ownerFamily = $customer->company->ownerFamily;
        }

        $transaction->payment       = $payment;
        $transaction->payment->code = PaymentType::PREAUTH;

        $transaction->analysis->items                       = $basketItems;
        $transaction->analysis->preCheckId                  = $precheckResponseId;
        $transaction->analysis->calculationId               = $payment->calculationId;
        $transaction->analysis->customer->registrationLevel = (int)$customer->isRegistered;
        $transaction->analysis->customer->registrationDate  = $customer->isRegistered ? date('Ymd', strtotime($customer->registerDate)) : null;

        $taxAmount = 0.00;
        foreach ($basketItems as $item) {
            $taxAmount += $item->tax;
        }

        $transaction->analysis->taxAmount = $taxAmount;
        $transaction->analysis->needToUseSessionId = true;

        $transaction->analysis->shipping = $customer->shippingAddress;
        $transaction->analysis->account->country = $transaction->analysis->shipping->country;

        $this->paymentIntoTransaction($transaction, $paymentMethod, $payment);
    }

    /**
     * @param PaymentMethod $paymentMethod
     */
    protected function setPaymentMethod(PaymentMethod $paymentMethod)
    {
        parent::setPaymentMethod($paymentMethod);

        $isInstallment = ($paymentMethod == PaymentMethod::Installment());
        $isB2b         = ($paymentMethod == PaymentMethod::InvoiceB2b());
        $isDirectDebit = ($paymentMethod == PaymentMethod::DD());

        if ($isInstallment) {
            $brand = AccountType::PAYOLUTION_INSTALLMENT;
        }elseif($isDirectDebit){
            $brand = AccountType::DIRECT_DEBIT;
        } else {
            $brand = AccountType::PAYOLUTION_INVOICE;
        }

        $this->transaction()->account->brand = $brand;

        if ($isB2b) {
            $this->transaction()->analysis->trxType = 'B2B';
        }
    }


    /**
     * @param TransactionType $transaction
     * @param PaymentMethod $paymentMethod
     * @param PaymentType $payment
     */
    private function paymentIntoTransaction(TransactionType &$transaction, PaymentMethod $paymentMethod, PaymentType $payment)
    {
        switch ($paymentMethod) {
            case PaymentMethod::Installment():
                /* @var $form InstallmentForm */
                $form = $payment->paymentOptionsForm;
                $transaction->analysis->installmentAmount = $payment->formattedAmount();
                $transaction->analysis->duration = $form->installmentPeriod()->value();

                if ($form instanceof InstallmentDeForm) {
                    /* @var $form InstallmentDeForm */
                    $transaction->analysis->account->holder  = $form->accountHolder()->value();
                    $transaction->analysis->account->iban    = $form->iban()->value();
                }

                if ($form instanceof InstallmentGbForm) {
                    /* @var $form InstallmentGbForm */
                    $transaction->analysis->account->holder  = $form->accountHolder()->value();
                    $transaction->analysis->account->iban    = $form->iban()->value();
                    $transaction->analysis->account->country = 'GB';
                }
                break;

            case PaymentMethod::InvoiceB2c():
                break;

            case PaymentMethod::DD():
                /* @var $form DirectDebitForm */
                $form = $payment->paymentOptionsForm;
                $transaction->analysis->account->holder  = $form->holder()->value();
                $transaction->analysis->account->iban    = $form->iban()->value();
                break;
        }
    }
}
