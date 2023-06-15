<?php
namespace App\Repositories\Ticket;

Interface TicketRepositoryInterface{
    public function getTickets($filters);
    public function ticketListDashboard();
    public function storeTicket($data);
}
