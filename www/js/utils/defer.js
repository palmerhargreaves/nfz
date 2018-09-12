/**
     * Calls this function after the number of millseconds specified, optionally in a specific scope. Example usage:
     * 


var sayHi = function(name){
    alert('Hi, ' + name);
}

// executes immediately:
sayHi('Fred');

// executes after 2 seconds:
sayHi.defer(2000, this, ['Fred']);

// this syntax is sometimes useful for deferring
// execution of an anonymous function:
(function(){
    alert('Anonymous');
}).defer(100);


     * @param {Number} millis The number of milliseconds for the setTimeout call (if less than or equal to 0 the function is executed immediately)
     * @param {Object} scope (optional) The scope (this reference) in which the function is executed.
     * If omitted, defaults to the browser window.
     * @param {Array} args (optional) Overrides arguments for the call. (Defaults to the arguments passed by the caller)
     * @param {Boolean/Number} appendArgs (optional) if True args are appended to call args instead of overriding,
     * if a number the args are inserted at the specified position
     * @return {Number} The timeout id that can be used with clearTimeout
     */
Function.prototype.defer = function(millis, obj, args, appendArgs) {
    var fn = this.createDelegate(obj, args, appendArgs);
    if(millis > 0){
        return setTimeout(fn, millis);
    }
    fn();
    return 0;
}  
