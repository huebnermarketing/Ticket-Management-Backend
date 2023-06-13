@include('layouts.header')
<div class="content-container">
    <div class="content">
        <p>Hello {{ $mailData['assign_user_name'] }},</p>
        <p>A support ticket has been assigned to you. Please review the details below:</p>
        <p><strong>Ticket ID:</strong> {{ $mailData['ticket_id'] }}</p>
        <p><strong>Subject:</strong> {{ $mailData['ticket_title'] }}</p>
        <p><strong>Due Date:</strong> {{ $mailData['due_date'] }}</p>
        <p><strong>Description:</strong> {{ !empty($mailData['description']) ? $mailData['description'] : 'N.A.' }}</p>
        <p><strong>Assigned By:</strong> {{ $mailData['assigned_by'] }}</p>

    </div>
    <div class="button-container">
        <a class="button" href="{{ $mailData['ticket_detail_url'] }}">View Ticket Details</a>
    </div>
    <div class="sub content">
        <p>Please take the necessary actions to address and resolve the ticket as soon as possible. If you have any questions or require additional information, feel free to reach out to the assigned administrator or the support team.</p>
    </div>
    <div class="content">
        <p>Best regards,<br>{{$companyName['company_name']}}</p>
    </div>
</div>
@include('layouts.footer')
