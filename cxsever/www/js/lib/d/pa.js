'use strict';

// const { Agent } = require('undici');

// function get_html_text(sourceTitle) {

// 	// const title = sourceTitle.replace(/ /g, "_")

// 	const url = "https://medwiki.toolforge.org/get_html/rest_v1_page.php?title=" + sourceTitle
// 	const options = {
// 		method: 'GET',
// 		// dispatcher: new Agent({ connect: { timeout: 60_000 } })
// 	};

// 	const response = fetch(url, options);

// 	const text = response.text();

// 	return text;
// };

function get_text_api_wmcloud(title, callback) {
	var url = 'https://medwiki.toolforge.org/get_html/rest_v1_page.php?wmcloud=1&title=' + title

	const options = {
		method: 'GET',
		dataType: 'json',
	};
	return fetch(url, options)
		.then(response => response.json())
		.then(data => data.text)
		.then(result => callback(result))
}

function get_text_api_new(title, callback) {
	var url = 'https://medwiki.toolforge.org/get_html/rest_v1_page.php?title=' + title

	const options = {
		method: 'GET',
		dataType: 'json',
	};
	return fetch(url, options)
		.then(response => response.json())
		.then(data => data.text)
		.then(result => callback(result))
}

module.exports = {
	// get_html_text,
	get_text_api_new,
	get_text_api_wmcloud
};
