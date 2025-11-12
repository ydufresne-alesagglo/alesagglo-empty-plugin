// scripts front ales agglo empty plugin

document.addEventListener("DOMContentLoaded", function () {

	// Example of ajax call
	const ajaxButton = document.getElementById("ajaxButton");
	const ajaxResult = document.getElementById("ajaxResult");
	if(ajaxButton && ajaxResult) {

		ajaxButton.addEventListener("click", function () {
			const query = "send value";

			const xhr = new XMLHttpRequest();
			xhr.open('POST', '/wp-admin/admin-ajax.php', true);
			xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
			xhr.responseType = "json";
			xhr.addEventListener('error', () => console.log('Erreur Ajax'));
			xhr.addEventListener('load', function () {

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


	// Example of localized data from PHP to JS Var
	const aep_jsvar_name = (aep_data_jsvar && typeof aep_data_jsvar == "object" && aep_data_jsvar.aep_jsvar_name) ? aep_data_jsvar.aep_jsvar_name : "";
	console.log("JS Var Name: " + aep_jsvar_name);


	// Example of cookie
	let aep_cookie_name = getCookie('aep_cookie_name');
	console.log('Cookie Name: ' + aep_cookie_name);
});


// Read cookie
function getCookie(name = null) {
	const cookies = {};
	document.cookie.split(";").forEach(cookie => {
		const [key, value] = cookie.split("=");
		if (key && value != undefined) {
			cookies[key.trim()] = decodeURIComponent(value.trim());
		}
	});

	if (name == null) {
		return cookies;
	}
	return cookies[name] || null;
}

// Write cookie
function setCookie(name, value="", hours=24) {
	const date = new Date();
	date.setTime(date.getTime() + (hours * 60 * 60 * 1000));
	const expires = "; expires=" + date.toUTCString();
	document.cookie = name + "=" + encodeURIComponent(value) + expires + "; path=/";
}

// Delete cookie
function unsetCookie(name) {
	document.cookie = name + "=; Max-Age=0; path=/";
}
