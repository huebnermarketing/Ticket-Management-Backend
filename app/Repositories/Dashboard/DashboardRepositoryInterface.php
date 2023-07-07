<?php
namespace App\Repositories\Dashboard;

Interface DashboardRepositoryInterface{
    public function getDetails();
    public function getTicketStatus();
    public function getTicketProblem();
    public function getTicketPayment();
    public function getTicketPriority();
    public function getTicketAssignee();
    public function getTicketRevenue();
}

