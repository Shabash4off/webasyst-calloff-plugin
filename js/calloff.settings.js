var CalloffSettings = (function () {
    /**
     * @constructor
     * @param {object} options
     * @param {string} options.$wrapper Wrapper 
     * @param {{[key: string]: string}} options.locale Locale strings
     * @param {string} options.lang
     */
    CalloffSettings = function (options) {
        this.$wrapper = options.$wrapper;
    
        this.locale = options.locale;
        this.lang = options.lang;

        // Init modules
        for (const moduleName in this.modules) {
            if (Object.hasOwnProperty.call(this.modules, moduleName)) {
                const module = this.modules[moduleName];
                
                module.apply(this, [this.$wrapper]);
            }
        }
    }

    CalloffSettings.prototype.modules = {
        switcher: function ($wrapper) {
            var $switchers = $wrapper.find('[data-type="switcher"]');

            $switchers.iButton({
                labelOn: this.locale['on'],
                labelOff: this.locale['off'],
                className: 'mini',
            });
        },

        redactor: function ($wrapper) {
            var $redactors = $wrapper.find('.redactor');

            $redactors.redactor({
                toolbarFixed: false,
                lang: this.lang,
                plugins: ['source', 'fontcolor', 'fontfamily', 'alignment', 'fontsize'],
                structure: true,
                paragraphize: false,
                replaceDivs: false
            });
        },

        editor: function ($wrapper) {
            var $editors = $wrapper.find('.editor');

            $editors.each(function () {
                var $editor = $(this);
                var editorMode = $editor.data('mode');

                CodeMirror.fromTextArea(this, { mode: editorMode  })
            });
        },

        tabs: function ($wrapper) {
            var $tabs = $wrapper.find('[data-block="tabs"]');

            $tabs.each(function () {
                var $tab = $(this);

                var $tabTriggers = $tab.find('[data-tab-trigger]');

                function showActiveTabContent(tab) {
                    $tabTriggers.each(function () { $(this).parent().removeClass('selected') })

                    var $tabTrigger = $tab.find('[data-tab-trigger="' + tab + '"]');
                    $tabTrigger.parent().addClass('selected');

                    var $tabContent = $tab.find('[data-tab-content]');
                    $tabContent.hide();

                    var $selectedTabContent = $tab.find('[data-tab-content="' + tab + '"]');
                    $selectedTabContent.show();

                }

                showActiveTabContent('display');
                
                $tabTriggers.on('click', function (event) {
                    event.preventDefault();

                    var tab = $(event.target).data('tab-trigger');
                    showActiveTabContent(tab);
                })
            })
        },

        storefrontSelect: function ($wrapper) {
            var self = this;

            var $storefrontSelect = $wrapper.find('#storefront-select');
            var $storefrontContent = $wrapper.find('#storefront-content');

            if ($storefrontSelect.length === 0) return;

            function showActiveTabContent() {
                $storefrontContent.html('<i class="icon16 loading"></i>');

                var selectedStorefront = $storefrontSelect.find(':selected');
                
                var storefrontDomain = selectedStorefront.data('domain');
                var storefrontUrl = selectedStorefront.data('url');

                $.post('?module=calloffPluginSettingsStorefront', {domain: storefrontDomain, url: storefrontUrl})
                .done(function (data) {
                    $storefrontContent.html(data);
                    new CalloffSettings({
                        $wrapper: $storefrontContent,
                        locale: self.locale,
                        lang: self.lang
                    })
                });
            }
            showActiveTabContent();
            $storefrontSelect.on('change', showActiveTabContent);
        },

        // selectTab: function ($wrapper) {
        //     var $tabSelects = $wrapper.find('[data-tab-select]');

        //     function showActiveTabContent() {
        //         var $tabSelect = $(this);

        //         var contentBlock = $tabSelect.data('tab-select');
        //         var $contentBlock = $wrapper.find('[data-tab-select-content-block="' + contentBlock + '"]');

        //         var $content = $contentBlock.find('[data-tab-select-content]');
        //         $content.hide();

        //         var selectedTabLabel = $tabSelect.find('option:selected').val();

        //         $contentBlock.find('[data-tab-select-content="'+selectedTabLabel+'"]').show();
        //     }

        //     $tabSelects.each(showActiveTabContent);

        //     $tabSelects.on('change', showActiveTabContent);
        // },

        tooltip: function ($wrapper) {
            $wrapper.find('[title]').tooltip();
        },

        spoiler: function ($wrapper) {
            var $spoilerTriggers = $wrapper.find('[data-spoiler-trigger]');
    
            $spoilerTriggers.each(function () {
                var $spoilerTrigger = $(this);
    
                var spoilerContentSelector = $spoilerTrigger.data('spoiler-trigger');
    
                var $spoilerContent = $wrapper.find('[data-spoiler-content="' + spoilerContentSelector + '"]');
    
                $spoilerContent.hide();
    
                if($spoilerTrigger.is(':checked')) $spoilerContent.show();
    
                $spoilerTrigger.on('click change', function (e) {
                    e.preventDefault();
    
                    $spoilerContent.toggle();
                })
            })
        },

        radioSpoiler: function ($wrapper) {
            var $spoilerTriggers = $wrapper.find('input[data-radio-spoiler-trigger]');

            function showSelected() {
                var $spoilerTrigger = $(this);

                var spoilerContentSelector = $spoilerTrigger.data('radio-spoiler-trigger');
                var spoilerContentValue = $spoilerTrigger.val();

                var $spoilerContent = $wrapper.find('[data-radio-spoiler-content="' + spoilerContentSelector + '"]');

                $spoilerContent.hide();

                var $selectedSpoilerContent = $spoilerContent.filter(function () {
                    return $(this).data('radio-spoiler-value') == spoilerContentValue;
                }) 

                $selectedSpoilerContent.show();
            }

            $spoilerTriggers.filter(function () { return $(this).is(':checked') }).each(showSelected)

            $spoilerTriggers.on('click change', showSelected)
            
        },

        dynamicAppearance: function($wrapper) {
            var $dynamicAppearanceBlocks = $wrapper.find('[data-block="dynamic-appearance"]');

            $dynamicAppearanceBlocks.each(function () {
                var $dynamicAppearanceBlock = $(this);

                var id = $dynamicAppearanceBlock.data('id');

                var $dynamicAppearanceInputs = $dynamicAppearanceBlock.find('[data-dynamic-appearance]');
                var $dynamicAppearanceStorage = $dynamicAppearanceBlock.find('[data-dynamic-appearance-storage]')
                var $dynamicAppearanceDist = $dynamicAppearanceBlock.find('[data-dynamic-appearance-dist]')

                var jss = initJSS();

                $dynamicAppearanceInputs.each(initStyles);
                $dynamicAppearanceInputs.each(executeStyles);
                $dynamicAppearanceInputs.on('input', executeStyles);

                function initJSS() {
                    var styles;
                    try{
                        var hiddenValue = $dynamicAppearanceStorage.val();
                        styles = JSON.parse(hiddenValue);
                    }
                    catch(err) {
                        styles = {};
                    } 

                    return new CalloffJSS(styles);
                }

                function initStyles() {
                    var $dynamicAppearanceInput = $(this);

                    var selector = $dynamicAppearanceInput.data('selector');
                    var property = $dynamicAppearanceInput.data('property');
                    var cell = +$dynamicAppearanceInput.data('cell') || 0;

                    var value = jss.getStyle(selector, property, cell);

                    if(!!value) $dynamicAppearanceInput.val(value);
                }

                function executeStyles() {
                    var $dynamicAppearanceInput = $(this);

                    var selector = $dynamicAppearanceInput.data('selector');
                    var property = $dynamicAppearanceInput.data('property');
                    var cell = +$dynamicAppearanceInput.data('cell') || 0;
                    var postfix = $dynamicAppearanceInput.data('postfix') || '';
                    var value = $dynamicAppearanceInput.find(':selected').val() || $dynamicAppearanceInput.val() || '';

                    if(typeof value !== 'undefined' && value.length > 0) {
                        jss.setStyle(selector, property, cell, value, postfix);
                    } else {
                        jss.setStyle(selector, property, cell, '');
                    }

                    injectCSS();
                }

                function injectCSS() {
                    var css = jss.getCSS('[data-dynamic-appearance-template="' + id + '"]');

                    var $styleEl = $('style[data-dynamic-appearance-style="' + id + '"]');
                    if($styleEl.length === 0) {
                        $styleEl = $('<style data-dynamic-appearance-style="' + id + '">' + css + '</style>')
                        $wrapper.append($styleEl);
                    } else {
                        $styleEl.html(css)
                    }

                    var storage = JSON.stringify(jss.styles);
                    var dist = jss.getCSS();

                    $dynamicAppearanceStorage.val(storage);
                    $dynamicAppearanceDist.val(dist);
                }
            })
        }
    }

    return CalloffSettings;

})()