/**
 * Created by kostet on 04.08.2016.
 */
ActivityQuartersData = function(config) {
    this.activity = 0;
    this.on_activity_quarter_change_url = '';

    $.extend(this, config);

}

ActivityQuartersData.prototype = {
    start: function() {
        this.initEvents();

        return this;
    },

    initEvents: function() {
        $(document).on('change', '.activity-header-selects input[name]', $.proxy(this.onChangeCompanyTypeActivity, this));
        this.getTabHeaderWrapper().on('click', '.activity-quarter-data', $.proxy(this.onActivityQuarterClick, this));

    },

    onActivityQuarterClick: function() {

    },

    onChangeCompanyTypeActivity: function(e) {
        var $from = $(e.target), link = $from.val();

        if (link != "") {
            window.location.href = link;
        }
    },

    getTabHeaderWrapper: function() {
        return $('.activity-quarter-data');
    }
}