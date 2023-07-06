<?php

namespace App\Http\Controllers\Api\v1\Dashboard;

use App\Http\Controllers\Controller;
use RestResponse;
use Illuminate\Http\Request;
use App\Repositories\Dashboard\DashboardRepositoryInterface;

class DashboardController extends Controller
{
    private $dashboardRepository;

    public function __construct(DashboardRepositoryInterface $dashboardRepository)
    {
        $this->dashboardRepository = $dashboardRepository;
    }
    public function ticketDetails(){
        try{
            $dashboardData = $this->dashboardRepository->getDetails();
            if(!$dashboardData){
                return RestResponse::warning('Dashboard data not found');
            }
            return RestResponse::success($dashboardData,'Dashboard data');
        }catch (\Exception $e) {
            return RestResponse::error($e->getMessage(), $e);
        }
    }

    public function ticketStatus(){
        try{
            $ticketStatus = $this->dashboardRepository->getTicketStatus();
            if(!$ticketStatus){
                return RestResponse::warning('Ticket status data not found');
            }
            return RestResponse::success($ticketStatus,'Ticket status data');
        }catch (\Exception $e){
            return RestResponse::error($e->getMessage(), $e);
        }
    }
}
