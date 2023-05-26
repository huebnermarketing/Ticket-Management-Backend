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
                                        Your Account password
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
                                                        <td style="font-size: 1px;height: 15px;line-height: 15px;"></td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <p>Dear, {{$mailData['first_name']}} {{$mailData['last_name']}}</p>
                                                            <p>We are writing to inform you that a password has been initiated for your account at Ticket Management.
                                                                As requested, we are providing you with a password to access your account.</p>
                                                            <p>Password: {{$mailData['password']}}</p>
                                                            <p>Thank you for your cooperation. Should you have any further questions or require additional assistance,
                                                                please do not hesitate to reach out to us. We are here to help.</p>
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
