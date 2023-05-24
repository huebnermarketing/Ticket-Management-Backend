@include('layouts.header')
<table bgcolor="#F7F8F9" width="600" border="0" cellspacing="0" cellpadding="15" style="line-height: 1.5;">
    <tbody>
    <tr>
        <td>
            <table bgcolor="#ffffff" width="570" cellspacing="0" cellpadding="15" style="border-radius: 4px;">
                <tbody>
                <tr>
                    <td>
                        <table width="100%" cellspacing="0" cellpadding="0">
                            <tbody>
                            <tr>
                                <td style="font-size: 1px;height: 15px;line-height: 15px;">&nbsp;</td>
                            </tr>
                            <tr>
                                <td align="left">
                                    <h2 style="font-size: 20px;font-weight: 600;line-height: 1.2;margin: 0px;">
                                        Reset your password
                                    </h2>
                                </td>
                            </tr>
                            <tr>
                                <td style="font-size: 1px;height: 30px;line-height: 30px;">&nbsp;</td>
                            </tr>
                            </tbody>
                        </table>
                        <table width="100%" cellspacing="0" cellpadding="0">
                            <tbody>
                            <tr>
                                <td>
                                    <table width="100%" cellspacing="0" cellpadding="15"
                                           style="border: 1px solid #D8DCE2;border-radius: 4px;">
                                        <tbody>
                                        <tr>
                                            <td>
                                                <table width="100%" cellspacing="0" cellpadding="0">
                                                    <tbody>
                                                    <tr>
                                                        <td style="font-size: 1px;height: 15px;line-height: 15px;">
                                                            &nbsp;
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <p>If you requested that we reset your password, simply use
                                                                the link below to create a new one.</p>
                                                            <p>If you didn’t request that we change your password, then
                                                                you can ignore this email. If no action is taken, your
                                                                password will remain the same</p>
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                                <table class="w-auto" border="0" cellpadding="0" cellspacing="0"
                                                       role="presentation"
                                                       style="border-collapse:separate;line-height:100%;border-radius:4px;">
                                                    <tbody>
                                                    <tr>
                                                        <td align="center" bgcolor="#212934" role="presentation"
                                                            style="border:none;border-radius:4px;cursor:auto;padding:11px 20px;background:#212934;"
                                                            valign="middle">
                                                            <a href="{{ $resetData }}"
                                                               style="background:#212934;color:#ffffff;font-size:14px;font-weight:600;line-height:120%;margin:0;text-decoration:none;text-transform:none;border-radius:4px;"
                                                               target="_blank">
                                                                Choose a New Password
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                                <table width="100%" cellspacing="0" cellpadding="0">
                                                    <tbody>
                                                    <tr>
                                                        <td style="font-size: 1px;height: 30px;line-height: 30px;">
                                                            &nbsp;
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <h3 style="color: #263138;margin: 0;font-size: 18px;font-weight: 500;">
                                                                “Choose a New Password” button not working?
                                                            </h3>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="font-size: 1px;height: 15px;line-height: 15px;">
                                                            &nbsp;
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <p style="margin: 0px;">
                                                                Just copy and paste this link in your browser:
                                                            </p>
                                                            <p>
                                                                <a style="color: #445EA7;text-decoration: none;"
                                                                   href="{{ $resetData }}" target="_blank">
                                                                    {{ $resetData }}
                                                                </a>
                                                            </p>
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    </tbody>
</table>

@include('layouts.footer')
