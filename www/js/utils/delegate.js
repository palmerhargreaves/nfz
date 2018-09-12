/**
 * @class Function
 * These functions are available on every Function object (any JavaScript function).
 */
    /**
     * Creates a delegate (callback) that sets the scope to obj.
     * Call directly on any function. Example: <code>this.myFunction.createDelegate(this, [arg1, arg2])</code>
     * Will create a function that is automatically scoped to obj so that the <tt>this</tt> variable inside the
     * callback points to obj. Example usage:
     * <pre><code>
var sayHi = function(name){
    // Note this use of "this.text" here.  This function expects to
    // execute within a scope that contains a text property.  In this
    // example, the "this" variable is pointing to the btn object that
    // was passed in createDelegate below.
    alert('Hi, ' + name + '. You clicked the "' + this.text + '" button.');
}

var btn = new Ext.Button({
    text: 'Say Hi',
    renderTo: Ext.getBody()
});

// This callback will execute in the scope of the
// button instance. Clicking the button alerts 
// "Hi, Fred. You clicked the "Say Hi" button."
btn.on('click', sayHi.createDelegate(btn, ['Fred']));
</code></pre>
     * @param {Object} obj (optional) The object for which the scope is set
     * @param {Array} args (optional) Overrides arguments for the call. (Defaults to the arguments passed by the caller)
     * @param {Boolean/Number} appendArgs (optional) if True args are appended to call args instead of overriding,
     *                                             if a number the args are inserted at the specified position
     * @return {Function} The new function
     */
Function.prototype.createDelegate = function(obj, args, appendArgs) {
  var method = this;
  return function() {
      var callArgs = args || arguments;
      if(appendArgs === true){
          callArgs = Array.prototype.slice.call(arguments, 0);
          callArgs = callArgs.concat(args);
      }else if(typeof appendArgs == "number"){
          callArgs = Array.prototype.slice.call(arguments, 0); // copy arguments first
          var applyArgs = [appendArgs, 0].concat(args); // create method call params
          Array.prototype.splice.apply(callArgs, applyArgs); // splice them in
      }
      return method.apply(obj || window, callArgs);
  };
}