<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Master</title>
    <style>
        /* Reset some default styles to ensure consistency across email clients */
        body,
        table,
        td,
        a {
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #333333;
            margin: 0;
            padding: 0;
        }
        table {
            border-collapse: collapse;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }
        td {
            vertical-align: top;
        }

        /* Container */
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header */
        .header {
            background-color: #3498db;
            padding: 15px 10px;
            text-align: center;
        }
        .header .company-name {
            font-size: 18px;
            font-weight: bold;
            margin: 0;
            color: #ffffff;
        }

        /* Content */
        .content-container {
            border: 1px solid #e1e1e1;
            border-radius: 4px;
            padding: 20px;
            margin: 20px 0;
        }
        .content p {
            margin: 0 0 10px;
        }

        .sub {
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #929292;
            margin: 0;
            padding: 0;
        }

        /* Button */
        .button-container {
            margin-bottom: 20px;
        }
        .button {
            display: inline-block;
            background-color: #3498db;
            color: #ffffff;
            text-decoration: none;
            font-weight: bold;
            padding: 10px 20px;
            border-radius: 5px;
        }
        .button:hover {
            background-color: #258cd1;
        }

        /* Footer */
        .footer {
            background-color: #333333;
            margin-top: 20px;
            padding: 1px 10px;
            font-size: 12px;
            color: #ffffff;
        }


    </style>
</head>
<body>
<table class="container" align="center" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td>
            <div class="header">
                @if(!empty($companyName['company_logo']))
                    <img src="{{ $companyName['company_logo'] }}" alt=""
                         style="margin: 0; border: 0; padding: 0; display: block;" height="30">
                @else
                    <h2 class="company-name">{{ $companyName['company_name'] }}</h2>
                @endif
            </div>
