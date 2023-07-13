@include('layouts.header')
<div class="content-container">
    <div class="content">
        <p>Hello {{ $mailData['assign_user_name'] }},</p>
        <p>New ticket has been assigned to you with ref. <strong>#{{$mailData['ticket_id']}}</strong>.</p>
        <p><strong>Problem Title:</strong> {{ $mailData['problem_title'] }}</p>
        <p><strong>Problem Type:
                @foreach($mailData['problem_types'] as $type)
                </strong> {{ $type['problem_name'] }}@if($mailData['problem_count'] != 1),@endif
                @endforeach
        </p>
        <p><strong>Description:</strong> {{ !empty($mailData['description']) ? $mailData['description'] : 'N.A.' }}</p>
        <p><strong>Appointment Type:</strong> {{ $mailData['appointment_type']['appointment_name'] }}</p>
        <p><strong>Due Date:</strong> {{ $mailData['due_date'] }}</p>
        <p><strong>Status:</strong> {{ $mailData['status_type']['status_name'] }}</p>
        <p><strong>Priority:</strong> {{ $mailData['priority']['priority_name'] }}</p><hr>
        <p><strong>Customer Name:</strong> {{ $mailData['customer_name']['first_name'] }} {{ $mailData['customer_name']['last_name'] }}</p>
        <p><strong>Customer Mobile:</strong> {{ $mailData['customer_phone']['phone'] }}</p>
        <p><strong>Location:</strong> {{ $mailData['customer_location']['address_line1'] }}, {{ $mailData['customer_location']['area'] }}, {{ $mailData['customer_location']['city'] }}, {{ $mailData['customer_location']['state'] }}, {{ $mailData['customer_location']['country'] }}, {{ $mailData['customer_location']['zipcode'] }}</p>
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
