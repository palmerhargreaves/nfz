/**
 * String utils
 * 
 * @version $Id: string.js 1188 2010-07-23 15:25:17Z  $
 */
String.prototype.entityDecode = function() {
  return this.replace('&gt;', '>')
         .replace('&lt;', '<')
         .replace('&quot;', '"')
         .replace('&#039;', "'")
         .replace('&amp;', '&');
}
