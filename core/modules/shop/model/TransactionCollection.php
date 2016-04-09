<?php

class TransactionCollection extends Ajde_Collection
{
    public function filterByUser($user)
    {
        if ($user instanceof Ajde_User) {
            $user = $user->getPK();
        }

        return $this->addFilter(new Ajde_Filter_Where('user', Ajde_Filter::FILTER_EQUALS, $user));
    }

    public function filterByPaymentStatus($status)
    {
        return $this->addFilter(new Ajde_Filter_Where('payment_status', Ajde_Filter::FILTER_EQUALS, $status));
    }
}
