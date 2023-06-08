@include('layouts.header')
<div class="content-container">
    <div class="content">
        <p>Hello {{ $resetData['user_name'] }},</p>
        <p>We received a request to reset the password for your account associated with this email address.</p>
        <p>To proceed with the password reset, please click the button below or copy and paste the full link into your browser:</p>
    </div>
    <div class="button-container">
        <a class="button" href="{{ $resetData['reset_pwd_link'] }}">Reset Password</a>
    </div>
    <div class="sub content">
        <p>If you did not initiate this request or believe it to be an error, you can safely ignore this email. Your password will remain unchanged.</p>
    </div>
    <div class="content">
        <p>Best regards,<br>{{ $resetData['user_name'] }}<br>{{$companyName['company_name']}}</p>
    </div>

</div>
@include('layouts.footer')
