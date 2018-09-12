/**
 * @author CatMan
 */

/**
 * Создает пространство имен или возвращает ссылку на него, если оно существует
 * <pre>
 * ns('property.package')
 * </pre>
 * Использование
 * <pre>
 * ns.property.package.func = ...
 * </pre>
 * Написан на основе YAHOO.namespace
 * 
 * @param {String*} name имена одного или нескольких пространств
 */
function ns() {
	var a = arguments, o = null, i, j, d;
	for(i = 0; i < a.length; i ++) {
		d = a[i].split('.');
		
		o = window;
		for(j = 0; j < d.length; j ++) {
			o[d[j]] = o[d[j]] || {};
			o = o[d[j]];
		}
	}
	return o;
}