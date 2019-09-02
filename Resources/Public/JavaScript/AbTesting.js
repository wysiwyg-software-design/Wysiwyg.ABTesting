var WY = WY || {};

WY.AbTesting = {

    abTestingCookieString: '',
    abTestingObject: {},

    init: function () {
        this.abTestingCookieString = $.cookie("WYSIWYG_AB_TESTING");
        this.abTestingObject = JSON.parse(this.abTestingCookieString);
    },

    /**
     * This function builds an array of TrackingStrings.
     *
     * Example:
     * [
     *  'featureX_a',
     *  'featureY_b'
     * ]
     *
     * @returns {Array}
     */
    getTrackingStringsArrayForAllFeatures: function() {
        let trackingStringsArray = [];

        for (let featureName in this.abTestingObject) {
            trackingStringsArray.push(this.getTrackingStringForFeature(featureName));
        }

        return trackingStringsArray;
    },

    /**
     * This functions concat a featureName with a decision separated by an underscore ('_') for a given feature by featureName.
     *
     * Example: 'feature_a'
     *
     * @param featureName
     * @returns {string}
     */
    getTrackingStringForFeature: function(featureName) {
        return featureName + '_' + this.getDecisionForFeature(featureName);
    },

    /**
     * This function returns an abTestingObject.
     * An Object has a featureName for every key and the value represents the decision for a feature.
     *
     * Example:
     * {
     *     featureX: 'a',
     *     featureY: 'b'
     * }
     *
     * @returns {WY.AbTesting.abTestingObject|{}}
     */
    getDecisionsForAllFeatures: function () {
        return this.abTestingObject;
    },

    /**
     * Searches the abTestObject for a property which matches the featureName and return its value.
     *
     * @param featureName
     * @returns {string}
     */
    getDecisionForFeature: function (featureName) {
        if (featureName in this.abTestingObject) {
            return this.abTestingObject[featureName];
        }
    }
};
