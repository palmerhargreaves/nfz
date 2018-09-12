/**
 * Created by kostet on 16.08.2016.
 */

ActivityEfficiencyGraphArrow = function(config) {
    this.arrow_slider_selector = '';
    this.allow_animate_digits = false;

    /*values*/
    this.effectiveness_of_your_action = 0;
    this.revenue = 0;
    this.costs = 0;

    $.extend(this, config);

    this.FORMULA_TYPE_EFFICTIVENESS = 'effectiveness';
    this.FORMULA_TYPE_REVENUE = 'revenue';
    this.FORMULA_TYPE_COSTS = 'costs';
}

ActivityEfficiencyGraphArrow.prototype = {
    start: function() {
        this.initEvent();

        return this;
    },

    initEvent: function() {
        var container_height = (this.getArrowSliderContainer().height()),
            center_position = container_height * 50 / 100,
            arrow_position = center_position;

        if (this.allow_animate_digits) {
            this.animateDigits();
        }

        if (this.getEffectivenessOfYourAction() < 0) {
            arrow_position = Math.round(center_position + (center_position * Math.abs(this.getEffectivenessOfYourAction()) / this.getCosts()));
        } else {
            arrow_position = Math.round(center_position - (center_position * this.getEffectivenessOfYourAction() / this.getRevenue()));
        }

        this.getArrowSlider().animate( { top : arrow_position + 'px' }, 1000 );
    },

    animateDigits: function() {
        $('.efficiency-graph-value').each(function(ind, el) {
            $(el).find('span').animateNumber( { number: $(el).data('value') } );
        });
    },

    getArrowSlider: function() {
        return $(this.arrow_slider_selector);
    },

    getArrowSliderContainer: function() {
        return this.getArrowSlider().parent();
    },

    //Еффективность
    getEffectivenessOfYourAction: function() {
        return this.checkFormulaTypeAndGetValue(this.FORMULA_TYPE_EFFICTIVENESS);
    },

    // Выручка
    getRevenue: function() {
        return this.checkFormulaTypeAndGetValue(this.FORMULA_TYPE_REVENUE);
    },

    // Расходы по активности
    getCosts: function() {
        return this.checkFormulaTypeAndGetValue(this.FORMULA_TYPE_COSTS);
    },

    getGraphArrowValue: function() {
        return $('.efficiency-graph-value');
    },

    checkFormulaTypeAndGetValue: function(type) {
        var $found_el = undefined;

        this.getGraphArrowValue().each(function(i, el) {
            if ($(el).data('formula-type') == type) {
                $found_el = $(el);
            }
        });

        if ($found_el != undefined) {
            return $found_el.data('value');
        }

        return 0;
    }
}
