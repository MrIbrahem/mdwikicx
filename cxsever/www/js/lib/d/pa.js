'use strict';

// const { Agent } = require('undici');

var u = require('./u.js');

async function get_html_text(sourceTitle) {

	// const title = sourceTitle.replace(/ /g, "_")

	const url = "https://mdwiki.toolforge.org/cx/get_html.php?title=" + sourceTitle
	const options = {
		method: 'GET',
		dataType: 'json',
		// dispatcher: new Agent({ connect: { timeout: 60_000 } })
	};
	return await fetch(url, options)
		.then((response) => response.json())
		.then((response) => response.segmentedContent);
};

function get_page(title) {
	const source = get_html_text(title);
	if (!source || !source.length) {
		return "no source";
	}
	console.log(source);
	const result = u.tet(source);
	return result;
}

module.exports = {
	get_page,
	get_html_text
};
