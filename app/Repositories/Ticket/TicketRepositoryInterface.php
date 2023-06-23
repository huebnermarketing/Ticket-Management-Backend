<?php
namespace App\Repositories\Ticket;

Interface TicketRepositoryInterface{
    public function getTickets($filters);
    public function ticketListDashboard();
    public function storeTicket($data);
    public function findTicket($id);
    public function updateTicket($data,$ticketId);
}
