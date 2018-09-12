<!DOCTYPE html>
<html>
    <head>
        <title></title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    </head>
    <body id="mailsub">
        <table cellspacing="0" cellpadding="0" border="0" width="100%">
          <tr>
            <td align="center">
                    
              <table style="border: 1px solid #eaeaea; border-style: collapse;" width="560" cellspacing="0" cellpadding="0" border="1" bordercolor="#eaeaea">
                  <tr>
                      <td border="0">
                          <table cellspacing="0" cellpadding="0" border="0" width="100%"><tr>
                                  <td align="left">
                                      <font style="font-size: 14px;" size="2" face="Verdana" color="#282727">

                                      <font color="#0060ad"><b>Здравствуйте, <?php echo $user->getName() ?>!</b></font><br/><br/>
<?php echo $sf_data->getRaw('text') ?>

<br/>
<br/>
С уважением,<br/>
команда Servicepool<br/><br/>
                                      </font>
                                  </td>
                              </tr>
                          </table>
                      </td>
                  </tr>
              </table>
              
            </td>
          </tr>
        </table>
    </body>
</html>
