/**
 * Утилита для реализации наследования
 * 
 * Данная утилита фактически является копией функции
 * из Yahoo! User Interface Library
 */

ns('utils');

/**
 * Utility to set up the prototype, constructor and superclass properties to
 * support an inheritance strategy that can chain constructors and methods.
 *
 * @method extend
 * @static
 * @param {Function} subc   the object to modify
 * @param {Function} superc the object to inherit
 * @param {Object} overrides  additional properties/methods to add to the
 *                              subclass prototype.  These will override the
 *                              matching items obtained from the superclass 
 *                              if present.
 */
utils.extend = function(subc, superc, overrides) {
    var F = function() {};
    F.prototype=superc.prototype;
    subc.prototype=new F();
    subc.prototype.constructor=subc;
    subc.superclass=superc.prototype;
    if (superc.prototype.constructor == Object.prototype.constructor) {
        superc.prototype.constructor=superc;
    }

    if (overrides) {
        for (var i in overrides) {
            subc.prototype[i]=overrides[i];
        }
    }
}