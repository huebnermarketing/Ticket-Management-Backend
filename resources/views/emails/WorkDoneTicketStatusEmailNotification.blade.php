@include('layouts.header')
<div class="content-container">
    <div class="content">
        <p>Hello {{ $mailData['user_name'] }},</p>
        <p>Ticket with ref. <strong>#{{$mailData['ticket']['unique_id']}}</strong> has been marked as Work Done.</p>
        <p><strong>Problem Title:</strong> {{ $mailData['ticket']['problem_title'] }}</p>
        <p><strong>Problem Type:
                @foreach($mailData['problem_types'] as $type)
            </strong> {{ $type['problem_name'] }},
            @endforeach
        </p>
        <p><strong>Assignee:</strong> {{ $mailData['assignee']['first_name'] }}  {{ $mailData['assignee']['last_name'] }}</p>
        <p><strong>Due Date:</strong> {{ $mailData['ticket']['due_date'] }} </p>
        <p><strong>Appointment type:</strong> {{ $mailData['appointment_type']['appointment_name'] }} </p>
        <p><strong>Customer Name:</strong> {{ $mailData['customer_name']['first_name'] }}  {{ $mailData['customer_name']['last_name'] }} </p>
        <p><strong>Mobile Number:</strong> {{ $mailData['customer_phone']['phone'] }} </p>
        <p><strong>Company Name:</strong> {{ $mailData['customer_location']['company_name'] }} </p>
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
