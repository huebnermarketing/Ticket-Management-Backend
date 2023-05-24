{{--<div id="copyright text-right">© Copyright 2023 </div>--}}
<table bgcolor="#ffffff" width="600" border="0" cellspacing="0" cellpadding="15" style="line-height: 1.5;">
    <tbody>
    <tr>
        <td>
            <table width="570" cellspacing="0" cellpadding="0">
                <tbody>
                <tr>
                    <td>
                        <p style="font-size: 12px;margin: 0px;font-weight: 400;color: #788A95;text-align: center;">
                            Questions about setting up {{ str_replace("-"," ", config('constant.APP_NAME')) }} for your
                            own company? <br>Email us at
                            <a href="mailto:{{ config('constant.MAIL_SALES_ADDRESS') }}"
                               style="color: #445EA7;font-size: 12px;font-weight: 600;text-decoration: none;">
                                sales@tickethub.com
                            </a>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td style="font-size: 1px;height: 10px;line-height: 10px;">&nbsp;</td>
                </tr>
                </tbody>
            </table>
            <table width="570" cellspacing="0" cellpadding="0">
                <tbody>
                <tr>
                    <td style="font-size: 1px;height: 15px;line-height: 15px;">&nbsp;</td>
                </tr>
                <tr>
                    <td>
                        <table bgcolor="#ffffff" width="100%" cellspacing="0" cellpadding="10"
                               style="border-radius: 4px;">
                            <tbody>
                            <tr>
                                <td>
                                    <table cellspacing="0" cellpadding="0">
                                        <tbody>
                                        <tr>
                                            <td align="left" valign="middle" style="padding-right: 10px;">
                                                <img src="{{ asset('img/email-footer-icon.png') }}" alt=""
                                                     style="margin: 0 auto; border: 0; padding: 0; display: block;"
                                                     height="22">
                                            </td>
                                            <td align="left" valign="middle"
                                                style="font-size: 13px;margin: 0px;font-weight: 400;color: #788A95;">
                                                &copy;
                                                copyright {{ str_replace("-"," ", config('app.name'))}} {{date('Y')}}
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
</body>
</html>
