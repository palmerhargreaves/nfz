ActivityStatisticBackend = function(config) {
  //activity data
  this.on_load_activity_data_url = '';

  //sections
  this.add_section_url = ''; // add new section
  this.begin_edit_section_url = ''; // edit section
  this.edit_section_url = ''; // edit section
  this.delete_section_url = ''; // delete section
  this.list_sections_url = ''; // sections list

  //fields
  this.add_field_url = ''; // add new field
  this.begin_edit_field_url = ''; // edit section
  this.edit_field_url = ''; // edit field
  this.delete_field_url = ''; // delete field
  this.list_field_url = ''; // fields list
  this.change_field_required_status_url = '';

  //mails lists
  this.add_dealer_to_mail_list = '';
  this.remove_dealer_from_mail_list = '';
  this.send_dealers_mail = '';
  this.on_change_dealer_mail_date_url = '';

  //Concepts
  this.add_new_concept = '';

  $.extend(this, config);
  
  this.activity_id = 0;
  this.calc_fields = [];

  this.CALC_FIELD = 'calc';
  this.CALC_FIELD_TYPE_CALC = 'calc';

  this.mailsLists = [];
}

ActivityStatisticBackend.prototype = {
  start: function() {
    this.initEvents();
    
    return this;
  },
  
  initEvents: function() {
    //Activity
    this.getActivitySb().change($.proxy(this.onLoadActivityData, this));

    //sections
    this.getActivityContainerData().on('click', '#btAddStatisticNewSection', $.proxy(this.onAddNewStatisticSection, this));
    this.getActivityContainerData().on('click', '.section-edit', $.proxy(this.onBeginEditSectionData, this));
    this.getActivityContainerData().on('click', '#btEditSection', $.proxy(this.onEditSectionData, this));
    this.getActivityContainerData().on('click', '.delete-section', $.proxy(this.onDeleteSectionItem, this));

    //Fields
    this.getActivityContainerData().on('click', '#btAddStatisticNewField', $.proxy(this.onAddNewStatisticField, this));
    this.getActivityContainerData().on('click', '.field-edit', $.proxy(this.onBeginEditFieldData, this));
    this.getActivityContainerData().on('click', '#btEditField', $.proxy(this.onEditFieldData, this));
    this.getActivityContainerData().on('click', '.delete-field', $.proxy(this.onDeleteFieldItem, this));
    this.getActivityContainerData().on('click', '#btFieldAddCalcField', $.proxy(this.onAddCalcField, this));

    this.getActivityContainerData().on('change', '#sbFieldType', $.proxy(this.onChangeFieldsType, this));
    this.getActivityContainerData().on('click', '.on-delete-field', $.proxy(this.onDeleteField, this));
    this.getActivityContainerData().on('click', '.ch-required-field', $.proxy(this.onChangeFieldRequiredStatus, this));

    //Dealers Mails
    this.getActivityContainerData().on('click', '.add-mail-dealer', $.proxy(this.onAddDealerToMailList, this));    
    this.getActivityContainerData().on('click', '.delete-mail-dealer-from-list', $.proxy(this.onDeleteDealerFromMailList, this));    
    this.getActivityContainerData().on('click', '#btSendDealersMail', $.proxy(this.onSendDealersMail, this));   
    this.getActivityContainerData().on('click', '.on-get-template', $.proxy(this.onSetMailTemplate, this));   

    this.getActivityContainerData().on('change', '.mail-dealer-date', $.proxy(this.onChangeDealerDate, this));

    //Concepts
    this.getActivityContainerData().on('click', '.on-add-new-concept', $.proxy(this.onBeginAddConcepts, this));

  },

  /*Activity data*/
  getActivityContainerData: function() {
    return $('#activity-statistic-data');
  },

  getActivitySb: function() {
    return $('#sbActivity');
  },

  onLoadActivityData: function(e) {
    if($(e.target).val() == -1)
      this.getActivityContainerData().empty();
    else
      $.post(this.on_load_activity_data_url, { id : $(e.target).val() }, $.proxy(this.onLoadActivityDataSuccess, this ));
  },

  onLoadActivityDataSuccess: function(data) {
    this.getActivityContainerData().empty().html(data).show();
  },
  /*Sections*/
  
  getBtAddNewStatisticSection: function() {
    return $('#btAddStatisticNewSection');
  },

  onAddNewStatisticSection: function(e) {
    e.preventDefault();

    this.sendSectionForm(this.add_section_url, true);
  },

  onAddStatisticSectionSuccess: function(data) {
    this.getSectionHeaderName().val('');

    this.onReloadSectionsList(data);
  },

  //Edit section data
  onBeginEditSectionData: function(e) {
    $.post(this.begin_edit_section_url, { id : $(e.target).data('id') }, $.proxy(this.onEditSectionGetDataSuccess, this));
  },

  onEditSectionGetDataSuccess: function(data) {
    this.getSectionFormContainer().empty().html(data);
  },

  onEditSectionData: function(e) {
    e.preventDefault();

    this.sendSectionForm(this.edit_section_url, false);
  },

  sendSectionForm: function(url, isNew) {
    this.getErrorContainer().hide();

    var hasError = false;
    if(this.getActivity().val() == -1 && isNew) {
      this.showError('Ошибка!', 'Выберите активность для добавления разделов или полей');
      return;
    }

    var values = this.wrapFromValues(this.getSectionForm().serializeArray());
    if(values.data.txtSectionName == undefined || $.trim(values.data.txtSectionName).length == 0) {
      this.showError('Ошибка!', 'Введите название раздела.');
      return;
    }

    values.data.activityId = this.getActivity().val();
    $.post(url, values.data, $.proxy(this.onAddStatisticSectionSuccess, this));
  },

  onDeleteSectionItem: function(e) {
    if(confirm('Удалить раздел ?'))
      $.post(this.delete_section_url, { id : $(e.target).data('id') }, $.proxy(this.onDeleteSectionItemSuccess, this));
  },

  onDeleteSectionItemSuccess: function(data) {
    this.getSectionFormContainer().empty().html(data);
  },

  getSectionForm: function() {
    return $('#frmSection')
  },

  getSectionHeaderName: function() {
    return $('#txtSectionName');
  },

  getSectionsContainer: function() {
    return $('#sections-container');
  },

  getActivity: function() {
    return $('#sbActivity');
  },

  getSectionFormContainer: function() {
    return $('#section-form-container');
  },

  showError: function(header, text) {
    this.getErrorContainer().empty().html('<h4>' + header + '</h4>' + text).fadeIn();
  },

  showSuccess: function(header, text) {
    this.getSuccessContainer().empty().html('<h4>' + header + '</h4>' + text).fadeIn();
  },

  getErrorContainer: function() {
    return $('.container-error');
  },

  getSuccessContainer: function() {
    return $('.container-success');
  },  

  onReloadSectionsList: function(data) {
    this.getSectionFormContainer().empty().html(data);
  },

  /*Fields*/
  getFieldsFormContainer: function() {
    return $('#field-form-container');
  },

  onAddNewStatisticField: function(e) {
    e.preventDefault();

    this.sendFieldForm(this.add_field_url, true);
  },

  onBeginEditFieldData: function() {

  },

  onEditFieldData: function() {
    e.preventDefault();
    
    this.sendFieldForm(this.edit_field_url, false);
  },

  onDeleteFieldItem: function() {
    if(confirm('Удалить поле ?')) {

    }
  },

  sendFieldForm: function(url, isNew) {
    this.getErrorContainer().hide();

    var hasError = false;
    if(this.getActivity().val() == -1 && isNew) {
      this.showError('Ошибка!', 'Выберите активность для добавления разделов или полей');
      return;
    }

    var calcFieldVal = this.getCalcFieldsList().val();
    if(calcFieldVal == this.CALC_FIELD_TYPE_CALC && this.calc_fields.length == 0) {
        this.showError('Ошибка!', 'Добавьте вычисляемые поля.');
        return;   
    }

    var values = this.wrapFromValues(this.getFieldForm().serializeArray());
    if(values.data.txtFieldName == undefined || $.trim(values.data.txtFieldName).length == 0) {
      this.showError('Ошибка!', 'Введите название поля.');
      return;
    }
    else
      values.data.sbFieldCalcFields = this.getCalculatedFieldsList().val();

    values.data.activityId = this.getActivity().val();

    if(this.calc_fields.length != 0)
      values.data.calcFields = this.calc_fields;

    $.post(url, values.data, $.proxy(this.onAddStatisticFieldSuccess, this));
  },

  onAddStatisticFieldSuccess: function(data) {
    this.onReloadFieldsList(data);
    this.calc_fields = [];
  },

  onReloadFieldsList: function(data) {
    this.getFieldsFormContainer().empty().html(data);
  },

  onAddCalcField: function(e) {
    e.preventDefault();

    this.getErrorContainer().hide();
    var calcFieldVal = this.getCalculatedFieldsList().val();
    if(calcFieldVal == null) 
    {
      this.showError('Ошибка!', 'Выберите вычисляемое поле.');
      return;
    }

    var data = new Object();

    data.field = calcFieldVal;
    data.fieldText = $('#sbFieldCalcFields :selected').text();

    this.calc_fields.push(calcFieldVal);
    $('#sbFieldCalcFields :selected').remove();

    this.addFieldCalcInfo(data);
  },

  addFieldCalcInfo: function(data) {
    $('.container-calculated-fields-to-add').append('- <span style="margin: 10px; font-weight: bold;">' + data.fieldText + '</span><br/>');
  },

  getFieldForm: function() {
    return $('#frmField');
  },

  getFieldType: function() {

  },

  getAddCalcFieldButton: function() {
    return $('#btFieldAddCalcField');
  },

  getCalcFieldsList: function() {
    return $('#sbFieldType');
  },

  getCalcultatedFieldsContainer: function() {
    return $('#container-for-calculated-fields');
  },

  onChangeFieldsType: function() {
    this.getCalcultatedFieldsContainer().hide();

    if(this.getCalcFieldsList().val() == this.CALC_FIELD)
      this.getCalcultatedFieldsContainer().show();
  },

  getCalculatedFieldsList: function() {
    return $('#sbFieldCalcFields');
  },

  getBtAddCalcField: function() {
    return $('#btAddCalcField');
  },

  onDeleteField: function(e) {
    if(confirm('Удалить поле ?')) {
      $.post(this.delete_field_url, { id : $(e.target).parent().data('id') }, $.proxy(this.onDeleteFieldSuccess, this));
    }
  },

  onDeleteFieldSuccess: function(data) {
    this.onReloadFieldsList(data);
  },
  /*Fields*/

  /*Mail Lists*/
  onAddDealerToMailList: function(e) {
    var $el = $(e.target);

    $.post(this.add_dealer_to_mail_list, { id : this.getMailDealer().val(), activity: this.getActivity().val() }, $.proxy(this.onAddDealerToMailListSuccess, this));
  },

  onAddDealerToMailListSuccess: function(data) {
    this.getDealerMailsList().empty().html(data);

    $('input.mail-dealer-date').datepicker({ dateFormat: "dd-mm-yy" });
  },

  onDeleteDealerFromMailList: function(e) {
    var $el = $(e.target);

    this.deletedMailDealer = $el;
    $.post(this.remove_dealer_from_mail_list, { id : $el.data('id')}, $.proxy(this.onDeleteDealerFromMailListSuccess, this));
  },

  onDeleteDealerFromMailListSuccess: function(data, result, item) {
    this.deletedMailDealer.closest('tr').remove();
    this.deletedMailDealer = null;
  },

  getDealerMailsList: function() {
    return $('.table-mail-dealers-list');
  },

  getMailDealer: function() {
    return $('#sbMailDealers');
  },

  onSendDealersMail: function(e) {
    var selectedItems = this.getCheckedItems('ch-dealer-mail-item', 'data-id'),
          msgText = $.trim($('#txtMailMsg').val());

    this.getErrorContainer().hide();
    if(msgText.length == 0) {
      this.showError('Ошибка', 'Для продолжения введите текст рассылки');
      return;
    }

    var dealers = '';
    if(selectedItems.length > 0) {
      dealers = selectedItems;
    }

    this.getSuccessContainer().hide();
    $.post(this.send_dealers_mail,
            {
              msg : msgText,
              dealers : dealers,
              activity: this.getActivity().val()
            },
            $.proxy(this.onSendDealerMailSuccess, this));
  },

  onSendDealerMailSuccess: function(data) {
    $('.container-sended-mails-to-dealers').empty().html(data);

    this.showSuccess("Рассылка писем", "Рассылка писем по выбранному шаблону прошла успешно.");
  },

  onSetMailTemplate: function(e) {
    $('#txtMailMsg').val($(e.target).data('text'));
  },

  onChangeDealerDate: function(e) {
    $.post(this.on_change_dealer_mail_date_url,
            {
              id : $(e.target).data('id'),
              data: $(e.target).val()
            }, 
            $.proxy(this.onChangeDealerDataSuccess, this));
  },

  onChangeDealerDataSuccess: function(data) {
    
  },

  onChangeFieldRequiredStatus: function(e) {
    $.post(this.change_field_required_status_url,
        {
          fieldId : $(e.target).data('field-id'),
          status : $(e.target).is(':checked') ? 1 : 0
        },
        $.proxy(this.onChangeFieldRequiredStatusSuccess));
  },

  onChangeFieldRequiredStatusSuccess: function(data) {

  },

  //Concepts
  onBeginAddConcepts: function(e) {

  },

  wrapFromValues: function(values)
  {
    var dataArray = new Object(), data = new Object();

    for(index in values) {
      if(values[index].value) {
        dataArray[values[index].name] = values[index].value;
      }
    }

    //data.data = JSON.stringify(dataArray);
    data.data = dataArray;

    return data;
  },

  getCheckedItems: function(cls, attr) {
    var result = new Array(),
      data = ""; 

    $.each($("." + cls), function(){
      if($(this).is(':checked'))
        result.push($(this).attr(attr));
    });
    
    if(result.length != 0)
      data = result.join(',');
  
    return data;
  }

}