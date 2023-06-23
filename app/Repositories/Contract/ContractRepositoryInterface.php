<?php

namespace App\Repositories\Contract;


Interface ContractRepositoryInterface{

    public function getContracts($data);
    public function getClientContracts($data);
    public function storeContract($data);
    public function getSearchClient($data);
    public function archiveNotarchiveContract($data);
}
