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

    public function ticketProblem(){
        try{
            $ticketProblem = $this->dashboardRepository->getTicketProblem();
            if(!$ticketProblem){
                return RestResponse::warming('Ticket problem count not found');
            }
            return RestResponse::success($ticketProblem,'Ticket problem data');
        }catch (\Exception $e){
            return RestResponse::error($e->getMessage(), $e);
        }
    }

    public function ticketPayment(){
        try{
            $ticketPayment = $this->dashboardRepository->getTicketPayment();
            if(!$ticketPayment){
                return RestResponse::warming('Ticket payment count not found');
            }
            return RestResponse::success($ticketPayment,'Ticket payment data');
        }catch (\Exception $e){
            return RestResponse::error($e->getMessage(), $e);
        }
    }
    public function ticketPriority(){
        try{
            $ticketPriority = $this->dashboardRepository->getTicketPriority();
            if(!$ticketPriority){
                return RestResponse::warming('Ticket priority count not found');
            }
            return RestResponse::success($ticketPriority,'Ticket priority data');
        }catch (\Exception $e){
            return RestResponse::error($e->getMessage(), $e);
        }
    }

    public function ticketAssignee(){
        try{
            $ticketAssignee = $this->dashboardRepository->getTicketAssignee();
            if(!$ticketAssignee){
                return RestResponse::warming('Ticket assignee count not found');
            }
            return RestResponse::success($ticketAssignee,'Ticket assignee data');
        }catch (\Exception $e){
            return RestResponse::error($e->getMessage(), $e);
        }
    }

    public function ticketRevenue(){
        try{
            $ticketRevenue = $this->dashboardRepository->getTicketRevenue();
            if(!$ticketRevenue){
                return RestResponse::warming('Ticket revenue data not found');
            }
            return RestResponse::success($ticketRevenue,'Ticket revenue data');
        }catch (\Exception $e){
            return RestResponse::error($e->getMessage(), $e);
        }
    }
}
