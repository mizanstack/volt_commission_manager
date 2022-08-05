<?php

namespace Volt\Services;

use Volt\Models\User;
use Volt\Services\Calculation\Deposit;
use Volt\Services\Calculation\Withdraw;
use Volt\Services\Excel;
use Volt\Contracts\CommissionInterface;

class Commission implements CommissionInterface
{	
    protected $excel;

    public function __construct(Excel $excel)
    {
        $this->excel = $excel;
    }

    protected $user_transaction_list = [];

    public function makeReadyCommissionForUser(User $user)
    {
		$this->user_transaction_list[] = $user;        
    }

    public function processDataForCommission()
    {
        $excel_data = $this->excel->getAllData();
        $id = 1;
        foreach ($excel_data as $excel_row) {
            $user = new User();
            $user->setId($id++);
            $user->setDate($excel_row[0]);
            $user->setUserId((int) $excel_row[1]);
            $user->setUserType($excel_row[2]);
            $user->setOperationType($excel_row[3]);
            $user->setAmount($excel_row[4]);
            $user->setCurrency($excel_row[5]);
            $this->makeReadyCommissionForUser($user);
        }
    }


    public function getAllTransaction(): array
    {
        return $this->user_transaction_list;
    }

    public function getResult(): array
    {
    	$deposit = new Deposit;
    	$withdraw = new Withdraw($this);
    	$result = [];
    	foreach($this->user_transaction_list as $trans){

    		if($trans->getOperationType() == 'deposit'){
                $deposit_comission =  $deposit->getCommission($trans);
	    		$result[] = $this->rounded($deposit_comission);
    		}

    		if($trans->getOperationType() == 'withdraw'){
	    		$withdraw_comission = $withdraw->getCommission($trans);
                $result[] = $this->rounded($withdraw_comission);
    		}

    	}
    	return $result;
    }

    protected function rounded($amount): string
    {
        $rounded_result = number_format((float)$amount, 2, '.', '');
        return $rounded_result;
    }

    

}
