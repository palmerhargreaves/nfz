Info = function(config) {
  // configurable {
  // }
  this.modal = '';
  
  this.load_fields_url = '';
  this.load_field_data = '';

  this.accept_url = '';
  this.delete_url = '';

  this.activity_id = 0;

  $.extend(this, config);

  this.fieldId = 0;
}

Info.prototype = {
  start: function() {
    this.initEvents();
    
    return this;
  },
  
  initEvents: function() {
    var self = this;

    /*this.getDealersSelect().change($.proxy(this.onSelectDealer, this));
    this.getTasksListBlock().on('click', 'a', $.proxy(this.onClickActionLink, this));*/
    this.getActionConfigInfoParam().click($.proxy(this.configActivityInfoParams, this));
    
    this.getFieldsList().on('click', 'a', $.proxy(this.onClickField, this));
    this.getPanelContentContainer().on('click', '.action-activity-config-info-params-add-field', $.proxy(this.addNewField, this));

    this.getPanelContentContainer().on('input', 'textarea', $.proxy(this.onTxtChangeFieldData, this));
    this.getPanelSaveFieldBt().click($.proxy(this.onSaveFieldsData, this));

    this.getPanelContentContainer().on('click', '.action-field-data-delete', $.proxy(this.onDeleteFieldData, this));
  },

  configActivityInfoParams: function(e) {
    this.resetParams();

    this.activity_id = $(e.target).data('id');

    this.getModal().modal('show');
    this.onLoadData();
  },

  resetParams: function() {

  },

  onClickField: function(e) {
    this.getPanelSaveFieldBt().hide();

    this.getPanelSaveFieldBt().attr('data-activity-id', $(e.target).data('id'));
    this.getPanelSaveFieldBt().attr('data-field-id', $(e.target).data('field-id'));

    this.fieldId = $(e.target).data('field-id');
    $.post(this.load_field_data,
          { 
              id : $(e.target).data('id'),
              fieldId : $(e.target).data('field-id')
          },
          $.proxy(this.onLoadFieldDataToContentContainer, this));  

    this.onLoadData();
  },

  onLoadData: function() {
    $.post(this.load_fields_url, 
            {
              activity_id : this.activity_id
            },
            $.proxy(this.onAddInfoFieldsList, this));
  },

  addNewField: function(e) {
    var $panel = this.getPanelFieldsContainer(),
        $fields = $panel.find('textarea');

    if($fields.length != 0)
      $fields.last().after('<textarea name="field_data' + $(e.target).data('field-id') + '[]" data-field-id="' +$(e.target).data('field-id') + '" data-activity-id="' +$(e.target).data('activity-id') + '" style="width: 460px;" rows="2"></textarea>');
    else
      $panel.find('a').before('<textarea name="field_data' + $(e.target).data('field-id') + '[]" data-field-id="' +$(e.target).data('field-id') + '" data-activity-id="' +$(e.target).data('activity-id') + '" style="width: 460px;" rows="2"></textarea>');

    this.getPanelSaveFieldBt().show();
  },

  onTxtChangeFieldData: function() {
    this.getPanelSaveFieldBt().show();
  },

  onAddInfoFieldsList: function(data) {
    this.getPanelLeftContainer().html(data);
  },

  onLoadFieldDataToContentContainer: function(data) {
    this.getPanelContentContainer().html(data);
  },

  onSaveFieldsData: function(e) {
    var $fields = this.getPanelContentContainer().find('textarea'),
        data = [], ind = 0;

    $.each($fields, function(ind, element) {
      var $el = $(element);
      
      if($el.attr('data-id') != undefined) {
        data[ind] = {
          msg: $el.val(),
          isNew: 0,
          id: $el.data('id')
        };
      }
      else {
        data[ind] = {
          msg: $el.val(),
          isNew: 1,
          activityId: $el.data('activity-id'),
          fieldId: $el.data('field-id')
        };
      }

      ind++;
    });

    $.post(this.accept_url,{
          data: data
        }, 
        $.proxy(this.onAddFieldDataResult ,this));
  },

  onAddFieldDataResult: function(data) {
    if($.trim(data).length > 0)
      $('.field-info-' + this.fieldId).trigger('click');
  },

  onDeleteFieldData: function(e) {
    if(confirm('Удалить ?')) {
      this.fieldId = $(e.target).data('field-id');
      $.post(this.delete_url,
              {
                id: $(e.target).data('id')
              },
              $.proxy(this.onFieldDataDeleted, this));
    }
  },

  onFieldDataDeleted: function(data) {
    if($.trim(data))
      $('.field-info-' + this.fieldId).trigger('click');
  },

  getModal: function() {
    return $(this.modal);
  },

  getPanelLeftContainer: function() {
    return $('.panel-info-left', this.getModal());
  },

  getPanelContentContainer: function() {
    return $('.panel-info-content', this.getModal());
  },  

  getPanelLinkToAddNewField: function() {
    return $('.action-activity-config-info-params-add-field', this.getModal());
  },

  getPanelFieldsContainer: function() {
    return $('.inputs-fields', this.getModal());
  },

  getPanelSaveFieldBt: function() {
    return $('.action-activity-config-info-params-save-fields', this.getModal());
  },

  getActionConfigInfoParam: function() {
    return $('.action-activity-config-info-params');
  },

  getActivityFieldDataLink: function() {
    return $('.action-activity-load-field-data');
  },

  getFieldsList: function() {
    return $('.fields-list');
  }
}