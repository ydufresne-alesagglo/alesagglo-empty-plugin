/* scripts for the front */

document.addEventListener("DOMContentLoaded", function() {
	console.log("AEP loaded.");

	window.AEP_FunctionAfterLoad = function(arg) {
		//
	}


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


});

function AEP_FunctionBeforeLoad (arg) {
	//
}