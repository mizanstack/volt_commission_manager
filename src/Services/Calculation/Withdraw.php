<?php

namespace Volt\Services\Calculation;

use Volt\Services\Commission;

class Withdraw
{
	// type = withdraw
    // withdraw = private/business
		// private 0.3%
			// not applicable if
			// each week mon - sun &&
			// 3 withdraw per week &&
			// 1000 eur max PER week &&
			// 1000 eur over then charged exceeded amount
		// business 0.5%

    protected $commission;
    protected $commission_private = 0.3;
    protected $commission_business = 0.5;
    protected $each_week_private_trans_count_free = 3;
    protected $each_week_private_trans_amount_free = 1000;

    public function __construct(Commission $commission)
    {
        $this->commission = $commission;
    }

    public function getCommission($trans): float
    {
    	if($trans->getUserType() == 'private'){
    		return $this->processPrivateCommission($trans);
    	}

    	if($trans->getUserType() == 'business'){
    		return $this->processBusinessCommission($trans);
    	}
        
    }

    public function processPrivateCommission($trans): float
    {

        $current_transaction_id = $trans->getId();
        $current_transaction_count = 0;
        $current_transaction_week_amount_in_euro = 0;
        $comission_applicable_amount = 0;
        $transaction_history_per_week = $this->getWeekTransactionListByUser($trans);

        foreach ($transaction_history_per_week as $week_transaction) {

            $current_transaction_week_amount_in_euro += $week_transaction->getAmountInEuro();
            $current_transaction_count++;

            if($current_transaction_id == $week_transaction->getId()){
                break;
            }
        }

        if( ($current_transaction_count <= $this->each_week_private_trans_count_free) && ($current_transaction_week_amount_in_euro > $this->each_week_private_trans_amount_free) ){

            $old_transaction_count = $current_transaction_week_amount_in_euro - $trans->getAmountInEuro();

            if($old_transaction_count >= $this->each_week_private_trans_amount_free){
                $comission_applicable_amount = $trans->getAmountInEuro();
            } else {
                $comission_applicable_amount = $current_transaction_week_amount_in_euro - $this->each_week_private_trans_amount_free;
            }
            
        }


        if($current_transaction_count > $this->each_week_private_trans_count_free){
            $comission_applicable_amount = $trans->getAmountInEuro();
        }

        // pr('user_id ' . $trans->getUserId() . ' amount ' . $trans->getAmountInEuro() . ' count '. $current_transaction_count . ' week transaction ' . $current_transaction_week_amount_in_euro . ' applicable amount ' . $comission_applicable_amount);

    	$discount =  $comission_applicable_amount * $this->commission_private / 100;
        return $trans->getEuroAmountInUserCurrency($discount);
    }


    public function processBusinessCommission($trans): float
    {
    	return $trans->getAmount() * $this->commission_business / 100;
    }


    public function getWeekTransactionListByUser($user): array
    {
        $week_transaction_list = [];
        foreach ($this->commission->getAllTransaction() as $transaction) {
            if(
             ($this->getWeekNumber($user) == $this->getWeekNumber($transaction)) &&
             ($this->diffInDaysBetweenTwoTransaction($user, $transaction) <= 7) &&
             ($user->getUserId() == $transaction->getUserId()) &&
             ($user->getOperationType() == $transaction->getOperationType()) &&
             ($user->getUserType() == $transaction->getUserType())
            ){
                $week_transaction_list[] = $transaction;
            }
        }

        return $week_transaction_list;
    }


    public function getWeekNumber($trans): int
    {
        $date = new \DateTime($trans->getDate());
        return (int) $date->format('W');
    }

    public function diffInDaysBetweenTwoTransaction($trans1, $trans2): int
    {
        $transactionOneInstance = new \DateTime($trans1->getDate());
        $transactionTwoInstance = new \DateTime($trans2->getDate());
        $diff_in_days = $transactionOneInstance->diff($transactionTwoInstance)->days;
        return abs($diff_in_days);

    }

    public function getYear($trans): int
    {
        $date = new \DateTime($trans->getDate());
        return $date->format('o');
    }

    public function getMonth($trans): int
    {
        $date = new \DateTime($trans->getDate());
        return $date->format('n');
    }


}
