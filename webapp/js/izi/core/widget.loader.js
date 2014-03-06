/**
 * izicms
 * 
 * LICENSE
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE Version 2 
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-2.0.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@izicms.com so we can send you a copy immediately.
 * 
 * @copyright	Copyright (c) 2009-2010 izicms Team (http://www.izicms.vn)
 * @license		http://www.gnu.org/licenses/gpl-2.0.txt GNU GENERAL PUBLIC LICENSE Version 2
 * @version 	$Id: widget.loader.js 5352 2010-09-09 03:21:56Z hoangpt $
 * @since		2.0.0
 */

'Izi.Core.Widget.Loader'.namespace();

/**
 * Izi.Core.Widget.Loader
 * This class required plugins:
 * - ajaxq
 * - jquery.json
 */
Izi.Core.Widget.Loader = function() {};

Izi.Core.Widget.Loader.baseUrl = '/';

/**
 * Put the widget loading to queue
 * 
 * @param string module Name of module
 * @param string name Name of widget
 * @param string Parameters names and values in JSON format
 * @param string containerId The DIV container that render the widget
 * @param string callback Function or name of callback function (since 2.0.8)
 * @return void
 */
Izi.Core.Widget.Loader.queue = function(module, name, data, containerId, callback) {
	Izi.Core.Widget.Loader.queueAction(module, name, 'show', data, containerId, callback);
};

/**
 * Put the widget action to queue
 * 
 * @param string module Name of module
 * @param string name Name of widget
 * @param string act Action's name
 * @param string Parameters names and values in JSON format
 * @param string containerId The DIV container that render the widget
 * @param string callback Function or name of callback function (since 2.0.8)
 * @return void
 */
Izi.Core.Widget.Loader.queueAction = function(module, name, act, data, containerId, callback) {
	$('#' + containerId).addClass('loading').html('');
	var baseUrl = Izi.Core.Widget.Loader.baseUrl;
	baseUrl = baseUrl.replace(/\/+$/, "");
	$.ajaxq('widget', {
		url: baseUrl + '/core/widget/ajax/',
		type: 'POST',
		data: { mod: module, name: name, act: act, params: data },
		success: function(response) {
			response = $.evalJSON(response);
			$('#' + containerId).removeClass('loading').html(response.content);
			
			for (var i in response.css) {
				if ($('head').find('link[href="' + response.css[i] + '"]').length == 0) {
					$('<link rel="stylesheet" type="text/css" href="' + response.css[i] + '" />').appendTo('head');
				}
			}
			
			for (i in response.javascript) {
				if (response.javascript[i].file != null && $('body').find('script[src="' + response.javascript[i].file + '"]').length == 0) {
					$('<script type="text/javascript" src="' + response.javascript[i].file + '"></script>').prependTo('body');
				}
				
				/**
				 * Add scripts to the end of page
				 * @since 2.0.6
				 */
				if (response.javascript[i].script != null) {
					$('<script type="text/javascript">' + response.javascript[i].script + '</script>').prependTo('body');
				}
			}
			
			/**
			 * Run the callback
			 * @since 2.0.8
			 */
			if (callback) {
				callback(response);
			}
		}
	});
};
