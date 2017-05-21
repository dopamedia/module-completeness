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
            return value.completeness;
        }
    });
});
