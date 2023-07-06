<?php

namespace App\Repositories\Contract;


Interface ContractRepositoryInterface{
    public function getContractDetails();
    public function getContracts($data);
    public function getClientContracts($data);
    public function viewContract($data);
    public function storeContract($data);
    public function storeContractService($contractId,$data);
    public function storeContractProductService($contractId,$data);
    public function getSearchClient($data);
    public function updateContract($data);
    public function suspendContract($data);


}
