var CalloffJSS = (function () {
    
    /**
     * @param {{[selector: string]: {[propertyName: string]: Array<{value: string, postfix: string}>}}} styles 
     */
     CalloffJSS = function (styles) {
        this.styles = styles || {};
    }
    /**
     * @param {string} selector 
     * @param {string} property 
     * @param {number} cell 
     * @param {string} value 
     */
     CalloffJSS.prototype.setStyle = function (selector, property, cell, value, postfix) {
        if(typeof this.styles[selector] === 'undefined') this.styles[selector] = {};
        
        if(typeof this.styles[selector][property] === 'undefined') this.styles[selector][property] = [];

        this.styles[selector][property][cell] = { value: value, postfix: postfix };
    }

    CalloffJSS.prototype.getStyle = function (selector, property, cell) {
        if(typeof this.styles[selector] === 'undefined') return null;
        
        if(typeof this.styles[selector][property] === 'undefined') return null;

        if(typeof this.styles[selector][property][cell] === 'undefined' && this.styles[selector][property][cell]['value'].trim().length === 0) return null;

        return this.styles[selector][property][cell]['value'];
    }

    CalloffJSS.prototype.getCSS = function (parentSelector) {
        var css = '';

        for (const selector in this.styles) {
            if (Object.hasOwnProperty.call(this.styles, selector)) {
                const property = this.styles[selector];
                
                css += [parentSelector, selector].join(' ') + '{'
                
                for (const propertyName in property) {
                    if (Object.hasOwnProperty.call(property, propertyName)) {
                        const propertyValues = property[propertyName];

                        const propertyValue = $.map(propertyValues, function (v) { return v.postfix ? v.value + v.postfix : v.value }).join(' ');

                        if(propertyValue.trim().length === 0) continue;
                        
                        css += propertyName + ':' + propertyValue + ';';
                    }
                }

                css += '}'

            }
        }

        return css;
    }

    return CalloffJSS;
})()