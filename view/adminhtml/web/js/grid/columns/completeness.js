/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    './column'
], function (Column) {
    'use strict';

    return Column.extend({

        /**
         * sets the label to the html value
         *
         * @returns {String} label.
         */
        getLabel: function (value) {
            var _value = value.completeness;
            if (_value == 0) {
                return "<span class=\"grid-severity-critical\"><span>" + _value + " %</span></span>";
            } else if (value < 100) {
                return "<span class=\"grid-severity-minor\"><span>" + _value + " %</span></span>";
            } else {
                return "<span class=\"grid-severity-notice\"><span>" + _value + " %</span></span>";
            }
        }
    });
});
