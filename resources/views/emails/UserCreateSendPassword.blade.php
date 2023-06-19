@include('layouts.header')
<div class="content-container">
    <div class="content">
        <p>Hello {{$mailData['first_name']}} {{$mailData['last_name']}},</p>
        <p>Your account password has been set by the admin. Please use the following password to log in:</p>
        <p>Password: <strong>{{ $mailData['password'] }}</strong></p>
        <p>After logging in with the password, we recommend changing it to a new password of your choice from your User Profile settings.</p>
    </div>
    <div class="button-container">
        <a class="button" href="{{ config('constant.FRONTEND_URL') }}/login">Log In</a>
    </div>
    <div class="content">
        <p>Best regards,<br>{{$companyName['company_name']}}</p>
    </div>
</div>
@include('layouts.footer')
