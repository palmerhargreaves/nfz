UsersPostForm = function(config) {
  this.onLoadCompanyPostUrl = config.onLoadCompanyPostUrl;
  this.onAcceptUserPostUrl = config.onAcceptUserPostUrl;

  $.extend(this, config);
}

UsersPostForm.prototype = {
  start: function() {
    this.initEvents();

    return this;
  },

  initEvents: function() {
    this.getCompanyDepartment().change($.proxy(this.onChangeCompanyDepartment, this));
    this.getAcceptUserPostButton().click($.proxy(this.onAcceptUserPost, this));
  },
  
  syncCompanyPost: function() {
    $.post(this.onLoadCompanyPostUrl, { companyDep : this.getCompanyDepartment().val() }, $.proxy(this.onLoadCompanyPost, this) );

    this.getAcceptUserPostButton().show();
  },

  onLoadCompanyPost: function(data) {
    this.getCompanyPost().empty().html(data);
    this.getCompanyPostBlock().show();

    if(this.getCompanyDepartment().val() != '4') {
      this.getCompanyPostBlock().show();
      $('.company-post-krik-select').krikselect();
    }
    else
      this.getCompanyPostBlock().hide();
  },

  onAcceptUserPost: function(e) {
    e.preventDefault();

    $.post(this.onAcceptUserPostUrl, 
          {
            department : this.getCompanyDepartment().val(),
            userPost : this.getUserPost().val()
          },
          function(result) {
            $("#users-post-modal").krikmodal('hide');
            $('#post-bg').hide();
        });
  },

  getCompanyDepartment: function() {
    return $(':input[name=company_department]', this.getForm()).eq(0);
  },

  getUserPost: function() {
    return $(':input[name=post]', this.getForm());
  },
    
  getCompanyDepartmentBlock: function() { 
    return $('.company-department', this.getForm());
  },

  getCompanyPost: function() {
    return $('.company-post', this.getForm());
  },

  getCompanyPostBlock: function() {
    return $('.company-post-block', this.getForm());
  },

  getAcceptUserPostButton: function() {
    return $('#accept-user-post-button');
  },

  onChangeCompanyDepartment: function() {
    this.syncCompanyPost();
  },

  getForm: function() {
    return $('#frmUsersPost');
  }

};