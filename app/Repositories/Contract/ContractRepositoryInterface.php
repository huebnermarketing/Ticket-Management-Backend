<?php

namespace App\Repositories\Contract;


Interface ContractRepositoryInterface{

    public function getContracts();
    public function storeContract($data);
}
