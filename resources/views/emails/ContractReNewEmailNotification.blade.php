@include('layouts.header')
<div class="content-container">
    <div class="content">
        <p>Hello {{ $mailData['user_name'] }},</p>
        <p>A system generated auto-renew contract record has been added. </p>
        <p><strong>Company Name: </strong> {{ $mailData['customer_location']['company_name'] }}</p>
        <p><strong>Customer Name:</strong> {{ $mailData['customer_name']['first_name']}} {{ $mailData['customer_name']['last_name']}}</p>
        <p><strong>Customer Mobile:</strong> {{ $mailData['customer_phone']['phone']}}</p>
        <p><strong>Contract Duration: </strong> {{ $mailData['contract']['start_date']}} - {{ $mailData['contract']['end_date']}}</p>
        <p><strong>Location: </strong> {{ $mailData['customer_location']['address_line1'] }}, {{ $mailData['customer_location']['area'] }}, {{ $mailData['customer_location']['city'] }}, {{ $mailData['customer_location']['state'] }}, {{ $mailData['customer_location']['country'] }}, {{ $mailData['customer_location']['zipcode'] }}</p>
        <p><strong>Contract Amount: :</strong> {{ $mailData['contract']['amount']}}</p>
    </div>
    <div class="sub content">
        <p>Please take the necessary actions to address and resolve the ticket as soon as possible. If you have any questions or require additional information, feel free to reach out to the assigned administrator or the support team.</p>
    </div>
    <div class="content">
        <p>Best regards,<br>{{$companyName['company_name']}}</p>
    </div>
</div>
@include('layouts.footer')
