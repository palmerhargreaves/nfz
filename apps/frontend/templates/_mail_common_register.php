<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>VW</title>
</head>

<body style="font-family:Arial, Helvetica, sans-serif; color:#2d2d2d; font-size:14px; line-height:20px">
<table cellpadding="0" cellspacing="0" border="0" width="100%" height="100%" bgcolor="#ffffff">
    <tbody>
    <tr>
        <td valign="top">
            <table cellpadding="0" cellspacing="0" border="0" width="670" align="center" bgcolor="#ffffff">
                <tbody>
                <tr>
                    <td>
                        <table cellpadding="0" cellspacing="45" border="0" width="100%">
                            <tbody>
                            <tr>
                                <td width="70%"><img
                                        src="<?php echo sfConfig::get('app_site_url'); ?>/images/register/logo.png"
                                        alt=""/></td>
                                <td width="30%">
                                    <font face="Arial" color="#000000" size="1"><b>Volkswagen Service</b></font><br/>
                                    <font face="Arial" color="#000000" size="1" style="font-size:11px">Новому сотруднику<br/>дилерского
                                        центра</font>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        <img src="<?php echo sfConfig::get('app_site_url'); ?>/images/register/pic.jpg" width="670"
                             alt=""/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table cellpadding="0" cellspacing="20" border="0" align="center" width="620">
                            <tbody>
                            <tr>
                                <td>
                                    <font face="Arial" color="#000000" size="1"
                                          style="font-size:14px"><b>
                                            <?php if (isset($sf_data['user'])): ?><?php echo $user->getName() ?>, добрый день!<br/>
                                            <?php endif; ?>Ваш аккаунт активирован.<br/>
                                            Добро пожаловать в нашу команду!</br>
                                    </font>
                                    <br/>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td height="30"></td>
                <tr>
                </tbody>
            </table>
        </td>
    </tr>
    </tbody>
</table>
</body>
</html>