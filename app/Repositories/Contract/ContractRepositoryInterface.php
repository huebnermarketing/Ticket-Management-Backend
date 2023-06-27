<?php

namespace App\Repositories\Contract;


Interface ContractRepositoryInterface{
    public function getContractDetails();
    public function getContracts($data);
    public function getClientContracts($data);
    public function storeContract($data);
    public function storeContractService($contractId,$data);
    public function storeContractProductService($contractId,$data);
//    public function storeContractCostomer($contractId, $data);
    public function getSearchClient($data);
    public function archiveNotarchiveContract($data);
    public function updateContract($data);


}
