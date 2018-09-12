<ul class="nav nav-tabs" id="extended-statistic-tabs">
  <li class="active"><a href="#fields" data-toggle="tab">Поля</a></li>
  <li><a href="#cert-date" data-toggle="tab">Продление</a></li>
  <li><a href="#statistic" data-toggle="tab">Статистика</a></li>
  <li><a href="#mails" data-toggle="tab">Рассылка</a></li>
</ul>

<div class="tab-content">
    <div class="tab-pane active" id="fields">
        <div class="container-fluid">
            <div class="row-fluid">
              <div class="well span3">
                <div class="sidebar-nav" style="float:left; ">
                  <div id='section-form-container'>
                    <?php include_partial('section_form', array('sections' => $sections, 'section' => null)); ?>
                    <?php include_partial('sections', array('sections' => $sections)); ?>
                  </div>
                </div><!--/.well -->
              </div><!--/span-->

              <div class="well span9">
                <div class="row-fluid">
                  <div class="sidebar-nav">
                    <div id="field-form-container">
                      <?php include_partial('field_form', array('sections' => $sections, 'fields' => $fields, 'field' => null)); ?>
                      <?php include_partial('fields_list', array('fields' => $fields)); ?>
                    </div>

                  </div><!--/.well -->
                </div>
              </div><!--/span-->
            </div>
        </div>
    </div>
    
    <div class="tab-pane" id="cert-date">
        <div class="span9">
            <div class="row-fluid">
              <div class="well sidebar-nav">
                <div id="certificate-dates-form-container">
                  <?php include_partial('certificate_data_list', array('items' => $certificateItems)); ?>
                </div>

              </div><!--/.well -->
            </div>
          </div><!--/span-->
    </div>

    <div class="tab-pane" id="statistic">
        <div class="container-fluid">
            <div class="row-fluid">
                <div class="well span3">
                    <div class="sidebar-nav" style="float:left; ">
                      <div id='section-form-container'>
                        <?php include_partial('export_form', array('statistic' => $statistic, 'activity' => $activity)); ?>
                      </div>
                    </div><!--/.well -->
                </div><!--/span-->

                <div class="span9 well">
                    <div class="row-fluid">
                      <div class="sidebar-nav">
                        <div id="statistic-form-container">
                          <?php include_partial('statistic', array('statistic' => $statistic, 'activity' => $activity)); ?>
                        </div>

                      </div><!--/.well -->
                    </div>
                  </div><!--/span-->
            </div>
        </div>
    </div>

    <div class="tab-pane" id="mails">
        <div class="container-fluid">
            <div class="row-fluid">
                <div class="well span3">
                  <div class="sidebar-nav" style="float:left;">
                    <div id='mails-dealers-form-container'>
                      <?php include_partial('dealers'); ?>
                    </div>
                  </div><!--/.well -->
                </div><!--/span-->

                <div class="well span9">
                  <div class="row-fluid">
                    <div class="sidebar-nav">
                      <div id="mails-send-form-container">
                        <?php include_partial('mails', array('items' => $mailDealerList)); ?>
                      </div>
                    </div><!--/.well -->
                  </div>
                </div><!--/span-->
            </div>
        </div>
    </div>
</div>

<script>
$(function() {
   $("#extended-statistic-tabs a:first").tab('show');
});
</script>

