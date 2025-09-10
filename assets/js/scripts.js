/* scripts for the front */

document.addEventListener("DOMContentLoaded", function() {
	console.log("AEP loaded.");

	window.AEP_FunctionAfterLoad = function(arg) {
		//
	}


	// Example of localized data from PHP to JS
	const info_to_js = (aep_data && typeof aep_data == 'object' && aep_data.info_to_js) ? aep_data.info_to_js : '';
	console.log("Info to JS: " + info_to_js);


	// Example of ajax call
	const ajaxButton = document.getElementById("ajaxButton");
	const ajaxResult = document.getElementById("ajaxResult");
	if(ajaxButton && ajaxResult) {

		ajaxButton.addEventListener("click", function() {
			const query = "send value";

			const xhr = new XMLHttpRequest();
			xhr.open('POST', '/wp-admin/admin-ajax.php', true);
			xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
			xhr.responseType = "json";
			xhr.addEventListener('error', () => console.log("Erreur Ajax"));
			xhr.addEventListener('load', function() {

				concatResult = "";
				if(xhr.response.data){
					xhr.response.data.forEach((result) => {
						concatResult += "<li>"+result+"</li>";
					});
				}
				ajaxResult.innerHTML = "<ul>"+concatResult+"</ul>";

			});

			xhr.send("action=aep_ajax_action&query=" + encodeURIComponent(query));
		});
	}


	// Example of cookie read on page load
	console.log("Cookie aep_cookie_name: " + getCookie('aep_cookie_name'));
});

function AEP_FunctionBeforeLoad (arg) {
	//
}

// Cookie read function
function getCookie(name = null) {
	const cookies = {};
	document.cookie.split(";").forEach(cookie => {
		const [key, value] = cookie.split("=");
		if (key.trim() && value != undefined) {
			cookies[key.trim()] = value;
		}
	});

	if (name == null) {
		return cookies;
	}

	return cookies[name] || null;
}
